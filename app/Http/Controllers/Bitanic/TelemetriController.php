<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\Farmer;
use App\Models\Garden;
use App\Models\Telemetri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TelemetriController extends Controller
{
    public function index(Request $request, Farmer $farmer, Garden $garden)
    {
        if (!Gate::allows('update-garden', $garden)) return abort(403);

        $telemetries = Telemetri::query()
            ->with(['device:id,device_series'])
            ->whereHas('garden', function($query)use($garden){
                $query->where('id', $garden->id);
            });

        if ($request->query('date_start') && $request->date('date_start')) {
            if ($request->date('date_end') && $request->date('date_end')->lt($request->date('date_start'))) {
                return back()->withErrors([
                    'errors' => 'Waktu awal tidak boleh lebih besar dari waktu akhir!'
                ]);
            }
            $telemetries = $telemetries->where(function($query)use($request){
                if ($request->date('date_end')) {
                    $query->whereBetween('datetime', [$request->date('date_start'), $request->date('date_end')]);
                } else {
                    $query->whereDate('datetime', $request->date('date_start'));
                }
            });
        }

        $data['data'] = $telemetries->orderBy('datetime', 'desc')->paginate(10)->withQueryString();

        $garden->load(['land:id,farmer_id,name']);

        $data['garden'] = $garden;
        $data['farmer'] = $farmer;

        return view('bitanic.garden.telemetri', $data);
    }

    public function show($id)
    {
        $telemetri = Telemetri::with(['farmer','device','garden'])->find($id);

        if (!$telemetri) {
            return response()->json([
                'message' => (object) [
                    'text' => ['Data tidak ditemukan']
                ]
            ], 404);
        }

        if (! Gate::allows('show-telemetri', $telemetri)) {
            return response()->json([
                'messages' => (object) [
                    'warning' => ["Anda tidak dapat mengakses data ini"]
                ]
            ], 403);
        }

        return response()->json([
            'data' => $telemetri,
            'message' => 'Data telemetri'
        ], 200);
    }
}
