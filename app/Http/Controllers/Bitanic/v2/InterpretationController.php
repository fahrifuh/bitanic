<?php

namespace App\Http\Controllers\Bitanic\v2;

use App\Http\Controllers\Controller;
use App\Models\Interpretation;
use App\Models\LevelInterpretation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InterpretationController extends Controller
{
    public function index() {
        $data['datas'] = Interpretation::with(['level_interpretation'])->get();

        return view('bitanic.intepretasi.index', $data);
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
        $unsur = Interpretation::with(['level_interpretation'])->find($id);

        if (!$unsur) {
            return response()->json([
                'messages' => (object) [
                    'text' => ["Data tidak ditemukan!"]
                ]
            ], 404);
        }

        $v = Validator::make($request->all(), [
            'sangat_rendah' => 'nullable|numeric',
            'rendah_first' => 'required_without:sangat_rendah|numeric',
            'rendah_second' => [
                'required_with:sangat_rendah',
                'numeric',
                function ($attribute, $value, $fail)use($request) {
                    if (!$request->sangat_rendah && $value <= $request->rendah_first) {
                        $fail('Nilai rendah kedua ('.$value.') lebih rendah dari nilai rendah pertama');
                    }
                },
            ],
            'sedang_second' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail)use($request) {
                    if ($request->rendah_second && $value <= $request->rendah_second || !$request->rendah_second && $value <= $request->rendah_first) {
                        $fail('Nilai rendah kedua ('.$value.') lebih rendah dari nilai rendah pertama');
                    }
                },
            ],
            'tinggi_second' => 'nullable|numeric|gt:sedang_second',
        ]);

        if ($v->fails()) {
            return response()->json([
                'messages' => $v->errors(),
            ], 400);
        }

        $sangat_rendah = (isset($request->sangat_rendah)) ? $request->sangat_rendah : null;
        $rendah = $sangat_rendah ? $sangat_rendah."-".$request->rendah_second : $request->rendah_first;
        $sedang = $sangat_rendah ? $request->rendah_second."-".$request->sedang_second : $request->rendah_first."-".$request->sedang_second;
        $tinggi = isset($request->tinggi_second) ? $request->sedang_second."-".$request->tinggi_second : $request->sedang_second;
        $sangat_tinggi = isset($request->tinggi_second) ? $request->tinggi_second : null;

        $level = LevelInterpretation::query()->firstWhere('interpretation_id', $unsur->id);

        $level->update([
            'sangat_rendah' => $sangat_rendah,
            'rendah' => $rendah,
            'sedang' => $sedang,
            'tinggi' => $tinggi,
            'sangat_tinggi' => $sangat_tinggi,
        ]);

        return response()->json([
            'message' => "Data berhasil disimpan!"
        ], 200);
    }
}
