<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Garden;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use PhpMqtt\Client\Facades\MQTT;

class DeviceController extends Controller
{
    public function listPerangkat()
    {
        $user = auth()->user();

        $data['data'] = Device::with(['specification'])->where('farmer_id', $user->farmer->id)->get();
        $data['message'] = "List data perangkat anda miliki/aktifasi";
        $data['status'] = 200;

        return response()->json($data, 200);
    }

    public function perangkatAktifasi()
    {
        $user = auth()->user();

        $data['data'] = Device::where('farmer_id', $user->farmer->id)->whereNotNull('activate_date')->get();
        $data['message'] = "List data perangkat anda yang diaktivasi!";
        $data['status'] = 200;

        return response()->json($data, 200);
    }

    public function perangkatTidakDipakai()
    {
        $user = auth()->user();

        $data['data'] = Device::where('farmer_id', $user->farmer->id)->whereNull('garden_id')->whereNotNull('activate_date')->get();
        $data['message'] = "List data perangkat anda yang tidak dipakai di kebun!";
        $data['status'] = 200;

        return response()->json($data, 200);
    }

    public function detailPerangkat($sn)
    {
        $perangkat = Device::with([
                'specification',
                'fertilization',
                'selenoids:id,device_id,land_id,selenoid_id,selenoid_status,selenoid_watering',
                'selenoids.land:id,name',
            ])
            ->where('device_series', $sn)
            ->first();

        if ($perangkat) {
            $data['data'] = $perangkat;
            $data['status'] = 200;
            $data['message'] = "Detail data perangkat";
            return response()->json($data, 200);
        }

        return response()->json([
            'message' => "Perangkat ".$sn." tidak ditemukan!",
            'status' => 404
        ], 404);
    }

    public function aktifasiPerangkat(Request $request)
    {
        $v = Validator::make($request->all(), [
            'seri_perangkat' => 'required'
        ]);

        if ($v->fails()) {
            return response()->json([
                'message' => $v->errors(),
                'status' => 400
            ], 400);
        }

        $sn = $request->seri_perangkat;

        $perangkat = Device::where('device_series', $sn)->first();

        if ($perangkat->farmer_id != null && $perangkat->farmer_id != auth()->user()->farmer->id) {
            return response()->json([
                'message' => "Perangkat ini bukan milik anda!",
                'status' => 200
            ], 200);
        }

        if ($perangkat->activate_date != null) {
            return response()->json([
                'message' => "Perangkat ini sudah diaktifkan!",
                'status' => 200
            ], 200);
        }

        $perangkat->activate_date = now('Asia/Jakarta');
        $perangkat->farmer_id = auth()->user()->farmer->id;
        $perangkat->save();

        return response()->json([
            'message' => "Perangkat berhasil diaktifkan!",
            'status' => 200
        ], 200);
    }


    public function checkStatus(Request $request, Garden $garden, Device $device)
    {
        if (!$device) {
            return response()->json(
                [
                    'messages' => (object) [
                        'text' => ['Data perangkat tidak ditemukan'],
                    ],
                ],
                404,
            );
        }

        if (! Gate::allows('update-device', $device)) {
            return response()->json([
                'messages' => (object) [
                    'warning' => ["Anda tidak dapat mengakses data ini"]
                ]
            ], 403);
        }

        $v = Validator::make($request->all(), [
            'status' => 'required|integer|in:0,1',
        ], [
            'status.in' => "Status harus berupa 0 (tidak aktif) atau 1 (aktif)"
        ]);

        if ($v->fails()) {
            return response()->json(
                [
                    'messages' => $v->errors(),
                ],
                400,
            );
        }

        if ($device->farmer_id == null) {
            return response()->json(
                [
                    'messages' => (object) [
                        'texts' => ['Perangkat tidak dimiliki oleh petani manapun!']
                    ],
                ],
                400,
            );
        }

        $message = "";

        switch ($request->status) {
            case 0: // change status device to inactive
                $device->status = $request->status;
                $device->save();

                $message = "Status diubah menjadi tidak aktif";
                break;
            case 1: // change status device to active
                if ($request->query('debug') == 1) { // debug to check if status changed without waiting for mqtt
                    $device->status = $request->status;
                }

                $device->check_status = 1;
                $device->save();

                // send mqtt to device
                MQTT::publish('bitanic/' . $device->device_series, $device->device_series . ',0,*');
                $message = "Request dikirim";
                break;
        }

        MQTT::publish('bitanic/' . $device->device_series, 'GETDATA,*');

        return response()->json([
            'message' => $message
        ], 200);
    }

    public function resetDevice(Device $device)
    {
        if (!Gate::allows('update-device', $device)) {
            return response()->json([
                'messages' => (object) [
                    'warning' => ["Anda tidak dapat mengakses data ini"]
                ]
            ], 403);
        }

        MQTT::publish('bitanic/' . $device->device_series, 'RESETALL,*');

        return response()->json([
            'message' => "mqtt message sended. Check your device!"
        ]);
    }

    public function updateManualSelenoid(Request $request, $serial_number) {
        $perangkat = Device::query()
            ->with('selenoids:id,device_id,selenoid_id,selenoid_status')
            ->where('farmer_id', auth()->user()->farmer->id)
            ->where('type', 3)
            ->where('device_series', $serial_number)
            ->firstOrFail();

        $selenoid = collect($perangkat->selenoids)->map(fn($selenoid, $key) => $selenoid->selenoid_id)->join(',');

        $request->validate([
            'type' => 'required|in:penyiraman,pemupukan',
            'lahan' => 'required|json',
        ]);

        $lands = json_decode($request->lahan, true);

        $v = Validator::make([
            'lahan' => $lands
        ], [
            'lahan' => 'required|array|size:' . count($perangkat->selenoids),
            'lahan.*.id' => 'required|integer|distinct|in:' . $selenoid,
            'lahan.*.status' => 'required|string|in:on,off',
        ]);

        if ($v->fails()) {
            return response()->json([
                'messages' => $v->errors()
            ]);
        }

        $type = $request->type;
        $topic = 'bitanic/'.$perangkat->device_series;

        $message = [
            "mode" => "manual",
            "tipe" => $type, // or penyiraman
        ];

        foreach ($lands as $land) {
            $message = array_merge($message, ["lahan" . $land['id'] => $land['status']]);
        }

        for ($i=count($lands) + 1; $i <= 4; $i++) {
            $message = array_merge($message, ["lahan$i" => "off"]);
        }

        MQTT::publish($topic, json_encode($message), false, config('app.mqtt'));

        return response()->json([
            'message' => "Mengirim status pada alat",
            "status" => 200
        ], 200);
    }
}
