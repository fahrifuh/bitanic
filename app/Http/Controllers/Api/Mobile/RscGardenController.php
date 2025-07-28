<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Garden;
use App\Models\RscGarden;
use App\Models\RscGardenTelemetry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RscGardenController extends Controller
{
    public function store(Request $request) : JsonResponse {
        $validated = $request->validate([
            'device_id' => 'required|string|max:255',
            'garden_id' => 'required|integer|min:0',
            'samples' => 'required|array|min:1|max:15',
            'samples.*.latitude' => 'required|regex:/^(-?\d+(\.\d+)?)$/',
            'samples.*.longitude' => 'required|regex:/^(-?\d+(\.\d+)?)$/',
            'samples.*.ambient_humidity' => ['required', 'numeric'],
            'samples.*.ambient_temperature' => ['required', 'numeric'],
            'samples.*.n' => ['required', 'numeric'],
            'samples.*.p' => ['required', 'numeric'],
            'samples.*.k' => ['required', 'numeric'],
            'samples.*.ec' => ['required', 'numeric'],
            'samples.*.ph' => ['required', 'numeric'],
            'samples.*.soil_temperature' => ['required', 'numeric'],
            'samples.*.soil_moisture' => ['required', 'numeric'],
        ]);

        $device = Device::query()
            ->where('farmer_id', auth()->user()->farmer->id)
            ->where('device_series', $validated['device_id'])
            ->firstOrFail();

        $garden = Garden::query()
            ->whereHas('land', function($query){
                $query->where('farmer_id', auth()->user()->farmer->id);
            })
            ->findOrFail($validated['garden_id']);

        $now = now();

        $rsc_garden = RscGarden::create([
            'device_id' => $device->id,
            'garden_id' => $garden->id,
        ]);

        $insert_telemetries = collect($validated['samples'])
            ->map(function($sample, $key)use($now, $rsc_garden){
                return [
                    'rsc_garden_id' => $rsc_garden->id,
                    "latitude" => $sample["latitude"],
                    "longitude" => $sample["longitude"],
                    'samples' => json_encode((object) [
                        "ambient_humidity" => $sample["ambient_humidity"],
                        "ambient_temperature" => $sample["ambient_temperature"],
                        "n" => $sample["n"],
                        "p" => $sample["p"],
                        "k" => $sample["k"],
                        "soil_temperature" => $sample["soil_temperature"],
                        "soil_moisture" => $sample["soil_moisture"],
                        "ec" => $sample["ec"],
                        "ph" => $sample["ph"],
                    ]),
                    'created_at' => $now
                ];
            })
            ->all();

        RscGardenTelemetry::insert($insert_telemetries);

        return response()->json([
            'message' => 'Berhasil disimpan',
            'status' => 200
        ]);
    }

    public function telemetries(RscGarden $rscGarden) : JsonResponse {
        $rscGarden->load([
            'garden:id,name,land_id',
            'garden.land:id,farmer_id',
            'device:id,device_series',
        ]);

        if (!Gate::allows('update-garden', $rscGarden->garden)) {
            return response()->json([
                'errors' => (object) [
                    'authorize' => ["Anda tidak dapat mengakses data ini"]
                ]
            ], 403);
        }

        $rscGarden
            ->loadAvg('rscGardenTelemetries as avg_n', 'samples->n')
            ->loadAvg('rscGardenTelemetries as avg_p', 'samples->p')
            ->loadAvg('rscGardenTelemetries as avg_k', 'samples->k');

        $rscGardenTelemetries = RscGardenTelemetry::query()
            ->where('rsc_garden_id', $rscGarden->id)
            ->latest('created_at')
            ->paginate(15);

        return response()
            ->json([
                'rsc_garden' => $rscGarden,
                'rsc_garden_telemetries' => $rscGardenTelemetries,
            ]);
    }
}
