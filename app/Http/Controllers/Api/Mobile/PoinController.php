<?php

namespace App\Http\Controllers\APi\Mobile;

use App\Http\Controllers\Controller;
use App\Models\BitanicPlusPoin;
use App\Models\BitanicPlusPoinHistory;
use App\Models\PoinDescription;
use Illuminate\Http\Request;

class PoinController extends Controller
{
    public function bitanicPlusSaya()
    {
        $id = auth()->user()->id;

        $data['poin_saya'] = BitanicPlusPoin::where('user_id', $id)->first();
        $data['cara_dapat_poin'] = PoinDescription::get();
        $data['riwayat_poin'] = BitanicPlusPoinHistory::where('bitanic_plus_poin_id', $data['poin_saya']->id)->get();

        return response()->json([
            'data' => (object) $data,
            'message' => "Data berhasil didapat",
            'status' => 200
        ], 200);
    }

    public function rewardPoinPlus()
    {
        $data['data'] = BitanicPlusPoin::get();
        $data['message'] = "Data poin reward bitanic plus";
        $data['status'] = 200;

        return response()->json($data, 200);
    }
}
