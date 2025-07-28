<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Models\LiteDevice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckLastData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telemetri:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check last telemetri data from devices (type 2) to check if devices still active.';

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
        $devices = Device::query()
            ->with('last_data_telemetri:id,device_id,datetime')
            ->where('status', 1)
            ->get(['id','device_series', 'updated_at']);

        $deviceOff = [];
        $now = now('Asia/Jakarta');

        foreach ($devices as $device) {
            if ($device->last_data_telemetri && $now->diffInMinutes($now->copy()->parse($device->last_data_telemetri->datetime)) >= 10) {
                $deviceOff[] = $device->id;
            } elseif ($now->diffInMinutes($now->copy()->parse($device->updated_at)) >= 10) {
                $deviceOff[] = $device->id;
            }
        }

        $lite_devices = LiteDevice::query()
            ->where('status', 1)
            ->get(['id','full_series', 'last_updated_telemetri']);
        $lite_devices_off = [];

        foreach ($lite_devices as $lite_device) {
            if ($lite_device->last_updated_telemetri && $now->diffInMinutes($now->copy()->parse($lite_device->last_updated_telemetri)) >= 10) {
                $lite_devices_off[] = $lite_device->id;
            }
        }

        $countDevice = count($deviceOff);

        DB::table('devices')->whereIn('id', $deviceOff)->update(['status' => 0]);
        DB::table('lite_devices')->whereIn('id', $lite_devices_off)->update(['status' => 0]);

        $this->comment("Done $countDevice");

        return 0;
    }
}
