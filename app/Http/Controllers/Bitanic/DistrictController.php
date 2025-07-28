<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\District;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DistrictController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $district = District::query()
            ->with('city')
            ->withCount(['subdistrict']);

        if (auth()->user()->role == 'admin' && auth()->user()->city_id != null) {
            $district = $district->where('city_id', auth()->user()->city_id);
        }

        if (request()->query('search')) {
            $search = request()->query('search');
            $district = $district->where('dis_name', 'LIKE', '%'.$search.'%');
        }

        if (request()->query('city') && request()->query('city') != 'all') {
            $city = request()->query('city');
            $district = $district->where('city_id', $city);
        }

        $data['data'] = $district->paginate(10)->withQueryString();

        $cities = City::query();

        if (auth()->user()->role == 'admin' && auth()->user()->city_id != null) {
            $cities = $cities->where('id', auth()->user()->city_id);
        }

        $data['cities'] = $cities->get(['id','city_name'])->pluck('city_name','id');

        return view('bitanic.wilayah.kecamatan.index', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'dis_name' => 'required|string|max:255|unique:districts,dis_name',
            'city_id' => 'required|exists:cities,id'
        ]);

        if ($v->fails()) {
            return response()->json(
                [
                    'messages' => $v->errors(),
                ],
                400,
            );
        }

        District::create($request->only(['dis_name', 'city_id']));

        return response()->json(
            [
                'message' => 'Berhasil disimpan',
            ],
            200,
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $district = District::find($id);

        if (!$district) {
            return response()->json(
                [
                    'messages' => (object) [
                        'text' => ['Data tidak ditemukan'],
                    ],
                ],
                404,
            );
        }

        $v = Validator::make($request->all(), [
            'dis_name' => 'required|string|max:255|unique:districts,dis_name,'.$id,
            'city_id' => 'required|exists:cities,id'
        ]);

        if ($v->fails()) {
            return response()->json(
                [
                    'messages' => $v->errors(),
                ],
                400,
            );
        }

        $district->update($request->only(['dis_name','city_id']));

        return response()->json(
            [
                'message' => 'Berhasil',
            ],
            200,
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $district = District::find($id);

        if (!$district) {
            return response()->json(
                [
                    'messages' => (object) [
                        'text' => ['Data tidak ditemukan'],
                    ],
                ],
                404,
            );
        }

        $district->delete();

        return response()->json(
            [
                'message' => 'Berhasil',
            ],
            200,
        );
    }
}
