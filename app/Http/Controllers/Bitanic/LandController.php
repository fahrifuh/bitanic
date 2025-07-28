<?php

namespace App\Http\Controllers\Bitanic;

use App\Exports\RscTelemetriesExport;
use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Farmer;
use App\Models\Land;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;

class LandController extends Controller
{
    public function index(Farmer $farmer)
    {
        $lands = Land::query()
            ->select(['id', 'name', 'area', 'farmer_id'])
            ->where('farmer_id', $farmer->id)
            ->orderBy('name')
            ->paginate(10);

        return view('bitanic.farmer.land.index', compact('lands', 'farmer'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Farmer $farmer)
    {
        $farmer_id = (auth()->user()->role == 'farmer')
            ? auth()->user()->farmer->id
            : $farmer->id;

        return view('bitanic.farmer.land.create', compact('farmer'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Farmer $farmer)
    {
        $request['polygon'] = $request->polygon ? json_decode($request->polygon) : [];
        $request->validate([
            'polygon.*.*' => 'required|regex:/^(-?\d+(\.\d+)?)$/',
            'polygon.*' => ['required', 'array'],
            'polygon' => ['required', 'array'],
            'latitude'               => 'required|regex:/^(-?\d+(\.\d+)?)$/',
            'longitude'               => 'required|regex:/^(-?\d+(\.\d+)?)$/',
            'altitude' => ['required', 'numeric'],
            'area' => ['required', 'numeric', 'min:0'],
            'color'     => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'name' => ['required', 'string', 'max:255'],
            'address'           => 'required|string|max:1000',
            'image'           => 'required|image|mimes:jpg,png,jpeg|max:10048',
        ]);

        $farmer_id = (auth()->user()->role == 'farmer')
            ? auth()->user()->farmer->id
            : $farmer->id;

        $input = [
            'image' => image_intervention($request->file('image'), 'bitanic-photo/lands/', 4 / 3),
            'polygon' => $request->polygon,
            'farmer_id' => $farmer->id,
            'color' => substr($request->color, 1)
        ];

        $land = Land::create($request->only([
            'latitude',
            'longitude',
            'altitude',
            'area',
            'name',
            'address',
        ])
        + $input);

        activity()
            ->performedOn($land)
            ->withProperties(
                collect($land)
                    ->except(['id', 'image', 'created_at', 'updated_at'])
                    ->merge([
                        'farmer' => $land->farmer_id == null ? null : $farmer->full_name
                    ]),
            )
            ->event('created')
            ->log('created');

        return back()->with('success', 'Berhasil disimpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Farmer $farmer, Land $land)
    {
        if (! Gate::allows('land', $land)) {
            return back()->withErrors([
                'warning' => ["Anda tidak dapat mengakses data ini"]
            ]);
        }

        if ($farmer->id != $land->farmer_id) {
            return back()->withErrors([
                'warning' => ["Lahan bukan milik petani yang dipilih"]
            ]);
        }

        $land->load('rsc_telemetries');

        $avgN = null;
        $avgP = null;
        $avgK = null;

        if ($land->rsc_telemetries) {
            $avgN = round(collect($land->rsc_telemetries)->map(function($item){ return $item->samples->n;})->avg(), 2, PHP_ROUND_HALF_DOWN);
            $avgP = round(collect($land->rsc_telemetries)->map(function($item){ return $item->samples->p;})->avg(), 2, PHP_ROUND_HALF_DOWN);
            $avgK = round(collect($land->rsc_telemetries)->map(function($item){ return $item->samples->k;})->avg(), 2, PHP_ROUND_HALF_DOWN);
        }

        return view('bitanic.rsc.show', compact('land', 'farmer', 'avgN', 'avgP', 'avgK'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Farmer $farmer, Land $land)
    {
        return view('bitanic.farmer.land.edit', compact('farmer', 'land'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Farmer $farmer, Land $land)
    {
        $request['polygon'] = $request->polygon ? json_decode($request->polygon) : [];
        $request->validate([
            'polygon.*.*' => ['required', 'regex:/^(-?\d+(\.\d+)?)$/'],
            'polygon.*' => ['required', 'array'],
            'polygon' => ['required', 'array'],
            'latitude'               => 'required|regex:/^(-?\d+(\.\d+)?)$/',
            'longitude'               => 'required|regex:/^(-?\d+(\.\d+)?)$/',
            'altitude' => ['required', 'numeric'],
            'area' => ['required', 'numeric', 'min:0'],
            'name' => ['required', 'string', 'max:255'],
            'address'           => 'required|string|max:1000',
            'image'           => 'nullable|image|mimes:jpg,png,jpeg|max:10048',
        ]);

        $picture_new = [];
        $picture_old = [];
        $image = $land->image;

        if ($request->file('image')) {
            $image = image_intervention($request->file('image'), 'bitanic-photo/lands/', 4 / 3);

            if (File::exists(public_path($land->image))) {
                File::delete(public_path($land->image));
            }

            $picture_new = ['image' => 'Updated'];
            $picture_old = ['image' => 'Old'];
        }

        $original = $land->getOriginal();

        $input = [
            'image' => $image,
            'polygon' => $request->polygon,
        ];

        $land->update($request->only([
            'latitude',
            'longitude',
            'altitude',
            'area',
            'name',
            'address',
        ])
        + $input);

        $changes = collect($land->getChanges());
        $old = collect($original)->only($changes->keys());

        activity()
            ->performedOn($land)
            ->withProperties(
                collect(
                    array_merge(
                        [
                            'old' => $old
                                ->except(['image', 'updated_at'])
                                ->merge($picture_old)
                                ->toArray(),
                        ],
                        [
                            'new' => $changes
                                ->except(['image', 'updated_at'])
                                ->merge($picture_new)
                                ->toArray(),
                        ],
                    ),
                )->toArray(),
            )
            ->event('updated')
            ->log('updated');

        return back()->with('success', 'Berhasil disimpan');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Farmer $farmer, Land $land)
    {
        if ($land->image && File::exists(public_path($land->image))) {
            File::delete(public_path($land->image));
        }

        activity()
            ->performedOn($land)
            ->withProperties(['name', $land->name])
            ->event('deleted')
            ->log('deleted');

        $land->delete();

        return response()->json([
            'message' => 'Berhasil disimpan'
        ]);
    }

    public function getLands(Farmer $farmer) : JsonResponse {
        $lands = Land::query()
            ->select([
                'id',
                'name',
                'area',
                'latitude',
                'longitude',
                'polygon',
                'altitude',
                'farmer_id',
                'color',
            ])
            ->where('farmer_id', $farmer->id)
            ->orderBy('name')
            ->get();

        return response()->json($lands);
    }

    public function getLand(Farmer $farmer, $id) : JsonResponse {
        $land = Land::query()
            ->select([
                'id',
                'name',
                'area',
                'latitude',
                'longitude',
                'polygon',
                'altitude',
                'farmer_id',
                'created_at',
                'address',
                'image',
                'color',
            ])
            ->where('farmer_id', $farmer->id)
            ->firstWhere('id', $id);

        if (!$land) {
            return response()->json([
                'errors' => [
                    'messages' => [
                        'Data tidak ditemukan'
                    ]
                ]
            ], 404);
        }

        return response()->json($land);
    }

    public function getLandRsc(Farmer $farmer, $id) : JsonResponse {
        $land = Land::query()
            ->select([
                'id',
                'name',
                'area',
                'latitude',
                'longitude',
                'polygon',
                'altitude',
                'farmer_id',
                'created_at',
                'address',
                'image',
                'color',
            ])
            ->with('rsc_telemetries')
            ->where('farmer_id', $farmer->id)
            ->firstWhere('id', $id);

        if (!$land) {
            return response()->json([
                'errors' => [
                    'messages' => [
                        'Data tidak ditemukan'
                    ]
                ]
            ], 404);
        }

        return response()->json($land);
    }

    public function showRsc(Farmer $farmer, Land $land) {
        if (! Gate::allows('land', $land)) {
            return back()->withErrors([
                'warning' => ["Anda tidak dapat mengakses data ini"]
            ]);
        }

        if ($farmer->id != $land->farmer_id) {
            return back()->withErrors([
                'warning' => ["Lahan bukan milik petani yang dipilih"]
            ]);
        }

        $land->load('rsc_telemetri');

        return view('bitanic.rsc.show', compact('land', 'farmer'));
    }

    public function exportExcelRsc(Request $request, Farmer $farmer, Land $land) {
        return Excel::download(new RscTelemetriesExport($land->id), now()->format('YmdHis') . '_rsc-telemetries.xlsx');
    }
}
