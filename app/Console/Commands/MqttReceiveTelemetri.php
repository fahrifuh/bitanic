<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Models\Telemetri;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpMqtt\Client\Facades\MQTT;

class MqttReceiveTelemetri extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mqtt:telemetri';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $mqtt->subscribe('bitanicv2/telemetri', function (string $topic, string $message) {
            $data = json_decode($message);
            
            $now = now('Asia/Jakarta');

            try {
                $device = Device::where('device_series', $data->ID)->first();
                if ($device) {
                    [$day, $month, $year] = explode('/', $data->date);
                    [$hour, $minute, $second] = explode(':', $data->time);

                    $datetime = Carbon::create($year, $month, $day, $hour, $minute, $second, 'Asia/Jakarta');
                    
                    // Insert telemetri
                    Telemetri::insert([
                        'farmer_id' => $device->farmer_id,
                        'device_id' => $device->id,
                        'garden_id' => $device->garden_id,
                        "temperature" => $data->temperature,
                        "humidity" => $data->humidity,
                        "heatIndex" => $data->heatIndex,
                        "soil1" => $data->soil1,
                        "soil2" => $data->soil2,
                        "datetime" => $datetime,
                        'created_at' => $now
                    ]);

                    if ($device->status != 1) {
                        $device->update(['status' => 1]);
                    }
                    
                    echo "[$now]: Telemetri Receive! \n\n";
                }

                if (!$device) {
                    echo "[$now]: Device series ($data->ID) not recogniced;  \n";
                } else if ($device && $device->status == 0) {
                    echo "[$now]: Device is not active! Please check the device ($device->device_series) \n";
                }
            } catch (\Throwable $th) {
                echo "[$now]: " . $th;
            }
        }, 1);
        $mqtt->loop(true);
    }
}
