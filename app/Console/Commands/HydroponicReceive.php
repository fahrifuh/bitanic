<?php

namespace App\Console\Commands;

use App\Models\HydroponicDevice;
use App\Models\HydroponicDeviceTelemetry;
use Heyharpreetsingh\FCM\Facades\FCMFacade;
use Illuminate\Console\Command;
use PhpMqtt\Client\Facades\MQTT;

class HydroponicReceive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mqtt:hydroponic-receive';

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
     * @return void
     */
    public function handle()
    {
        echo "mqtt\n";
        /** @var \PhpMqtt\Client\Contracts\MqttClient $mqtt */
        $mqtt = MQTT::connection('private');
        $mqtt->subscribe('bitanic/hydroponic/receive', function (string $topic, string $message) {
            $now = now('Asia/Jakarta');

            try {
                $data = json_decode($message);

                echo "[$now]: Data diterima\n";

                echo $data->ID;

                $hydroponicDevice = HydroponicDevice::query()
                    ->with('hydroponicUser:id,firebase_token')
                    ->where('series', $data->ID)
                    ->first();

                if ($hydroponicDevice) {
                    $sensors = (object) [
                        'temperature' => $data->sensors->suhu,
                        'humidity' => $data->sensors->kelembapan,
                        'tdm' => $data->sensors->tdm,
                        'ph' => $data->sensors->ph,
                        'water_volume' => $data->sensors->volume_air,
                    ];

                    $this->savePumps($hydroponicDevice, $data->pompa);

                    HydroponicDeviceTelemetry::insert([
                        'hydroponic_device_id' => $hydroponicDevice->id,
                        'sensors' => json_encode($sensors),
                        'created_at' => $now
                    ]);

                    event(new \App\Events\HydroponicDeviceEvent($hydroponicDevice->id, $hydroponicDevice->pumps, $sensors));
                }
            } catch (\Throwable $th) {
                echo "[$now]: " . $th;
            }
        }, 1);
        $mqtt->loop(true);
    }

    private function savePumps(HydroponicDevice $hydroponicDevice, object $pumps) : bool {
        $change = false;
        $message = "";

        if ($hydroponicDevice->pumps->water != $pumps->pompa_air) {
            $change = true;
            $message .= "Pompa Air " . $this->labelStatusPompa($pumps->pompa_air) . "\n";
        }
        if ($hydroponicDevice->pumps->nutrient != $pumps->pompa_nutrisi) {
            $change = true;
            $message .= "Pompa Nutrisi " . $this->labelStatusPompa($pumps->pompa_nutrisi) . "\n";
        }
        if ($hydroponicDevice->pumps->ph_basa != $pumps->ph_basa) {
            $change = true;
            $message .= "pH Basa " . $this->labelStatusPompa($pumps->ph_basa) . "\n";
        }
        if ($hydroponicDevice->pumps->ph_asam != $pumps->ph_asam) {
            $change = true;
            $message .= "pH Asam " . $this->labelStatusPompa($pumps->ph_asam) . "\n";
        }
        if ($hydroponicDevice->pumps->mixer != $pumps->mixer) {
            $change = true;
            $message .= "Mixer " . $this->labelStatusPompa($pumps->mixer) . "\n";
        }

        if ($change) {
            $hydroponicDevice->update([
                'pumps' => (object) [
                    "water" => $pumps->pompa_air,
                    "nutrient" => $pumps->pompa_nutrisi,
                    "ph_basa" => $pumps->ph_basa,
                    "ph_asam" => $pumps->ph_asam,
                    "mixer" => $pumps->mixer,
                ]
            ]);

            if ($hydroponicDevice->hydroponicUser->firebase_token) {
                FCMFacade::send([
                    "message" => [
                        "token" => $hydroponicDevice->hydroponicUser->firebase_token,
                        "notification" => [
                            "body" => $message .
                                now()->format('H:i:s Y-m-d'),
                            "title" => "Aktifitas Pompa pada perangkat " . $hydroponicDevice->series
                        ]
                    ]
                ]);
            }
        }

        return true;
    }

    private function labelStatusPompa($status) : string {
        return $status ? 'Hidup' : "Mati";
    }
}
