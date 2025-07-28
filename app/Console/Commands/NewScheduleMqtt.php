<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Models\FertilizationSchedule;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpMqtt\Client\Facades\MQTT;

class NewScheduleMqtt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mqtt:schedule-listen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listening to mqtt message from devices';

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
        echo "mqtt\n";
        /** @var \PhpMqtt\Client\Contracts\MqttClient $mqtt */
        $mqtt = MQTT::connection('private');
        $mqtt->subscribe('bitanic', function (string $topic, string $message) {
            $now = now('Asia/Jakarta');
            echo "[$now]: Data diterima\n";
            // echo "[$now]: $message\n";
            $data = json_decode($message);

            $status = "Seri Perangkat tidak dikenal! \n";

            $device = null;

            if ($data) {
                $device = Device::with(['fertilization', 'selenoids:id,device_id,selenoid_id,selenoid_status'])->where('device_series', $data->ID)->first();
            }

            if ($device) {
                try {
                    if ($device->category == 'controller' && $device->type == 3) {
                        if (isset($data->statusPerangkat)) {
                            if ($device->status == 0) {
                                $device->update(['status' => 1]);
                            }

                            $output = $data->statusPerangkat->output;
                            $pompaUtama = $this->changeStrToIntStatus($output->pompaUtama);
                            $valveAir = $this->changeStrToIntStatus($output->valveAir);
                            $valvePemupukan = $this->changeStrToIntStatus($output->valvePemupukan);
                            $valveLahan1 = $this->changeStrToIntStatus($output->valveLahan1);
                            $valveLahan2 = $this->changeStrToIntStatus($output->valveLahan2);
                            $valveLahan3 = $this->changeStrToIntStatus($output->valveLahan3);
                            $valveLahan4 = $this->changeStrToIntStatus($output->valveLahan4);
                            $selenoid = collect();
                            $is_status_change = false;
                            $is_jadwal_pemupukan_on = false;

                            // if ($data->statusJadwal) {
                            //     $fertilizations_status = collect($data->statusJadwal->pemupukan->setontimes)
                            //         ->first(function ($value, $key) {
                            //             return $value->status->kondisi == "on";
                            //         });

                            //     $is_jadwal_pemupukan_on = $fertilizations_status ? true : false;
                            // }

                            if($device->status_motor_1 != $pompaUtama){
                                $device->status_motor_1 = $pompaUtama;
                                $is_status_change = true;
                            }
                            if($device->status_penyiraman != $valveAir && !$is_jadwal_pemupukan_on){
                                $device->status_penyiraman = $valveAir;
                                $is_status_change = true;
                            }
                            // if ($is_jadwal_pemupukan_on && $valveAir == 1) {
                            //     $device->status_pemupukan = 1;
                            //     $is_status_change = true;
                            // } elseif ($device->status_pemupukan != $valvePemupukan) {
                            //     $device->status_pemupukan = $valvePemupukan;
                            //     $is_status_change = true;
                            // }
                            if ($device->status_pemupukan != $valvePemupukan) {
                                $device->status_pemupukan = $valvePemupukan;
                                $is_status_change = true;
                            }
                            if($device->selenoids && isset($device->selenoids[0]) && $device->selenoids[0]->selenoid_status != $valveLahan1){
                                $device->selenoids[0]->selenoid_status = $valveLahan1;
                                $is_status_change = true;
                                $selenoid->push((object) [
                                    'id' => $device->selenoids[0]->selenoid_id,
                                    'status' => $valveLahan1,
                                ]);
                            }
                            if($device->selenoids && isset($device->selenoids[1]) && $device->selenoids[1]->selenoid_status != $valveLahan2){
                                $device->selenoids[1]->selenoid_status = $valveLahan2;
                                $is_status_change = true;
                                $selenoid->push((object) [
                                    'id' => $device->selenoids[1]->selenoid_id,
                                    'status' => $valveLahan2,
                                ]);
                            }
                            if($device->selenoids && isset($device->selenoids[2]) && $device->selenoids[2]->selenoid_status != $valveLahan3){
                                $device->selenoids[2]->selenoid_status = $valveLahan3;
                                $is_status_change = true;
                                $selenoid->push((object) [
                                    'id' => $device->selenoids[2]->selenoid_id,
                                    'status' => $valveLahan3,
                                ]);
                            }
                            if($device->selenoids && isset($device->selenoids[3]) && $device->selenoids[3]->selenoid_status != $valveLahan4){
                                $device->selenoids[3]->selenoid_status = $valveLahan4;
                                $is_status_change = true;
                                $selenoid->push((object) [
                                    'id' => $device->selenoids[3]->selenoid_id,
                                    'status' => $valveLahan4,
                                ]);
                            }

                            $device->push();

                            if ($is_status_change == true) {
                                $deviceDate = Carbon::createFromFormat('d/m/Y H:i', $data->statusPerangkat->dateTime->date . " " . $data->statusPerangkat->dateTime->time);
                                FertilizationSchedule::insert([
                                    'fertilization_id' => $device?->fertilization?->id ?? null,
                                    'device_id' => optional($device->fertilization)->id ? null : $device->id,
                                    'farmer_id' => $device->farmer_id,
                                    'week' => $device->fertilization ? ($data->statusJadwal?->currentPekan ?? 0) : 0,
                                    'day' => ((int) $deviceDate->copy()->format('w')),
                                    'type' => 'manual_motor_1',
                                    'start_time' => $deviceDate,
                                    'extras' => json_encode((object) [
                                        "air" => $device->status_penyiraman,
                                        "pemupukan" => $device->status_pemupukan,
                                        "lahan" => $selenoid
                                    ]),
                                    'motor_status' => $pompaUtama,
                                    'created_at' => $now
                                ]);

                                event(new \App\Events\DeviceEvent($device->id, $selenoid, $pompaUtama, 0, $device->status_penyiraman, $device->status_pemupukan));

                                echo "[$now]: Data alat disimpan\n";
                            }

                            if ($device->fertilization && $device->fertilization->end_datetime && $now->copy()->gt(now('Asia/Jakarta')->parse($device->fertilization->end_datetime))) {
                                $device->fertilization->is_finished = 1;
                                $device->push();
                            }

                            if (isset($data->statusPerangkat?->input?->DHTLahan1?->temperature) || isset($data->statusPerangkat?->input?->DHTLahan1?->humidity)) {
                                DB::table('telemetri')->insert([
                                    'perangkat_id' => $device->id,
                                    'temperature' => $data->statusPerangkat?->input?->DHTLahan1?->temperature ?? null,
                                    'humidity' => $data->statusPerangkat?->input?->DHTLahan1?->humidity ?? null,
                                    'heat_index' => null,
                                    'created_at' => $now
                                ]);
                            }
                        }

                    }
                } catch (\Throwable $th) {
                    echo $th;
                }

                $status = "Data berhasil diterima! \n";
            }

            echo "[$now]: $status\n";
        }, 1);
        $mqtt->loop(true);
    }

    private function changeStrToIntStatus(string $status = "off") : int {
        switch ($status) {
            case "on":
                return 1;
                break;
            case "off":
            default:
                return 0;
                break;
        }
    }
}
