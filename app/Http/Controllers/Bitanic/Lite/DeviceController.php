<?php

namespace App\Http\Controllers\Bitanic\Lite;

use App\Http\Controllers\Controller;
use App\Models\LiteDevice;
use App\Models\LiteSeries;
use App\Models\LiteUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $lite_devices = LiteDevice::query()
            ->when(request()->query('search'), function($query){
                $search = request()->query('search');
                return $query->where('full_series', 'LIKE', '%'.$search.'%');
            })
            ->orderBy('full_series')
            ->paginate(10);

        return view('bitanic.lite.device.index', compact('lite_devices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $lite_series = LiteSeries::pluck('prefix', 'id');
        $lite_users = LiteUser::pluck('name', 'id');

        return view('bitanic.lite.device.create', compact('lite_series', 'lite_users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'min_tds' => 'required|numeric|regex:/^-?\d+(\.\d+)?$/',
            'max_tds' => 'required|numeric|regex:/^-?\d+(\.\d+)?$/|gt:min_tds',
            'min_ph' => 'required|numeric|regex:/^-?\d+(\.\d+)?$/',
            'max_ph' => 'required|numeric|regex:/^-?\d+(\.\d+)?$/|gt:min_ph',
            'lite_series_id' => 'required|exists:lite_series,id',
            'lite_user_id' => 'nullable|exists:lite_users,id',
            'version' => 'required|regex:/^[0-9.]+$/',
            'production_date' => 'required|date',
            'purchase_date' => 'required|date',
            'activate_date' => 'nullable|date',
            'image' => 'required|image|mimes:jpg,png|max:20480'
        ]);

        $image = image_intervention($request->file('image'), 'bitanic-photo/lite-devices/', 16/9);

        LiteDevice::create($request->only([
            'min_tds',
            'max_tds',
            'min_ph',
            'max_ph',
            'lite_series_id',
            'lite_user_id',
            'version',
            'production_date',
            'purchase_date',
            'activate_date',
        ]) + [
            'image' => $image,
        ]);

        return redirect()->back()->with('success', 'berhasil disimpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\LiteDevice  $liteDevice
     * @return \Illuminate\Http\Response
     */
    public function show(LiteDevice $lite_device)
    {
        $lite_device->load('schedule');
        return view('bitanic.lite.device.show', compact('lite_device'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LiteDevice  $liteDevice
     * @return \Illuminate\Http\Response
     */
    public function edit(LiteDevice $lite_device)
    {
        $lite_series = LiteSeries::pluck('prefix', 'id');
        $lite_users = LiteUser::pluck('name', 'id');

        return view('bitanic.lite.device.edit', compact('lite_device', 'lite_series', 'lite_users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LiteDevice  $liteDevice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LiteDevice $liteDevice)
    {
        $request->validate([
            'min_tds' => 'required|numeric|regex:/^-?\d+(\.\d+)?$/',
            'max_tds' => 'required|numeric|regex:/^-?\d+(\.\d+)?$/|gt:min_tds',
            'min_ph' => 'required|numeric|regex:/^-?\d+(\.\d+)?$/',
            'max_ph' => 'required|numeric|regex:/^-?\d+(\.\d+)?$/|gt:min_ph',
            'lite_user_id' => 'nullable|exists:lite_users,id',
            'version' => 'required|regex:/^[0-9.]+$/',
            'production_date' => 'required|date',
            'purchase_date' => 'required|date',
            'activate_date' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpg,png|max:20480'
        ]);

        $columns = [];

        if ($request->file('image')) {
            $image = image_intervention($request->file('image'), 'bitanic-photo/lite-devices/', 16/9);

            if(File::exists(public_path($liteDevice->image))){
                File::delete(public_path($liteDevice->image));
            }

            $columns = array_merge($columns, [
                'image' => $image
            ]);
        }

        $liteDevice->update($request->only([
            'min_tds',
            'max_tds',
            'min_ph',
            'max_ph',
            'lite_user_id',
            'version',
            'production_date',
            'purchase_date',
            'activate_date',
        ]) + $columns);

        return redirect()->back()->with('success', 'berhasil disimpan');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LiteDevice  $liteDevice
     * @return \Illuminate\Http\Response
     */
    public function destroy(LiteDevice $liteDevice)
    {
        if(File::exists(public_path($liteDevice->image))){
          File::delete(public_path($liteDevice->image));
        }

        $liteDevice->delete();

        session()->flash('success', 'Berhasil dihapus');

        return response()->json([
          'message' => "Berhasil"
        ], 200);
    }
}
