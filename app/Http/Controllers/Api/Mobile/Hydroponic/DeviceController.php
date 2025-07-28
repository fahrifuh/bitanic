<?php

namespace App\Http\Controllers\Api\Mobile\Hydroponic;

use App\Http\Controllers\Controller;
use App\Models\HydroponicDevice;
use App\Models\HydroponicDeviceTelemetry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use PhpMqtt\Client\Facades\MQTT;

class DeviceController extends Controller
{
    public function index() : JsonResponse
    {
        $hydroponicDevices = HydroponicDevice::query()
            ->where('user_id', $this->hydroponicGuard()->id())
            ->whereNotNull('activation_date')
            ->oldest('series')
            ->get([
                'id',
                'series',
                'version',
                'picture',
                'activation_date',
                'production_date',
                'note',
            ]);

        return response()
            ->json($hydroponicDevices);
    }

    public function show(HydroponicDevice $hydroponicDevice) : JsonResponse
    {
        if (!Gate::allows('user-hydroponic-device', $hydroponicDevice)) {
            return response()
                ->json([
                    'message' => 'Anda tidak bisa mengakses data ini!',
                ], 403);
        }

        $hydroponicDevice->load('latestTelemetry');

        return response()
            ->json($hydroponicDevice);
    }

    public function activateDevice(Request $request) : JsonResponse
    {
        $request->validate([
            'series'    => 'required|string|max:255'
        ]);

        $user_id = $this->hydroponicGuard()->id();

        $hydroponicDevice = HydroponicDevice::query()
            ->where(function(Builder $query)use($user_id){
                $query->where('user_id', $user_id)
                    ->orWhereNull('user_id');
            })
            ->where('series', $request->series)
            ->firstOrFail();

        if ($hydroponicDevice->activation_date != null) {
            return response()
                ->json([
                    'message' => 'Perangkat sudah diaktifkan!'
                ], 422);
        }

        $now = now('Asia/Jakarta');

        $hydroponicDevice->user_id = $user_id;
        $hydroponicDevice->activation_date = $now;
        $hydroponicDevice->save();

        return response()
            ->json([
                'message' => 'Perangkat berhasil diaktivasi pada ' . $now->format('Y-m-d H:i:s')
            ]);
    }

    public function latestTelemetry(HydroponicDevice $hydroponicDevice) : JsonResponse
    {
        if (!Gate::allows('user-hydroponic-device', $hydroponicDevice)) {
            return response()
                ->json([
                    'message' => 'Anda tidak bisa mengakses data ini!',
                ], 403);
        }

        return response()
            ->json($hydroponicDevice->latestTelemetry);
    }

    public function latestTelemetries(HydroponicDevice $hydroponicDevice) : JsonResponse
    {
        if (!Gate::allows('user-hydroponic-device', $hydroponicDevice)) {
            return response()
                ->json([
                    'message' => 'Anda tidak bisa mengakses data ini!',
                ], 403);
        }

        $hydroponicDeviceTelemetry = HydroponicDeviceTelemetry::query()
            ->where('hydroponic_device_id', $hydroponicDevice->id)
            ->latest('created_at')
            ->paginate(10);

        return response()
            ->json($hydroponicDeviceTelemetry);
    }

    public function updateThreshold(Request $request, HydroponicDevice $hydroponicDevice) : JsonResponse
    {
        if (!Gate::allows('user-hydroponic-device', $hydroponicDevice)) {
            return response()
                ->json([
                    'message' => 'Anda tidak bisa mengakses data ini!',
                ], 403);
        }

        $request->validate([
            'crop_name' => 'required|string|max:255',
            'water.min' => 'required|numeric',
            'water.max' => 'required|numeric',
            'nutrient.min' => 'required|numeric',
            'nutrient.max' => 'required|numeric',
            'ph_basa' => 'required|numeric',
            'ph_asam' => 'required|numeric',
        ]);

        $thresholds = (object) [
            'crop_name' => $request->crop_name,
            "water" => [$request->water['min'], $request->water['max']],
            "nutrient" => [$request->nutrient['min'], $request->nutrient['max']],
            "ph_basa" => $request->ph_basa,
            "ph_asam" => $request->ph_asam,
        ];

        $hydroponicDevice->update([
            'thresholds' => $thresholds
        ]);

        MQTT::publish('bitanic/hydroponic/' . $hydroponicDevice->series, json_encode((object) [
            'thresholds' => $thresholds
        ]), false, 'private');

        return response()
            ->json([
                'message' => 'Berhasil disimpan!'
            ]);
    }

    public function updateAuto(Request $request, HydroponicDevice $hydroponicDevice) : JsonResponse
    {
        if (!Gate::allows('user-hydroponic-device', $hydroponicDevice)) {
            return response()
                ->json([
                    'message' => 'Anda tidak bisa mengakses data ini!',
                ], 403);
        }

        $request->validate([
            'status' => 'required|in:0,1',
        ]);

        $message = (object) [
            "auto" => $request->status
        ];

        MQTT::publish('bitanic/hydroponic/' . $hydroponicDevice->series, json_encode($message), false, 'private');

        return response()
            ->json([
                'message' => 'Berhasil disimpan!'
            ]);
    }

    public function updatePump(Request $request, HydroponicDevice $hydroponicDevice) : JsonResponse
    {
        if (!Gate::allows('user-hydroponic-device', $hydroponicDevice)) {
            return response()
                ->json([
                    'message' => 'Anda tidak bisa mengakses data ini!',
                ], 403);
        }

        $request->validate([
            'pump'      => 'required|string|in:water,nutrient,ph_basa,ph_asam,mixer',
            'status' => 'required|in:1,0',
        ]);

        $message = (object) [
            $request->pump => $request->status
        ];

        MQTT::publish('bitanic/hydroponic/' . $hydroponicDevice->series, json_encode($message), false, 'private');

        return response()
            ->json([
                'message' => 'Berhasil disimpan!'
            ]);
    }

    private function hydroponicGuard(): \Illuminate\Contracts\Auth\Guard | \Illuminate\Contracts\Auth\StatefulGuard
    {
        return auth()->guard('hydroponic');
    }
}
