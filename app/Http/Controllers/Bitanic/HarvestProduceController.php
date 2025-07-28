<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\Garden;
use App\Models\HarvestProduce;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class HarvestProduceController extends Controller
{
    public function index($farmer, $garden)
    {
        $garden = Garden::with('land:id,name')->find($garden);

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

        $data = [];

        $data['harvest_produces'] = HarvestProduce::query()
            ->with(['garden', 'crop'])
            ->where('garden_id', $garden->id)
            ->paginate(10);

        $data['farmer'] = $farmer;
        $data['garden'] = $garden->id;
        $data['garden_name'] = $garden->land->name;

        return view('bitanic.harvest-produce.index', $data);
    }

    public function store(Request $request, $farmer, $garden)
    {
        $garden = Garden::query()
            ->with('active_garden')
            ->find($garden);

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

        if ($garden->harvest_status != 3) {
            return response()->json([
                'messages' => (object) [
                    'warnings' => ["Status kebun harus sedang panen!"]
                ]
            ], 403);
        }

        HarvestProduce::create([
            'crop_id' => $garden->crop_id,
            'garden_id' => $garden->id,
            'value' => $request->hasil_panen,
            'unit' => $request->satuan_panen,
            'date' => today('Asia/Jakarta'),
            'note' => $request->catatan
        ]);

        $garden->harvest_status = 0;
        $garden->save();

        if ($garden->active_garden) {
            $garden->active_garden->finished_date = today('Asia/Jakarta');
            $garden->push();
        }

        return response()->json([
            'message' => "Berhasil disimpan"
        ], 200);
    }

    public function destroy($farmer, $garden, $id)
    {
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
            'message' => "Berhasil disimpan"
        ], 200);
    }
}
