<?php

namespace App\Http\Controllers\Bitanic\Lite;

use App\Http\Controllers\Controller;
use App\Models\LiteDevice;
use App\Models\LiteDevicePump;
use Illuminate\Http\Request;

class PumpController extends Controller
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
    public function create(LiteDevice $lite_device)
    {
        return view('bitanic.lite.device.pump.create', compact('lite_device'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, LiteDevice $lite_device)
    {
        $request->validate([
            // 'min_tds' => 'required|numeric|regex:/^-?\d+(\.\d+)?$/',
            // 'max_tds' => 'required|numeric|regex:/^-?\d+(\.\d+)?$/|gt:min_tds',
            // 'min_ph' => 'required|numeric|regex:/^-?\d+(\.\d+)?$/',
            // 'max_ph' => 'required|numeric|regex:/^-?\d+(\.\d+)?$/|gt:min_ph',
            'is_active' => 'required|integer|in:0,1',
            'name' => 'nullable|string|max:255',
        ]);

        $lite_device->loadCount('pumps');

        LiteDevicePump::create($request->only([
            // 'min_tds',
            // 'max_tds',
            // 'min_ph',
            // 'max_ph',
            'is_active',
            'name',
        ]) + [
            'number' => $lite_device->pumps_count + 1,
            'lite_device_id' => $lite_device->id
        ]);

        return redirect()->route('bitanic.lite-device.show', $lite_device->id)->with('success', 'Berhasil disimpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\LiteDevicePump  $liteDevicePump
     * @return \Illuminate\Http\Response
     */
    public function show(LiteDevicePump $liteDevicePump)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LiteDevicePump  $liteDevicePump
     * @return \Illuminate\Http\Response
     */
    public function edit(LiteDevice $lite_device, LiteDevicePump $lite_device_pump)
    {
        if ($lite_device->id != $lite_device_pump->lite_device_id) {
            return back()->with('failed', 'Pompa tidak sesuai dengan perangkat');
        }
        return view('bitanic.lite.device.pump.edit', compact('lite_device_pump', 'lite_device'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LiteDevicePump  $liteDevicePump
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LiteDevice $lite_device, LiteDevicePump $lite_device_pump)
    {
        if ($lite_device->id != $lite_device_pump->lite_device_id) {
            abort(403, 'Data pompa tidak sama dengan yang ada di perangkat');
        }

        $request->validate([
            // 'min_tds' => 'required|numeric|regex:/^-?\d+(\.\d+)?$/',
            // 'max_tds' => 'required|numeric|regex:/^-?\d+(\.\d+)?$/|gt:min_tds',
            // 'min_ph' => 'required|numeric|regex:/^-?\d+(\.\d+)?$/',
            // 'max_ph' => 'required|numeric|regex:/^-?\d+(\.\d+)?$/|gt:min_ph',
            'is_active' => 'required|integer|in:0,1',
            'name' => 'nullable|string|max:255',
        ]);

        $lite_device_pump->update($request->only([
            // 'min_tds',
            // 'max_tds',
            // 'min_ph',
            // 'max_ph',
            'is_active',
            'name',
        ]));

        return back()->with('success', 'Berhasil disimpan');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LiteDevicePump  $liteDevicePump
     * @return \Illuminate\Http\Response
     */
    public function destroy(LiteDevice $lite_device, LiteDevicePump $lite_device_pump)
    {
        if ($lite_device->id != $lite_device_pump->lite_device_id) {
            abort(403, 'Data pompa tidak sama dengan yang ada di perangkat');
        }

        $number = $lite_device_pump->number;

        $lite_device_pump->delete();

        $lands = LiteDevicePump::query()
            ->where([
                ['lite_device_id', $lite_device->id],
                ['number', '>', $number],
            ])
            ->orderBy('number')
            ->get(['id', 'lite_device_id', 'number']);

        foreach ($lands as $a) {
            $a->update([
                'number' => $number
            ]);

            $number++;
        }

        return response()->json('berhasil dihapus');
    }
}
