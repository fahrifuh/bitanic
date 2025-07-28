<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Crop;
use App\Models\Device;
use App\Models\Garden;
use App\Models\HarvestProduce;
use Illuminate\Http\Request;

class CropController extends Controller
{
    public function listTanaman()
    {
        $date = now('Asia/Jakarta')->format('Y-m-d');
        $user = auth()->user();
        $crop_id_from_garden = Garden::select('crop_id')->where('farmer_id', $user->farmer->id)->get();

        $data['tanaman'] = Crop::whereIn('id', $crop_id_from_garden)->get();
        $data['tanaman_siap_panen'] = Crop::whereIn('id', $crop_id_from_garden)->whereHas('garden', function($query)use($date){
            $query->where('estimated_harvest', '<=', $date);
        })->get();

        return response()->json([
            'data' => (object) $data,
            'message' => "List data tanaman anda",
            'status' => 200
        ]);
    }

    public function masterTanaman()
    {
        $data['data'] = Crop::get();
        $data['message'] = "Data master tanaman";
        $data['status'] = 200;

        return response()->json($data, 200);
    }

    public function hasilPanen(Request $request)
    {
        $tgl = now('Asia/Jakarta')->format('m');
        $filter['bulan'] = $request->query('bulan_panen', $tgl);
        $filter['jenis'] = $request->query('jenis', null);
        $user = auth()->user();

        $hasil_panen = HarvestProduce::query()
            ->with([
                'crop:id,crop_name,type,season,picture',
                'garden:id,land_id',
                'garden.land:id,name,address'
            ])
            ->whereHas('garden.land', function($query)use($user){
                $query->where('farmer_id', $user->farmer->id);
            })
            ->whereMonth('date', $filter['bulan']);

        if ($filter['jenis'] != null) {
            $hasil_panen = $hasil_panen->whereHas('crop', function($query)use($filter){
                $query->where('type', $filter['jenis']);
            });
        }

        $hasil_panen = $hasil_panen->get();

        return response()->json([
            'data' => $hasil_panen,
            'message' => "Data hasil panen",
            'status' => 200
        ], 200);
    }

    public function tanamanPanen(Request $request)
    {
        if (!$garden = Garden::find($request->kebun_id)) {
            return response()->json([
                'message' => "Kebun tidak ditemukan",
                'status' => 404
            ], 404);
        }

        if ($request->status === 3) {
            $garden->harvest_date = now('Asia/Jakarta');
        }

        $garden->harvest_status = $request->status;

        $garden->save();

        $perangkat = Device::where('garden_id', $garden->id)->first();
        $perangkat->garden_id = NULL;
        $perangkat->save();

        return response()->json([
            'message' => "Berhasil melakukan panen",
            'status' => 200
        ], 200);
    }
}
