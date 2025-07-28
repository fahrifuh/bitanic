<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $city = City::query()
            ->with('province')
            ->withCount(['district']);

        if (request()->query('search')) {
            $search = request()->query('search');
            $city = $city->where('city_name', 'LIKE', '%'.$search.'%');
        }

        if (request()->query('province') && request()->query('province') != 'all') {
            $province = request()->query('province');
            $city = $city->where('province_id', $province);
        }

        $data['data'] = $city->paginate(10)->withQueryString();

        $data['provinces'] = Province::get(['id','prov_name'])->pluck('prov_name','id');

        return view('bitanic.wilayah.kabupaten-kota.index', $data);
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
            'city_name' => 'required|string|max:255|unique:cities,city_name',
            'province_id' => 'required|exists:provinces,id'
        ]);

        if ($v->fails()) {
            return response()->json(
                [
                    'messages' => $v->errors(),
                ],
                400,
            );
        }

        City::create($request->only(['city_name', 'province_id']));

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
        $city = City::find($id);

        if (!$city) {
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
            'city_name' => 'required|string|max:255|unique:cities,city_name,'.$id,
            'province_id' => 'required|exists:provinces,id'
        ]);

        if ($v->fails()) {
            return response()->json(
                [
                    'messages' => $v->errors(),
                ],
                400,
            );
        }

        $city->update($request->only(['city_name','province_id']));

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
        $city = City::find($id);

        if (!$city) {
            return response()->json(
                [
                    'messages' => (object) [
                        'text' => ['Data tidak ditemukan'],
                    ],
                ],
                404,
            );
        }

        $city->delete();

        return response()->json(
            [
                'message' => 'Berhasil',
            ],
            200,
        );
    }
}
