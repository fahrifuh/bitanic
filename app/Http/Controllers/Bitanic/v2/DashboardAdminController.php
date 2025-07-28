<?php

namespace App\Http\Controllers\Bitanic\v2;

use App\Http\Controllers\Controller;
use App\Models\Land;
use App\Models\RscGarden;
use App\Models\RscGardenTelemetry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardAdminController extends Controller
{
    public function getLands(): JsonResponse {
        $lands = Land::query()
            ->with('use_garden:id,land_id,harvest_status')
            ->has('use_garden')
            ->get(['id', 'name', 'polygon']);

        return response()->json($lands);
    }

    public function countFertilizer(?int $year): JsonResponse
    {
        $farmer_id = optional(auth()->user()->farmer)->id ?? null;

        $rsc_gardens = RscGarden::query()
            ->selectRaw('MAX(id) as id') // Get the last created_at within each hour
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month_group') // Extract hour from timestamp considering the interval
            ->groupBy('month_group')
            ->orderBy('month_group')
            ->pluck('id');

        $rsc_garden_telemetries = RscGardenTelemetry::query()
            ->selectRaw("AVG(JSON_EXTRACT(samples, '$.n')) as avg_n, AVG(JSON_EXTRACT(samples, '$.p')) as avg_p, AVG(JSON_EXTRACT(samples, '$.k')) as avg_k")
            ->whereIn('rsc_garden_id', $rsc_gardens)
            ->first();

        return response()->json([
            'rsc_garden_telemetries' => $rsc_garden_telemetries
        ]);
    }
}
