<?php

namespace App\Http\Controllers\Hydroponic;

use App\Http\Controllers\Controller;
use App\Models\HydroponicDevice;
use App\Models\HydroponicUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class DeviceController extends Controller
{
    protected string $filePath = 'bitanic-photo/hydroponic/devices/';

    /**
     * Display a listing of the resource.
     *
     */
    public function index(): View
    {
        $hydroponicDevices = HydroponicDevice::query()
            ->latest('created_at')
            ->paginate(10);

        return view('bitanic.hydroponic.device.index', compact("hydroponicDevices"));
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create(): View
    {
        $hydroponicUsers = HydroponicUser::query()
            ->pluck('name', 'id');

        return view('bitanic.hydroponic.device.create', compact('hydroponicUsers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'series'            => 'required|string|alpha_dash|max:255|unique:hydroponic_devices,series',
            'version'           => 'required|string|regex:/^\d+\.\d+\.\d+$/',
            'user_id'           => 'nullable|integer|exists:hydroponic_users,id',
            'production_date'   => 'required|date_format:Y-m-d',
            'purchase_date'     => 'nullable|date_format:Y-m-d',
            'activation_date'   => 'nullable|date_format:Y-m-d',
            'picture'           => 'required|image|mimes:jpg,png|max:5048',
            'note'              => 'nullable|string|max:2000',
        ]);

        $picture = image_intervention($request->file('picture'), $this->filePath, 1/1);
        $pumps = (object) [
            "water" => 0,
            "nutrient" => 0,
            "ph_basa" => 0,
            "ph_asam" => 0,
            "mixer" => 0,
        ];
        $thresholds = (object) [
            "crop_name" => null,
            "water" => [null, null],
            "nutrient" => [null, null],
            "ph_basa" => null,
            "ph_asam" => null,
        ];

        HydroponicDevice::create(
            $request->only([
                'series',
                'version',
                'user_id',
                'production_date',
                'purchase_date',
                'activation_date',
                'note',
            ]) +
            [
                'picture'       => $picture,
                'pumps'         => $pumps,
                'thresholds'    => $thresholds,
            ]
        );

        return redirect()
            ->route('bitanic.hydroponic.device.index')
            ->with('success', 'Berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\HydroponicDevice  $hydroponicDevice
     */
    public function show(HydroponicDevice $hydroponicDevice): View
    {
        $hydroponicDevice->load('latestTelemetry');

        return view('bitanic.hydroponic.device.show', compact('hydroponicDevice'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\HydroponicDevice  $hydroponicDevice
     */
    public function edit(HydroponicDevice $hydroponicDevice): View
    {
        $hydroponicUsers = HydroponicUser::query()
            ->pluck('name', 'id');

        return view('bitanic.hydroponic.device.edit', compact('hydroponicDevice', 'hydroponicUsers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\HydroponicDevice  $hydroponicDevice
     */
    public function update(Request $request, HydroponicDevice $hydroponicDevice): RedirectResponse
    {
        $request->validate([
            'series'            => 'required|string|alpha_dash|max:255|unique:hydroponic_devices,series,' . $hydroponicDevice->id,
            'version'           => 'required|string|regex:/^\d+\.\d+\.\d+$/',
            'user_id'           => 'nullable|integer|exists:hydroponic_users,id',
            'production_date'   => 'required|date_format:Y-m-d',
            'purchase_date'     => 'nullable|date_format:Y-m-d',
            'activation_date'   => 'nullable|date_format:Y-m-d',
            'picture'           => 'nullable|image|mimes:jpg,png|max:5048',
            'note'              => 'nullable|string|max:2000',
        ]);


        $picture = $hydroponicDevice->picture;

        if ($request->file('picture')) {
            $picture = image_intervention($request->file('picture'), $this->filePath, 1/1);

            if(File::exists(public_path($hydroponicDevice->picture))){
                File::delete(public_path($hydroponicDevice->picture));
            }
        }

        $hydroponicDevice->update(
            $request->only([
                'series',
                'version',
                'user_id',
                'production_date',
                'purchase_date',
                'activation_date',
                'note',
            ]) +
            [
                'picture'       => $picture,
            ]
        );

        return redirect()
            ->route('bitanic.hydroponic.device.show', $hydroponicDevice->id)
            ->with('success', 'Berhasil disimpan!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\HydroponicDevice  $hydroponicDevice
     * @return \Illuminate\Http\Response
     */
    public function destroy(HydroponicDevice $hydroponicDevice)
    {
        if(File::exists(public_path($hydroponicDevice->picture))){
            File::delete(public_path($hydroponicDevice->picture));
        }

        $hydroponicDevice->delete();

        $message = 'Berhasil dihapus';

        session()->flash('success', $message);

        if (request()->wantsJson()) {
            return response()
                ->json([
                    'message' => $message
                ]);
        }

        return redirect()
            ->route('bitanic.hydroponic.device.index');
    }
}
