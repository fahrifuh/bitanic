<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Models\Fertilization;
use App\Models\FertilizationSchedule;
use App\Models\Telemetri;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpMqtt\Client\Facades\MQTT;

class MqttMotorSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mqtt:motor-schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Receive data from schedule';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        echo "Mqtt start; \n";
        /** @var \PhpMqtt\Client\Contracts\MqttClient $mqtt */
        $mqtt = MQTT::connection();
        $mqtt->subscribe('bitanic', function (string $topic, string $message) {
            try {
                $data = json_decode($message);

                $skip = false;

                $now = now('Asia/Jakarta');

                $series = isset($data->ID) ? $data->ID : null;

                $status = "Device series not recogniced $series";

                $device = null;

                if ($data) {
                    $device = Device::with(['finished_fertilization'])->where('device_series', $data->ID)->first();
                }

                if ($device && $device->category == 'controller' && $device->type == 1) {
                    $status = "Status check: ($data->ID) Device is sended data!";
                    if ($device->block_data == 0) { // check if data is need to be block or not
                        // Update status device to online
                        if ($device->status == 0) {
                            $device->status = 1; // online
                            $device->check_status = 0;
                            $device->save();
                        }

                        if (isset($data->STATUS_AKTIF)) {
                            $skip = true;
                        }

                        if (isset($data->TELEMETRI)) {
                            $telemetri = $data->TELEMETRI;
                            [$day, $month, $year] = explode('/', $telemetri->date);
                            [$hour, $minute, $second] = explode(':', $telemetri->time);

                            $datetime = Carbon::create($year, $month, $day, $hour, $minute, $second, 'Asia/Jakarta');

                            // Insert telemetri
                            Telemetri::insert([
                                'farmer_id' => $device->farmer_id,
                                'device_id' => $device->id,
                                'garden_id' => $device->garden_id,
                                "temperature" => $telemetri->temperature,
                                "humidity" => $telemetri->humidity,
                                "heatIndex" => $telemetri->heatIndex,
                                "soil1" => $telemetri->soil1,
                                "soil2" => $telemetri->soil2,
                                "datetime" => $datetime,
                                'created_at' => $now
                            ]);

                            $status = "Telemetri Receive!";
                            $skip = true;
                        }

                        if (isset($data->MOTOR)) {
                            $device->load(['fertilization', 'fertilization.schedule' => function ($query) use ($device) {
                                $query->where([
                                        ['device_id', $device->id],
                                        ['garden_id', $device->garden_id],
                                        ['farmer_id', $device->farmer_id]
                                    ])
                                    ->whereNotNull('start_time')
                                    ->whereNull('end_time')
                                    ->orderBy('id', 'desc');
                            }, 'fertilization_schedule' => function ($query) use ($device) {
                                $query->where([
                                        ['device_id', $device->id],
                                        ['garden_id', $device->garden_id],
                                        ['farmer_id', $device->farmer_id]
                                    ])
                                    ->whereNotNull('start_time')
                                    ->whereNull('end_time')
                                    ->orderBy('id', 'desc');
                            }]);

                            $insertManual = [];

                            if ($device->status_motor_1 != $data->MOTOR->STATUS_MOTOR1) {
                                if ($data->MOTOR->STATUS_MOTOR1 == 1) {
                                    $insertManual[] = [
                                        'device_id' => $device->id,
                                        'farmer_id' => $device->farmer_id,
                                        'garden_id' => $device->garden_id,
                                        'week' => $device?->fertilization?->created_at?->floatDiffInWeeks($now->startOfWeek()) ?? 0,
                                        'day' => (int) $now->format('w'),
                                        'type' => 'manual_motor_1',
                                        'fertilization_id' => $device?->fertilization->id ?? null,
                                        'start_time' => $now,
                                        'created_at' => $now
                                    ];
                                } else {
                                    if ($device?->fertilization?->schedule->where('type', 'manual_motor_1')->count() != 0) {
                                        $device->fertilization->schedule->where('type', 'manual_motor_1')->first()->end_time = $now;

                                        $device->push();
                                    } elseif ($device->fertilization_schedule->where('type', 'manual_motor_1')->count() != 0) {
                                        $device->fertilization_schedule->where('type', 'manual_motor_1')->first()->end_time = $now;

                                        $device->push();
                                    }
                                }

                                $device->status_motor_1 = $data->MOTOR->STATUS_MOTOR1;
                                $device->save();
                                $status = "($device->device_series) Motor status updated";
                            }

                            if ($device->status_motor_2 != $data->MOTOR->STATUS_MOTOR2) {
                                if ($data->MOTOR->STATUS_MOTOR2 == 1) {
                                    $insertManual[] = [
                                        'device_id' => $device->id,
                                        'farmer_id' => $device->farmer_id,
                                        'garden_id' => $device->garden_id,
                                        'week' => $device?->fertilization?->created_at?->floatDiffInWeeks($now->startOfWeek()) ?? 0,
                                        'day' => (int) $now->format('w'),
                                        'type' => 'manual_motor_2',
                                        'fertilization_id' => $device?->fertilization->id ?? null,
                                        'start_time' => $now,
                                        'created_at' => $now
                                    ];
                                } else {
                                    if ($device?->fertilization?->schedule->where('type', 'manual_motor_2')->count() != 0) {
                                        $device->fertilization->schedule->where('type', 'manual_motor_2')->first()->end_time = $now;

                                        $device->push();
                                    } elseif ($device->fertilization_schedule->where('type', 'manual_motor_2')->count() != 0) {
                                        $device->fertilization_schedule->where('type', 'manual_motor_2')->first()->end_time = $now;

                                        $device->push();
                                    }
                                }

                                $device->status_motor_2 = $data->MOTOR->STATUS_MOTOR2;
                                $device->save();
                                $status = "($device->device_series) Motor status updated";
                            }

                            if (count($insertManual) != 0) {
                                FertilizationSchedule::insert($insertManual);
                            }

                            event(new \App\Events\PumpEvent($device->id, $device->status_motor_1, $device->status_motor_2, $device->irrigation, $device->vertigation));

                            $skip = true;
                        }

                        if (!$skip) {
                            $device->load(['fertilization', 'fertilization.schedule' => function ($query) use ($device) {
                                $query->where([
                                    ['device_id', $device->id],
                                    ['garden_id', $device->garden_id],
                                    ['farmer_id', $device->farmer_id]
                                ])
                                    ->orderBy('id', 'desc');
                            }, 'fertilization_schedule' => function ($query) use ($device) {
                                $query->where([
                                    ['device_id', $device->id],
                                    ['garden_id', $device->garden_id],
                                    ['farmer_id', $device->farmer_id]
                                ])
                                    ->whereNotNull('start_time')
                                    ->whereNull('end_time')
                                    ->orderBy('id', 'desc');
                            }]);

                            $eventSkip = true;

                            if (isset($data->STATUS_MOTOR1)) {
                                if ($device->status_motor_1 != $data->STATUS_MOTOR1) {
                                    $device->status_motor_1 = $data->STATUS_MOTOR1;
                                    $device->save();
                                    $eventSkip = false;
                                }
                            }

                            if (isset($data->STATUS_MOTOR2)) {
                                if ($device->status_motor_2 != $data->STATUS_MOTOR2) {
                                    $device->status_motor_2 = $data->STATUS_MOTOR2;
                                    $device->save();
                                    $eventSkip = false;
                                }
                            }

                            $irrigations = $device->irrigation;

                            if (isset($data->STATUS_PE1_MOTOR1)) {
                                if ($irrigations && isset($irrigations[0]) && $irrigations[0]['status'] != $data->STATUS_PE1_MOTOR1) {
                                    $irrigations[0]['status'] = $data->STATUS_PE1_MOTOR1;
                                    $device->irrigation = $irrigations;
                                    $device->save();
                                    $eventSkip = false;
                                }
                            }
                            if (isset($data->STATUS_PE2_MOTOR1)) {
                                if ($irrigations && isset($irrigations[1]) && $irrigations[1]['status'] != $data->STATUS_PE2_MOTOR1) {
                                    $irrigations[1]['status'] = $data->STATUS_PE2_MOTOR1;
                                    $device->irrigation = $irrigations;
                                    $device->save();
                                    $eventSkip = false;
                                }
                            }
                            if (isset($data->STATUS_PE3_MOTOR1)) {
                                if ($irrigations && isset($irrigations[2]) && $irrigations[2]['status'] != $data->STATUS_PE3_MOTOR1) {
                                    $irrigations[2]['status'] = $data->STATUS_PE3_MOTOR1;
                                    $device->irrigation = $irrigations;
                                    $device->save();
                                    $eventSkip = false;
                                }
                            }
                            if (isset($data->STATUS_PE4_MOTOR1)) {
                                if ($irrigations && isset($irrigations[3]) && $irrigations[3]['status'] != $data->STATUS_PE4_MOTOR1) {
                                    $irrigations[3]['status'] = $data->STATUS_PE4_MOTOR1;
                                    $device->irrigation = $irrigations;
                                    $device->save();
                                    $eventSkip = false;
                                }
                            }
                            if (isset($data->STATUS_PE5_MOTOR1)) {
                                if ($irrigations && isset($irrigations[4]) && $irrigations[4]['status'] != $data->STATUS_PE5_MOTOR1) {
                                    $irrigations[4]['status'] = $data->STATUS_PE5_MOTOR1;
                                    $device->irrigation = $irrigations;
                                    $device->save();
                                    $eventSkip = false;
                                }
                            }
                            if (isset($data->STATUS_PE6_MOTOR1)) {
                                if ($irrigations && isset($irrigations[5]) && $irrigations[5]['status'] != $data->STATUS_PE6_MOTOR1) {
                                    $irrigations[5]['status'] = $data->STATUS_PE6_MOTOR1;
                                    $device->irrigation = $irrigations;
                                    $device->save();
                                    $eventSkip = false;
                                }
                            }

                            $vertigations = $device->vertigation;

                            if (isset($data->STATUS_PE1_MOTOR2)) {
                                if ($vertigations && isset($vertigations[0]) && $vertigations[0]['status'] != $data->STATUS_PE1_MOTOR2) {
                                    $vertigations[0]['status'] = $data->STATUS_PE1_MOTOR2;
                                    $device->vertigation = $vertigations;
                                    $device->save();
                                    $eventSkip = false;
                                }
                            }
                            if (isset($data->STATUS_PE2_MOTOR2)) {
                                if ($vertigations && isset($vertigations[1]) && $vertigations[1]['status'] != $data->STATUS_PE2_MOTOR2) {
                                    $vertigations[1]['status'] = $data->STATUS_PE2_MOTOR2;
                                    $device->vertigation = $vertigations;
                                    $device->save();
                                    $eventSkip = false;
                                }
                            }
                            if (isset($data->STATUS_PE3_MOTOR2)) {
                                if ($vertigations && isset($vertigations[2]) && $vertigations[2]['status'] != $data->STATUS_PE3_MOTOR2) {
                                    $vertigations[2]['status'] = $data->STATUS_PE3_MOTOR2;
                                    $device->vertigation = $vertigations;
                                    $device->save();
                                    $eventSkip = false;
                                }
                            }
                            if (isset($data->STATUS_PE4_MOTOR2)) {
                                if ($vertigations && isset($vertigations[3]) && $vertigations[3]['status'] != $data->STATUS_PE4_MOTOR2) {
                                    $vertigations[3]['status'] = $data->STATUS_PE4_MOTOR2;
                                    $device->vertigation = $vertigations;
                                    $device->save();
                                    $eventSkip = false;
                                }
                            }
                            if (isset($data->STATUS_PE5_MOTOR2)) {
                                if ($vertigations && isset($vertigations[4]) && $vertigations[4]['status'] != $data->STATUS_PE5_MOTOR2) {
                                    $vertigations[4]['status'] = $data->STATUS_PE5_MOTOR2;
                                    $device->vertigation = $vertigations;
                                    $device->save();
                                    $eventSkip = false;
                                }
                            }
                            if (isset($data->STATUS_PE6_MOTOR2)) {
                                if ($vertigations && isset($vertigations[5]) && $vertigations[5]['status'] != $data->STATUS_PE6_MOTOR2) {
                                    $vertigations[5]['status'] = $data->STATUS_PE6_MOTOR2;
                                    $device->vertigation = $vertigations;
                                    $device->save();
                                    $eventSkip = false;
                                }
                            }

                            if (!$eventSkip) {
                                event(new \App\Events\PumpEvent($device->id, $device->status_motor_1, $device->status_motor_2, $device->irrigation, $device->vertigation));
                            }

                            $manual = false;
                            $schedule_motor_1 = null;
                            $schedule_motor_2 = null;

                            if ($device->fertilization) {
                                $days = [0, 1, 2, 3, 4, 5, 6];
                                $today = today()->format('w');
                                $isScheduleTime = false;

                                $detHari = str_split($data->DET_HARI);

                                if ($detHari[$today] == 1) { // check if day is schedule
                                    [$setTime1, $menit1] = explode(" ", $data->ONTIME1);

                                    $scheduleToday = $device->fertilization->schedule->filter(function ($value, $key) {
                                        return $value->type == 'schedule';
                                    });

                                    if (count($scheduleToday->all()) == 0) {
                                        $isScheduleTime = true;
                                    }
                                }

                                if (!$isScheduleTime) {
                                    $schedule_motor_1 = FertilizationSchedule::query()
                                        ->where([
                                            ['fertilization_id', $device->fertilization->id],
                                            ['device_id', $device->id],
                                            ['week', (int) $data->CURRENTPEKAN],
                                            ['day', ((int) today()->parse($data->DATE)->format('w'))]
                                        ])
                                        ->whereNotNull('start_time')
                                        ->whereNull('end_time')
                                        ->where('type', 'manual_motor_1')
                                        ->latest()
                                        ->first();

                                    $schedule_motor_2 = FertilizationSchedule::query()
                                        ->where([
                                            ['fertilization_id', $device->fertilization->id],
                                            ['device_id', $device->id],
                                            ['week', (int) $data->CURRENTPEKAN],
                                            ['day', ((int) today()->parse($data->DATE)->format('w'))]
                                        ])
                                        ->whereNotNull('start_time')
                                        ->whereNull('end_time')
                                        ->where('type', 'manual_motor_2')
                                        ->latest()
                                        ->first();

                                    echo "manual true";

                                    $manual = true;
                                }
                            } elseif ($data->TOTALPEKAN == "END") {
                                $schedule_motor_1 = FertilizationSchedule::query()
                                    ->where([
                                        ['device_id', $device->id],
                                        ['week', (int) $data->CURRENTPEKAN],
                                        ['day', ((int) today()->parse($data->DATE)->format('w'))]
                                    ])
                                    ->whereNotNull('start_time')
                                    ->whereNull('end_time')
                                    ->where('type', 'manual_motor_1')
                                    ->latest()
                                    ->first();
                                $schedule_motor_2 = FertilizationSchedule::query()
                                    ->where([
                                        ['device_id', $device->id],
                                        ['week', (int) $data->CURRENTPEKAN],
                                        ['day', ((int) today()->parse($data->DATE)->format('w'))]
                                    ])
                                    ->whereNotNull('start_time')
                                    ->whereNull('end_time')
                                    ->where('type', 'manual_motor_2')
                                    ->latest()
                                    ->first();

                                $manual = true;
                            }

                            if ($schedule_motor_1 && isset($data->STATUS_MOTOR1) && $data->STATUS_MOTOR1 == 0 && $manual == true) {
                                $schedule_motor_1->end_time = today()->parse($data->DATE . " " . $data->TIME);
                                // $schedule_motor_1->DHT1Temp = $data->DHT1Temp;
                                // $schedule_motor_1->DHT2Temp = $data->DHT2Temp;
                                // $schedule_motor_1->DHT1Hum = $data->DHT1Hum;
                                // $schedule_motor_1->DHT2Hum = $data->DHT2Hum;
                                $schedule_motor_1->save();

                                $status = "($data->ID) Motor 1 stopped;";
                            } elseif (!$schedule_motor_1 && isset($data->STATUS_MOTOR1) && $data->STATUS_MOTOR1 == 1 && $manual == true) {
                                FertilizationSchedule::insert([
                                    'fertilization_id' => optional($device->fertilization)->id ?? null,
                                    'device_id' => $device->id,
                                    'farmer_id' => $device->farmer_id,
                                    'garden_id' => $device->garden_id,
                                    'week' => (int) $data->CURRENTPEKAN,
                                    'day' => ((int) today()->parse($data->DATE)->format('w')),
                                    'type' => 'manual_motor_1',
                                    'start_time' => today()->parse($data->DATE . " " . $data->TIME),
                                    'created_at' => $now
                                ]);

                                $status = "($data->ID) Motor 1 start & data saved;";
                            }

                            if ($schedule_motor_2 && isset($data->STATUS_MOTOR2) && $data->STATUS_MOTOR2 == 0 && $manual == true) {
                                $schedule_motor_2->end_time = today()->parse($data->DATE . " " . $data->TIME);
                                // $schedule_motor_2->DHT1Temp = $data->DHT1Temp;
                                // $schedule_motor_2->DHT2Temp = $data->DHT2Temp;
                                // $schedule_motor_2->DHT1Hum = $data->DHT1Hum;
                                // $schedule_motor_2->DHT2Hum = $data->DHT2Hum;
                                $schedule_motor_2->save();

                                $status = "($data->ID) Motor 2 stopped;";
                            } elseif (!$schedule_motor_2 && isset($data->STATUS_MOTOR2) && $data->STATUS_MOTOR2 == 1 && $manual == true) {
                                FertilizationSchedule::insert([
                                    'fertilization_id' => optional($device->fertilization)->id ?? null,
                                    'device_id' => $device->id,
                                    'farmer_id' => $device->farmer_id,
                                    'garden_id' => $device->garden_id,
                                    'week' => (int) $data->CURRENTPEKAN,
                                    'day' => ((int) today()->parse($data->DATE)->format('w')),
                                    'type' => 'manual_motor_2',
                                    'start_time' => today()->parse($data->DATE . " " . $data->TIME),
                                    'created_at' => $now
                                    // 'DHT1Temp' => $data->DHT1Temp,
                                    // 'DHT2Temp' => $data->DHT2Temp,
                                    // 'DHT1Hum' => $data->DHT1Hum,
                                    // 'DHT2Hum' => $data->DHT2Hum
                                ]);

                                $status = "($data->ID) Motor 2 start & data saved;";
                            }

                            if ($manual == false && $device->fertilization && $data->TOTALPEKAN != "END" && (int) $data->CURRENTPEKAN <= $device->fertilization->weeks) {
                                $days = [0, 1, 2, 3, 4, 5, 6];

                                $setDay = [];

                                $history = str_split($data->HISTORY);

                                foreach ($days as $day) {
                                    if ($history[$day] == 1) {
                                        $setDay[] = $day;
                                    }
                                }

                                $last_data = FertilizationSchedule::where([
                                    ['fertilization_id', $device->fertilization->id],
                                    ['week', (int) $data->CURRENTPEKAN],
                                    ['type', 'schedule']
                                ])
                                    ->whereIn('day', $setDay)
                                    ->get(['day'])->toArray();

                                if (count($last_data) > 0) {
                                    $last_data_collect = collect($last_data);
                                    $map = $last_data_collect->flatten();

                                    $collect = collect($setDay);
                                    $diff = $collect->diff($map->all());
                                    $setDay = $diff->all();
                                }

                                foreach ($setDay as $day) {
                                    FertilizationSchedule::create([
                                        'fertilization_id' => $device->fertilization->id,
                                        'device_id' => $device->id,
                                        'farmer_id' => $device->farmer_id,
                                        'garden_id' => $device->garden_id,
                                        'week' => (int) $data->CURRENTPEKAN,
                                        'day' => $day,
                                        'type' => 'schedule'
                                    ]);

                                    $status = "($data->ID) Data schedule saved;";

                                    if ($day === end($setDay)) {
                                        // Last item
                                        if ((int) $data->CURRENTPEKAN == $device->fertilization->weeks) {
                                            $device->fertilization->is_finished = 1;

                                            $device->push();

                                            $status = "($data->ID) fertilization done;";
                                        }
                                    }
                                }
                            }
                        }

                    }
                }
                echo "[$now]: $status \n";
            } catch (\Throwable $th) {
                echo $th;
                echo "[$now]: $status \n";
            }

        }, 1);
        $mqtt->loop(true);
    }
}
