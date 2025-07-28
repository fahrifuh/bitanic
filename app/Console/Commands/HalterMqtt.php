<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpMqtt\Client\Facades\MQTT;

class HalterMqtt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'halter:mqtt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run mqtt subscribe for halter';

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
        Log::channel('halter')->info("Halter MQTT Start; \n");
        /** @var \PhpMqtt\Client\Contracts\MqttClient $mqtt */
        $mqtt = MQTT::connection();
        $mqtt->subscribe('halter/data', function (string $topic, string $message) {
            $now = now('Asia/Jakarta');

            try {
                [$no_device, $header] = explode(',', $message);

                if ($header == 'SHMKR100601') {
                    [$no_device, $header, $accx, $accy, $accz, $gyrox, $gyroy, $gyroz, $vbatt, $hr, $spo2, $suhu, $tail] = explode(',', $message);

                    DB::table('halter_logs')
                        ->insert([
                            'No_Device' => $no_device ?? null, 
                            'Header' => $header ?? null, 
                            'AccX' => (is_numeric($accx) ? (double) $accx : null), 
                            'AccY' => (is_numeric($accy) ? (double) $accy : null), 
                            'AccZ' => (is_numeric($accz) ? (double) $accz : null), 
                            'GyroX' => (is_numeric($gyrox) ? (double) $gyrox : null), 
                            'GyroY' => (is_numeric($gyroy) ? (double) $gyroy : null), 
                            'GyroZ' => (is_numeric($gyroz) ? (double) $gyroz : null), 
                            'Vbatt' => (is_numeric($vbatt) ? (double) $vbatt : null), 
                            'HR' => (is_numeric($hr) ? (int) $hr : null), 
                            'SPO2' => (is_numeric($spo2) ? (int) $spo2 : null), 
                            'Suhu' => (is_numeric($suhu) ? (double) $suhu : null), 
                            'Tail' => $tail,
                            'created_at' => $now
                        ]);
                } elseif ($header == 'DVC10232') {
                    [$no_device, $header, $temp, $humidity, $light, $gas] = explode(',', $message);

                    DB::table('cage_logs')
                        ->insert([
                            'no_device' => $no_device ?? null, 
                            'header' => $header ?? null, 
                            'temperature' => is_numeric($temp) ? (float) $temp : null,
                            'humidity' => is_numeric($humidity) ? (float) $humidity : null,
                            'light_intensity' => is_numeric($light) ? (float) $light : null,
                            'gas' => is_numeric($gas) ? (float) $gas : null,
                            'created_at' => $now
                        ]);
                }

                Log::channel('halter')->info($message);
            } catch (\Throwable $th) {
                //throw $th;
                Log::channel('halter')->error($th);
            }
        }, 1);
        $mqtt->loop(true);
    }
}
