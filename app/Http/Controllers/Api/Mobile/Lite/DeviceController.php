<?php

namespace App\Http\Controllers\Api\Mobile\Lite;

use App\Http\Controllers\Controller;
use App\Models\LiteDevice;
use App\Models\LiteDevicePump;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use PhpMqtt\Client\Facades\MQTT;

class DeviceController extends Controller
{
    public function deviceActivation(Request $request) : JsonResponse {
        $request->validate([
            'device_series' => 'required|string'
        ]);

        $lite_device = LiteDevice::query()
            ->where('full_series', $request->device_series)
            ->firstOrFail();

        $user = auth()->guard('lite')->user();

        if (
            $lite_device->activate_date
        ) {
            return response()->json([
                'message' => 'Perangkat sudah diaktivasi pada tanggal ' . $lite_device->activate_date
            ]);
        }

        abort_if(($lite_device->lite_user_id && $lite_device->lite_user_id != $user->id), 403, 'Perangkat Bukan milik anda!');

        if (!$lite_device->lite_user_id) {
            $lite_device->lite_user()->associate($user);
        }

        $lite_device->activate_date = now('Asia/Jakarta');
        $lite_device->save();

        return response()->json([
            'message' => 'Perangkat berhasil diaktivasi!',
            'device' => $lite_device
        ]);
    }

    public function show(LiteDevice $lite_device) : JsonResponse {
        if (!Gate::allows('lite-device-mobile', $lite_device)) {
            return response()->json([
                'messages' => (object) [
                    'warning' => ["Anda tidak dapat mengakses data ini"]
                ]
            ], 403);
        }

        $lite_device->load(['pumps', 'schedule']);

        return response()->json([
            'message' => 'Detail Perangkat Lite',
            'device' => $lite_device
        ]);
    }

    public function activatedDevice() : JsonResponse {
        $user = auth()->guard('lite')->user();

        $lite_devices = LiteDevice::query()
            ->where('lite_user_id', $user->id)
            ->whereNotNull('activate_date')
            ->get(['id', 'lite_user_id', 'full_series', 'image', 'status', 'temperature', 'humidity']);

        return response()->json([
            'message' => 'list data perangkat lite sudah diaktivasi',
            'devices' => $lite_devices
        ]);
    }

    public function updateDevicePump(Request $request, LiteDevice $lite_device, LiteDevicePump $lite_device_pump) : JsonResponse {
        if (!Gate::allows('lite-device-mobile', $lite_device)) {
            return response()->json([
                'messages' => (object) [
                    'warning' => ["Anda tidak dapat mengakses data ini"]
                ]
            ], 403);
        }

        if ($lite_device->id != $lite_device_pump->lite_device_id) {
            abort(403, 'Data pompa tidak sama dengan yang ada di perangkat');
        }

        $request->validate([
            'min_tds' => 'required|numeric|regex:/^-?\d+(\.\d+)?$/',
            'max_tds' => 'required|numeric|regex:/^-?\d+(\.\d+)?$/|gt:min_tds',
            'min_ph' => 'required|numeric|regex:/^-?\d+(\.\d+)?$/',
            'max_ph' => 'required|numeric|regex:/^-?\d+(\.\d+)?$/|gt:min_ph',
            'is_active' => 'required|integer|in:0,1',
        ]);

        $lite_device_pump->update($request->only([
            'min_tds',
            'max_tds',
            'min_ph',
            'max_ph',
            'is_active',
        ]));

        return response()->json([
            'message' => 'Berhasil disimpan'
        ]);
    }

