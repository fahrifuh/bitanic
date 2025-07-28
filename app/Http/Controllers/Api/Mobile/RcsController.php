<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Land;
use App\Models\RscTelemetri;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;

class RcsController extends Controller
{
    public function store(Request $request) : JsonResponse {
        $request->validate([
            'device_id' => 'required|string|max:255',
            'land_id' => 'required|integer|min:0',
            'samples' => ['required', 'json'],
        ]);

        $device = Device::query()
            ->where('farmer_id', auth()->user()->farmer->id)
            ->where('device_series', $request->device_id)
            ->firstOrFail();

        $samples = json_decode($request->samples, true);

        $v = Validator::make([
            'samples' => $samples
        ], [
            'samples' => 'required|array|min:1',
            'samples.*.latitude' => 'required|regex:/^(-?\d+(\.\d+)?)$/',
            'samples.*.longitude' => 'required|regex:/^(-?\d+(\.\d+)?)$/',
            'samples.*.moisture' => ['required', 'nullable', 'numeric'],
            'samples.*.temperature' => ['required', 'nullable', 'numeric'],
            'samples.*.n' => ['required', 'numeric'],
            'samples.*.p' => ['required', 'numeric'],
            'samples.*.k' => ['required', 'numeric'],
            'samples.*.soil_temperature' => ['required', 'numeric'],
            'samples.*.soil_moisture' => ['required', 'numeric'],
        ]);

        if ($v->fails()) {
            return response()->json([
                'messages' => $v->errors(),
                'status' => 400
            ], 400);
        }

        $land = Land::query()
            ->with('rsc_telemetri')
            ->where('farmer_id', auth()->user()->farmer->id)
            ->findOrFail($request->land_id);

        if ($land->rsc_telemetries) {
            $land->rsc_telemetries()->delete();
        }

        $now = now();

        $insert_telemetries = collect($samples)->map(function($sample, $key)use($device, $request, $now){
            return [
                'device_id' => $device->id,
                'land_id' => $request->land_id,
                'samples' => json_encode((object) [
                    "latitude" => $sample["latitude"],
                    "longitude" => $sample["longitude"],
                    "moisture" => $sample["moisture"],
                    "temperature" => $sample["temperature"],
                    "n" => $sample["n"],
                    "p" => $sample["p"],
                    "k" => $sample["k"],
                    "soil_temperature" => $sample["soil_temperature"],
                    "soil_moisture" => $sample["soil_moisture"],
                    "ec" => $sample["ec"],
                ]),
                'created_at' => $now
            ];
        })
        ->all();

        RscTelemetri::insert($insert_telemetries);

        return response()->json([
            'message' => 'Berhasil disimpan',
            'status' => 200
        ]);
    }

    public function checkDevice(Request $request) : JsonResponse {
        $validated = $request->validate([
            'device_id' => 'required|string|max:255'
        ]);

        $device = Device::query()
            ->where('farmer_id', auth()->user()->farmer->id)
            ->where('category', 'tongkat')
            ->where('device_series', $request->device_id)
            ->first();

        if (!$device) {
            return response()->json([
                'message' => 'Perangkat tongkat tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Perangkat ditemukan',
            'device' => $device
        ]);
    }
}
