<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Models\Fertilization;
use App\Models\FertilizationSchedule;
use App\Models\Telemetri;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpMqtt\Client\Facades\MQTT;

class ReceiveMqtt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mqtt:receive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Receive MQTT message';

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
        $mqtt->subscribe('bitanicv2/pompa/manual', function (string $topic, string $message) {
            [$ID, $motor, $device_status] = explode('_', $message);

            $now =  now('Asia/Jakarta');

            $status = "Device series not recogniced; \n";

            try {
                if ($device = Device::with(['fertilization', 'finished_fertilization'])->where('device_series', $ID)->first()) {
                    switch ($motor) {
                        case 'MOTOR1':
                            $device->status_motor_1 = ($device_status == 'HIDUP') ? 1 : 0;
                            $device->save();
                            break;
                        case 'MOTOR2':
                            $device->status_motor_2 = ($device_status == 'HIDUP') ? 1 : 0;
                            $device->save();
                            break;
                    }

                    $status = "($device->device_series) Motor status updated ($device_status)";
                }
            } catch (\Throwable $th) {
                echo $th;
            }

            echo "[$now]: $status \n";
        }, 1);
        $mqtt->loop(true);
    }
}