    public function settingDevice(Request $request, LiteDevice $lite_device) : JsonResponse {
        $request->validate([
            'settings' => 'required|json',
            'settings.*.pumps' => 'required|json',
            'settings.*.min_tds' => 'required|numeric|regex:/^-?\d+(\.\d+)?$/',
            'settings.*.max_tds' => 'required|numeric|regex:/^-?\d+(\.\d+)?$/|gt:min_tds',
            'settings.*.min_ph' => 'required|numeric|regex:/^-?\d+(\.\d+)?$/',
            'settings.*.max_ph' => 'required|numeric|regex:/^-?\d+(\.\d+)?$/|gt:min_ph',
        ]);

        $settings = json_decode($request->settings, true);

        // return response()->json($settings);

        $pumps = $settings[0]['pumps'];

        $v = Validator::make([
            'pumps' => $pumps
        ],[
            'pumps.*.id' => 'required|integer|distinct',
            'pumps.*.active' => 'required|boolean',
        ]);

        if ($v->fails()) {
            return response()->json([
                'messages' => $v->errors(),
                'status' => 400
            ], 400);
        }

        $lite_device->load('pumps');

        $lite_device->update([
            'min_tds' => $settings[0]['min_tds'],
            'max_tds' => $settings[0]['max_tds'],
            'min_ph' => $settings[0]['min_ph'],
            'max_ph' => $settings[0]['max_ph'],
        ]);

        $output = collect($pumps)->map(function($item, $key){
            return (object) [
                "nama" => "pompa" . $item['id'],
                "active;" => $item['active'],
            ];
        })->all();

        $message = (object) [
            "type" => "lite",
            "mode" => "auto",
            "jadwal" => null,
            "setting" => (object) [
                "sensor_threshold" => [
                    (object) [
                        "nama" => "ph",
                        "min" => $lite_device->min_ph,
                        "max" => $lite_device->max_ph,
                    ],
                    (object) [
                        "nama" => "tds",
                        "min" => $lite_device->min_tds,
                        "max" => $lite_device->max_tds,
                    ],
                ],
                "output" => $output
            ]
        ];

        MQTT::publish("bitanic/" . $lite_device->full_series, json_encode($message), false, config('app.mqtt'));

        return response()->json([
            'message' => 'Berhasil disimpan'
        ]);
    }

    public function manualDevice(Request $request, LiteDevice $lite_device) : JsonResponse {
        $lite_device->load('pumps');

        $ids = collect($lite_device->pumps)->map(fn($item, $key) => $item->number)->join(',');

        $request->validate([
            'pumps' => 'required|json'
        ]);

        $pumps = json_decode($request->pumps, true);

        $v = Validator::make([
            'pumps' => $pumps
        ],[
            'pumps' => 'required|array|size:' . count($lite_device->pumps),
            'pumps.*.id' => 'required|integer|distinct|in:' . $ids,
            'pumps.*.active' => 'required|boolean',
        ]);

        if ($v->fails()) {
            return response()->json([
                'messages' => $v->errors(),
                'status' => 400
            ], 400);
        }

        $output = [];
        foreach ($pumps as $pump) {
            $output[] = (object) [
                "nama" => "pompa" . $pump['id'],
                "active" => $pump['active']
            ];
        }

        $message = (object) [
            "type" => "lite",
            "mode" => "manual",
            "setting" => (object) [
                "output" => $output
            ]
        ];

        MQTT::publish("bitanic/" . $lite_device->full_series, json_encode($message), false, config('app.mqtt'));

        return response()->json([
            'message' => 'Pesan berhasil dikirim'
        ]);
    }

    private function parseIntToBool(int $status) : bool {
        return $status == 0 ? false : true;
    }

    public function updateDeviceMode(Request $request, LiteDevice $liteDevice) : JsonResponse {
        $request->validate([
            'mode' => 'required|string|in:jadwal,manual,auto'
        ]);

        $liteDevice->mode = $request->mode;
        $liteDevice->save();

        $message = (object) [
            "type" => "lite",
            "mode" => "config",
            "setting" => (object) [
                "modeAlat" => $request->mode
            ]
        ];

        MQTT::publish("bitanic/" . $liteDevice->full_series, json_encode($message), false, config('app.mqtt'));

        return response()->json([
            'message' => 'Setting berhasil dikirim. Harap cek alat kembali'
        ], 200);
    }
}
