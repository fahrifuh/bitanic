<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\Interpretation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InterpretationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getStatus(Request $request) : JsonResponse {
        $unsur = Interpretation::with('level_interpretation')->firstWhere('nama', $request->unsur);

        if (!$unsur) {
            return response()->json([
                'message' => 'Data interpretasi tidak ditemukan!'
            ], 404);
        }

        $status = "Sangat Rendah";

        $level = $unsur->level_interpretation;

        $rendah = explode("-", $level->rendah);
        $sedang = explode("-", $level->sedang);
        $tinggi = explode("-", $level->tinggi);

        if (!$level->sangat_rendah && $request->ppm < (double)$level->rendah) {
            $status = "Rendah";
        } elseif ($level->sangat_rendah && $request->ppm >= (double)$rendah[0] && $request->ppm < (double)$rendah[1]) {
            $status = "Rendah";
        } elseif ($request->ppm >= (double)$sedang[0] && $request->ppm <= (double)$sedang[1]) {
            $status = "Sedang";
        } elseif (!$level->sangat_tinggi && $request->ppm > (double)$level->tinggi) {
            $status = "Tinggi";
        } elseif ($level->sangat_tinggi && $request->ppm >= (double)$tinggi[0] && $request->ppm < (double)$tinggi[1]) {
            $status = "Tinggi";
        } elseif ($level->sangat_tinggi && $request->ppm > (double)$level->sangat_tinggi) {
            $status = "Sangat Tinggi";
        }

        return response()->json([
            'message' => "Status Intepretasi",
            'data' => $status
        ]);
    }
}
