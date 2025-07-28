<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Commodity;
use App\Models\Crop;
use App\Models\Garden;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommodityController extends Controller
{
    public function index(Garden $garden) : JsonResponse {
        $farmer_id = auth()->user()->farmer->id;

        if (
            $farmer_id != $garden->land->farmer_id
        ) {
            return abort(403);
        }

        $garden->load('currentCommodity');

        if (!$garden->currentCommodity) {
            return response()->json([
                'errors' => [
                    'commodity' => ['Kebun tidak memiliki komoditi!']
                ]
            ], 422);
        }

        return response()->json([
            'commodity' => $garden->currentCommodity
        ]);
    }

    public function store(Request $request, Garden $garden) : JsonResponse {
        $farmer_id = auth()->user()->farmer->id;

        if (
            $farmer_id != $garden->land->farmer_id
        ) {
            return abort(403);
        }

        $garden->load('currentCommodity');

        if ($garden->currentCommodity) {
            return response()->json([
                'errors' => [
                    'commodity' => ['Kebun masih memiliki komoditi!']
                ]
            ], 422);
        }

        $validated = $request->validate([
            'crop_id' => 'required|integer|min:0',
            'total' => 'required|integer|min:0',
            'planting_dates' => 'required|date|date_format:Y-m-d',
        ]);

        $crop = Crop::query()
            ->find($validated['crop_id']);

        if (!$crop) {
            return response()->json([
                'errors' => [
                    'crop_id' => ['Tanaman tidak ditemukan']
                ]
            ], 422);
        }

        Commodity::create($validated + [
            'garden_id' => $garden->id,
            'estimated_harvest' => now()->parse($validated['planting_dates'])->addWeeks($crop->frekuensi_siram)
        ]);

        return response()->json([
            'message' => 'Berhasil disimpan'
        ]);
    }

    public function updateYield(Request $request, Garden $garden) : JsonResponse {
        $farmer_id = auth()->user()->farmer->id;

        if (
            $farmer_id != $garden->land->farmer_id
        ) {
            return abort(403);
        }

        $garden->load('currentCommodity');

        if (!$garden->currentCommodity) {
            return response()->json([
                'errors' => [
                    'commodity' => ['Kebun tidak memiliki komoditi!']
                ]
            ], 422);
        }

        if ($garden->harvest_status != 3) {
            return response()->json([
                'errors' => [
                    'garden' => ['Status kebun belum masa panen']
                ]
            ], 422);
        }

        $validated = $request->validate([
            'hasil_panen'   => 'required|numeric|min:0',
            'satuan_panen'  => 'required|string|in:kuintal,kg,ton',
            'catatan'       => 'nullable|string|max:1000',
        ]);

        $garden->currentCommodity->value = $validated['hasil_panen'];
        $garden->currentCommodity->unit = $validated['satuan_panen'];
        $garden->currentCommodity->note = $validated['catatan'];
        $garden->currentCommodity->harvested = now('Asia/Jakarta');
        $garden->currentCommodity->is_finished = 1;

        $garden->harvest_status = 0;

        $garden->push();

        return response()->json([
            'message' => 'Berhasil disimpan'
        ]);
    }

    public function destroy(Garden $garden) : JsonResponse {
        $farmer_id = auth()->user()->farmer->id;

        if (
            $farmer_id != $garden->land->farmer_id
        ) {
            return abort(403);
        }

        $garden->load('currentCommodity');

        if (!$garden->currentCommodity) {
            return response()->json([
                'errors' => [
                    'commodity' => ['Kebun tidak memiliki komoditi!']
                ]
            ], 422);
        }

        Commodity::query()
            ->where('id', $garden->currentCommodity->id)
            ->delete();

        return response()->json([
            'message' => 'Berhasil dihapus'
        ]);
    }

    public function indexFinished(Garden $garden) : JsonResponse {
        $farmer_id = auth()->user()->farmer->id;

        if (
            $farmer_id != $garden->land->farmer_id
        ) {
            return abort(403);
        }

        // $garden->load('finishedCommodities');

        return response()->json([
            'commodities' => $garden->finishedCommodities
        ]);
    }
}
