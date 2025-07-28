<?php

namespace App\Console\Commands;

use App\Models\LiteDevice;
use Carbon\Carbon;
use Illuminate\Console\Command;
use PhpMqtt\Client\Facades\MQTT;

class MqttLiteDevice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mqtt-lite-device:serve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listen to mqtt for lite topic';

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
     * @return void
     */
    public function handle()
    {
        echo "Mqtt start; \n";
        /** @var \PhpMqtt\Client\Contracts\MqttClient $mqtt */
        $mqtt = MQTT::connection();
        $mqtt->subscribe('bitanic/lite', function (string $topic, string $message) {
            $data = json_decode($message);

            $now = now('Asia/Jakarta');

            try {
                if ($data->ID) {
                    $lite_device = LiteDevice::query()
                        ->with('pumps')
                        ->where('full_series', $data->ID)
                        ->first();
                    if ($lite_device) {
                        [$day, $month, $year] = explode('/', $now->copy()->format('d/m/Y'));
                        [$hour, $minute] = explode(':', $now->copy()->format('H:i'));
                        if (
                            $data->statusPerangkat &&
                            $data->statusPerangkat->dateTime
                        ) {
                            if ($data->statusPerangkat->dateTime->date && count(explode('/', $data->statusPerangkat->dateTime->date)) == 3) {
                                [$day, $month, $year] = explode('/', $data->statusPerangkat->dateTime->date);
                            }
                            if (
                                $data->statusPerangkat->dateTime->time &&
                                (count(explode(':', $data->statusPerangkat->dateTime->time)) == 3 ||
                                count(explode(':', $data->statusPerangkat->dateTime->time)) == 2)
                            ) {
                                [$hour, $minute] = explode(':', $data->statusPerangkat->dateTime->time);
                            }
                        }

                        $datetime = Carbon::create($year, $month, $day, $hour, $minute, $now->second, 'Asia/Jakarta');

                        foreach ($data->statusPerangkat->input as $input) {
                            switch ($input->nama) {
                                case 'sensorPH':
                                    $lite_device->current_ph = $input->value;
                                    break;
                                case 'sensorTDS':
                                    $lite_device->current_tds = $input->value;
                                    break;
                                case 'sensorSuhu':
                                    $lite_device->temperature = $input->value;
                                    break;
                                case 'sensorKelembapan':
                                    $lite_device->humidity = $input->value;
                                    break;
                                case 'sensorSuhuAir':
                                    $lite_device->water_temperature = $input->value;
                                    break;
                            }
                        }

                        $output = collect($data->statusPerangkat->output);
                        $pumps_status = [];

                        foreach ($lite_device->pumps as $pump) {
                            $a = $output->firstWhere('nama', "pompa" . $pump->number);
                            if($a){
                                $pump->status = ($a->active == true) ? 1 : 0;
                            }
                            $pumps_status[] = (object) [
                                'id' => $pump->id,
                                'number' => $pump->number,
                                'status' => $pump->status,
                            ];
                        }

                        $lite_device->last_updated_telemetri = $datetime;
                        $lite_device->status = 1;

                        $lite_device->push();

                        event(new \App\Events\LiteEvent((object) [
                            'id' => $lite_device->id,
                            'full_series' => $lite_device->full_series,
                            'ph' => $lite_device->current_ph,
                            'tds' => $lite_device->current_tds,
                            'temperature' => $lite_device->temperature,
                            'humidity' => $lite_device->humidity,
                            'water_temperature' => $lite_device->water_temperature,
                            'pumps_status' => $pumps_status,
                            'last_updated_telemetri' => $lite_device->last_updated_telemetri,
                        ]));

                        echo "[$now]: Message Receive! \n\n";
                    }
                }
            } catch (\Throwable $th) {
                echo "[$now]: " . $th;
            }
        }, 1);
        $mqtt->loop(true);
    }
}
