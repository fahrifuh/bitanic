<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\Farmer;
use App\Models\StikTelemetri;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StickTelemetriController extends Controller
{
    public function index(Farmer $farmer) : View {
        $stik_telemetries = StikTelemetri::query()
            ->select([
                'id',
                'id_perangkat',
                'id_pengukuran',
                'user_id',
                'temperature',
                'moisture',
                'area',
                'n',
                'p',
                'k',
                'co2',
                'no2',
                'n2o',
                'created_at'
            ])
            ->where('user_id', $farmer->user_id)
            ->where('type', 'npk')
            ->orderByDesc('created_at')
            ->paginate(10);

        $farmer->load(['user:id,name']);

        return view('bitanic.stick-telemetri.index', compact('stik_telemetries', 'farmer'));
    }

    public function getArea(Farmer $farmer) : JsonResponse {
        $stick_telemetries = collect([]);
        StikTelemetri::query()
            ->select(['id', 'polygon', 'area', 'latitude', 'longitude', 'n', 'p', 'k', 'type', 'created_at'])
            ->where('user_id', $farmer->user_id)
            ->where('type', 'npk')
            ->orderByDesc('created_at')
            ->chunk(100, function($telemetries) use(&$stick_telemetries){
                $stick_telemetries = [...$stick_telemetries, ...$telemetries];
            });

        return response()->json($stick_telemetries);
    }

    public function getTelemetri(Farmer $farmer, StikTelemetri $stikTelemetri) : JsonResponse {
        return response()->json([
            'type' => $stikTelemetri->type,
            'polygon' => $stikTelemetri->type == 'luas' ? $stikTelemetri->polygon : null,
            'data' => $stikTelemetri->type == 'npk' ? $stikTelemetri->only([
                'id',
                'latitude',
                'longitude',
                'n',
                'p',
                'k',
                'co2',
                'no2',
                'n2o',
                'id_perangkat',
                'id_pengukuran',
                'temperature',
                'moisture',
                'area',
            ]) : $stikTelemetri->only(['area'])
        ]);
    }

    public function destroy(Farmer $farmer, StikTelemetri $stickTelemetri) : JsonResponse {
        $stickTelemetri->delete();

        return response()->json([
            'message' => 'Berhasil dihapus'
        ]);
    }
}
