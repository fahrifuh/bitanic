<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Garden;
use App\Models\HarvestProduce;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class HarvestProduceController extends Controller
{
    public function index($garden)
    {
        $user = auth()->user();

        $garden = Garden::find($garden);

        if (!$garden) {
            return response()->json([
                'messages' => (object) [
                    'text' => ["Data kebun tidak ditemukan"]
                ]
            ], 404);
        }

        if (!Gate::allows('update-garden', $garden)) {
            return response()->json([
                'messages' => (object) [
                    'warning' => ["Anda tidak dapat mengakses data ini"]
                ]
            ], 403);
        }

        $harvest_produces = HarvestProduce::query()
            ->with(['garden', 'crop'])
            ->where('garden_id', $garden->id)
            ->latest()
            ->paginate(10);

        return response()->json([
            'harvest_produces' => $harvest_produces,
            'status' => 200
        ], 200);
    }

    public function store(Request $request, $garden)
    {
        $user = auth()->user();

        $garden = Garden::find($garden);

        if (!$garden) {
            return response()->json([
                'messages' => (object) [
                    'text' => ["Data kebun tidak ditemukan"]
                ]
            ], 404);
        }

        if (!Gate::allows('update-garden', $garden)) {
            return response()->json([
                'messages' => (object) [
                    'warning' => ["Anda tidak dapat mengakses data ini"]
                ]
            ], 403);
        }

        $v = Validator::make($request->all(), [
            'hasil_panen' => 'required|numeric|min:0',
            'satuan_panen' => 'required|string|in:kuintal,kg,ton',
            'catatan' => 'nullable|string|max:1500'
        ]);

        if ($v->fails()) {
            return response()->json([
                'messages' => $v->errors()
            ], 400);
        }

        HarvestProduce::create([
            'crop_id' => $garden->crop_id,
            'garden_id' => $garden->id,
            'value' => $request->hasil_panen,
            'unit' => $request->satuan_panen,
            'date' => today('Asia/Jakarta'),
            'note' => $request->catatan
        ]);

        return response()->json([
            'message' => "Berhasil disimpan"
        ], 200);
    }

    public function destroy($garden, $id)
    {
        $user = auth()->user();

        $data = HarvestProduce::find($id);

        if (!$data) {
            return response()->json([
                'messages' => (object) [
                    'text' => ["Data hasil panen tidak ditemukan"]
                ]
            ], 404);
        }

        $garden = Garden::find($garden);

        if (!$garden) {
            return response()->json([
                'messages' => (object) [
                    'text' => ["Data kebun tidak ditemukan"]
                ]
            ], 404);
        }

        if (!Gate::allows('update-garden', $garden)) {
            return response()->json([
                'messages' => (object) [
                    'warning' => ["Anda tidak dapat mengakses data ini"]
                ]
            ], 403);
        }

        $data->delete();

        return response()->json([
            'message' => "Berhasil dihapus"
        ], 200);
    }
}
