<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\District;
use App\Models\Farmer;
use App\Models\FarmerGroup;
use App\Models\FarmerTransaction;
use App\Models\Garden;
use App\Models\Province;
use App\Models\Subdistrict;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getCountFertilizerByProvince(Request $request)
    {
        $year = $request->query('year', now()->year);
        $provinces = Province::query()->pluck('prov_name', 'id');

        return view('fertilizer-province', compact('provinces', 'year'));
    }

    public function getCountFertilizerByCity($province)
    {
        $cities = City::query()->where('province_id', $province)->pluck('city_name', 'id');

        return view('fertilizer-cities', compact('cities', 'province'));
    }

    public function getCountFertilizerByDistrict($city)
    {
        $districts = District::query()->where('city_id', $city)->pluck('dis_name', 'id');

        return view('fertilizer-districts', compact('districts', 'city'));
    }

    public function getCountFertilizerBySubdistrict($district)
    {
        $subdistricts = Subdistrict::query()->where('dis_id', $district)->pluck('subdis_name', 'id');

        return view('fertilizer-subdistrict', compact('subdistricts', 'district'));
    }

    public function getCountFertilizerByGroup($subdistrict)
    {
        $groups = FarmerGroup::query()->where('subdis_id', $subdistrict)->pluck('name', 'id');

        return view('fertilizer-group', compact('groups', 'subdistrict'));
    }

    public function getCountFertilizerByFarmer($group)
    {
        $farmers = Farmer::query()->whereNotNull('group_id')->where('group_id', $group)->pluck('full_name', 'id');

        return view('fertilizer-petani', compact('farmers', 'group'));
    }

    public function rekapitulasiTransaksiBitanic(Request $request) : JsonResponse {
        $year = $request->query('year', now('Asia/Jakarta')->year);

        $data = FarmerTransaction::query()
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('count(*) as total')
            )
            ->when(auth()->user()->role != 'admin', function($query){
                return $query->where('user_id', auth()->id());
            })
            ->whereYear('created_at', $year)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month')
            ->get();

        $transactions = [];
        foreach (get_month() as $key => $month) {
            if ($transaksi = $data->firstWhere('month', $key+1)) {
                array_push($transactions, (object) [
                    'x' => get_month($transaksi->month),
                    'y' => $transaksi->total
                ]);
            } else {
                array_push($transactions, (object) [
                    'x' => $month,
                    'y' => 0
                ]);
            }
        }

        return response()->json([
            'transactions' => $transactions
        ]);
    }
}
