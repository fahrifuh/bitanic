<?php

namespace App\Http\Controllers\Api\Mobile\Lite;

use App\Http\Controllers\Controller;
use App\Models\LiteDevice;
use App\Models\LiteDevicePump;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PumpController extends Controller
{
    public function updateName(Request $request, LiteDevice $lite_device, LiteDevicePump $lite_device_pump) : JsonResponse {
        if ($lite_device->id != $lite_device_pump->lite_device_id) {
            abort(403, 'Data pompa tidak sama dengan yang ada di perangkat');
        }

        $request->validate([
            'name' => 'nullable|string|max:255'
        ]);

        $lite_device_pump->update($request->only('name'));

        return response()->json([
            'message' => 'Berhasil disimpan'
        ], 200);
    }
}
