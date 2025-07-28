<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Subdistrict;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubdistrictController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $subdis = Subdistrict::query()
            ->with('district');

        if (auth()->user()->role == 'admin' && auth()->user()->city_id != null) {
            $subdis = $subdis->whereHas('district', function($query){
                $query->where('city_id', auth()->user()->city_id);
            });
        }

        if (request()->query('search')) {
            $search = request()->query('search');
            $subdis = $subdis->where('subdis_name', 'LIKE', '%'.$search.'%');
        }

        if (request()->query('district') && is_numeric(request()->query('district'))) {
            $district = request()->query('district');
            $subdis = $subdis->where('dis_id', $district);
        }

        $data['data'] = $subdis->paginate(10)->withQueryString();

        $districts = District::query();

        if (auth()->user()->role == 'admin' && auth()->user()->city_id != null) {
            $districts = $districts->where('city_id', auth()->user()->city_id);
        }

        $data['districts'] = $districts->get(['id','dis_name'])->pluck('dis_name','id');

        return view('bitanic.wilayah.desa.index', $data);
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
            'subdis_name' => 'required|string|max:255|unique:subdistricts,subdis_name',
            'dis_id' => 'required|exists:districts,id'
        ]);

        if ($v->fails()) {
            return response()->json(
                [
                    'messages' => $v->errors(),
                ],
                400,
            );
        }

        Subdistrict::create($request->only(['subdis_name', 'dis_id']));

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
        $subdistrict = Subdistrict::find($id);

        if (!$subdistrict) {
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
            'subdis_name' => 'required|string|max:255|unique:subdistricts,subdis_name,'.$id,
            'dis_id' => 'required|exists:districts,id'
        ]);

        if ($v->fails()) {
            return response()->json(
                [
                    'messages' => $v->errors(),
                ],
                400,
            );
        }

        $subdistrict->update($request->only(['subdis_name','dis_id']));

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
        $subdistrict = Subdistrict::find($id);

        if (!$subdistrict) {
            return response()->json(
                [
                    'messages' => (object) [
                        'text' => ['Data tidak ditemukan'],
                    ],
                ],
                404,
            );
        }

        $subdistrict->delete();

        return response()->json(
            [
                'message' => 'Berhasil',
            ],
            200,
        );
    }
}
