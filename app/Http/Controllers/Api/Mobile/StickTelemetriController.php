<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\StikTelemetri;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StickTelemetriController extends Controller
{
    public function index(Request $request) : JsonResponse {
        $per_page = $request->query('per_page', 10);

        $stik_telemetries = StikTelemetri::query()
            ->where('user_id', auth()->id())
            ->where('type', 'npk')
            ->orderByDesc('created_at')
            ->paginate($per_page);

        return response()->json([
            "stick_telemetries" => $stik_telemetries,
            'status' => 200
        ]);
    }

    public function store(Request $request) : JsonResponse {
        if ($request->polygon && is_string($request->polygon)) {
            $request['polygon'] = json_decode($request->polygon);
        }

        $v = Validator::make($request->all(), [
            'moisture' => ['nullable', 'numeric'],
            'temperature' => ['nullable', 'numeric'],
            'rh' => ['nullable', 'numeric'],
            't' => ['nullable', 'numeric'],
            'id_perangkat' => ['nullable', 'string', 'max:250'],
            'id_pengukuran' => ['nullable', 'string', 'max:250'],
            'n' => ['required_if:type,npk', 'nullable', 'numeric'],
            'p' => ['required_if:type,npk', 'nullable', 'numeric'],
            'k' => ['required_if:type,npk', 'nullable', 'numeric'],
            'co2' => ['sometimes', 'required_if:type,npk', 'nullable', 'numeric'],
            'no2' => ['sometimes', 'required_if:type,npk', 'nullable', 'numeric'],
            'n2o' => ['sometimes', 'required_if:type,npk', 'nullable', 'numeric'],
            'area' => ['required_if:type,luas', 'numeric'],
            'type' => ['required', 'string', 'in:luas,npk'],
            'polygon' => ['required_if:type,luas', 'nullable', 'array'],
            'longitude' => 'required_if:type,npk|regex:/^(-?\d+(\.\d+)?)$/',
            'latitude' => 'required_if:type,npk|regex:/^(-?\d+(\.\d+)?)$/',
        ]);

        if ($v->fails()) {
            return response()->json([
                'messages' => $v->errors(),
                'status' => 400
            ], 400);
        }

        $query = ['type'];

        if ($request->type == 'npk') {
            $query = [...$query, ...[
                'rh',
                't',
                'n',
                'p',
                'k',
                'co2',
                'no2',
                'n2o',
                'longitude',
                'latitude',
                'id_perangkat',
                'id_pengukuran',
                'temperature',
                'moisture',
            ]];
        } elseif ($request->type == 'luas') {
            $query = [...$query, 'area'];
        }

        StikTelemetri::insert($request->only($query) + [
            'polygon' => $request->type == 'luas' ? json_encode($request->polygon) : null,
            'user_id' => auth()->id(),
            'created_at' => now()
        ]);

        return response()->json([
            'message' => 'Berhasil menyimpan data',
            'status' => 200
        ]);
    }

    public function show(StikTelemetri $stickTelemetri) : JsonResponse {
        return response()->json([
            "stick_telemetri" => $stickTelemetri,
            "status" => 200
        ]);
    }
}
