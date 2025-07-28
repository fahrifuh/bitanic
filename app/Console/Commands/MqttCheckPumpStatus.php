<?php

namespace App\Console\Commands;

use App\Models\Device;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpMqtt\Client\Facades\MQTT;

class MqttCheckPumpStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mqtt:check-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check devices status active or not using mqtt';

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
        $mqtt->subscribe('bitanicv2/status', function (string $topic, string $message) {
            [$ID, $status] = explode('_', $message);

            $now =  now('Asia/Jakarta');

            $status = "Device series not recogniced;";

            try {
                if ($device = Device::select('id','status')->where('device_series', $ID)->first()) {
                    $device->status = 1;
                    $device->save();
                    
                    $status = $ID . " Device status active!";
                }
            } catch (\Throwable $th) {
                echo $th;
            }

            echo "[$now]: $status \n";
        }, 1);
        $mqtt->loop(true);
    }
}
