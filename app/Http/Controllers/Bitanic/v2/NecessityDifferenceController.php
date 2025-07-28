<?php

namespace App\Http\Controllers\Bitanic\v2;

use App\Http\Controllers\Controller;
use App\Models\NecessityDifference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class NecessityDifferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $necessity_differences = Cache::remember('selisih-kebutuhan', 60 * 60, function () {
            return NecessityDifference::query()
                ->orderBy('selisih_ph')
                ->get();
        });

        return view('bitanic.dolomit.index', compact('necessity_differences'));
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
            'selisih_ph' => 'required|numeric|unique:necessity_differences,selisih_ph',
            'kebutuhan_dolomit' => 'required|numeric',
        ]);

        if ($v->fails()) {
            return response()->json([
                'messages' => $v->errors(),
            ], 422);
        }

        NecessityDifference::create($request->only([
            'selisih_ph',
            'kebutuhan_dolomit',
        ]));

        Cache::forget('selisih-kebutuhan');

        session()->flash('success', 'Berhasil disimpan');

        return response()->json([
            'message' => "Data berhasil disimpan!"
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\NecessityDifference  $necessityDifference
     * @return \Illuminate\Http\Response
     */
    public function show(NecessityDifference $necessityDifference)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\NecessityDifference  $necessityDifference
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, NecessityDifference $necessityDifference)
    {
        $v = Validator::make($request->all(), [
            'selisih_ph' => 'required|numeric|unique:necessity_differences,selisih_ph,'.$necessityDifference->id,
            'kebutuhan_dolomit' => 'required|numeric',
        ]);

        if ($v->fails()) {
            return response()->json([
                'messages' => $v->errors(),
            ], 400);
        }

        $necessityDifference->update($request->only([
            'selisih_ph',
            'kebutuhan_dolomit',
        ]));

        Cache::forget('selisih-kebutuhan');
        session()->flash('success', 'Berhasil disimpan');

        return response()->json([
            'message' => "Data berhasil disimpan!"
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\NecessityDifference  $necessityDifference
     * @return \Illuminate\Http\Response
     */
    public function destroy(NecessityDifference $necessityDifference)
    {
        $necessityDifference->delete();

        Cache::forget('selisih-kebutuhan');

        session()->flash('success', 'Berhasil dihapus');

        return response()->json([
            'message' => "Data berhasil dihapus!"
        ], 200);
    }
}
