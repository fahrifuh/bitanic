<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\ActiveGarden;
use App\Models\Crop;
use App\Models\Device;
use App\Models\Farmer;
use App\Models\Fertilization;
use App\Models\FertilizationSchedule;
use App\Models\Garden;
use App\Models\Land;
use App\Models\Province;
use App\Models\RscGarden;
use App\Models\Telemetri;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use PhpMqtt\Client\Facades\MQTT;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class GardenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($farmer, $land)
    {
        $data['farmer'] = Farmer::select('id', 'full_name', 'user_id')->findOrFail($farmer);

        if (auth()->user()->role == 'farmer' && $data['farmer']->user_id != auth()->user()->id) {
            return abort(403);
        }

        if (auth()->user()->role == 'admin' && auth()->user()->city_id != null && $data['farmer']->user->subdistrict->district->city_id != auth()->user()->city_id) {
            return abort(403);
        }

        $data['land'] = Land::query()
            ->select(['id', 'name', 'polygon', 'color'])
            ->where('farmer_id', $farmer)
            ->findOrFail($land);

        $data['data'] = Garden::with([
                'currentCommodity:id,crop_id,garden_id,estimated_harvest',
                'currentCommodity.crop:id,crop_name',
                'device.specification',
                'land:id,name,area,farmer_id'
            ])
            ->whereHas('land', function($land)use($farmer){
                $land->where('farmer_id', $farmer);
            })
            ->where('land_id', $land)
            ->orderByDesc('created_at')
            ->paginate(10);

        $data['crops'] = Crop::get();
        $data['devices'] = Device::query()
            ->whereNull('garden_id')
            ->where('farmer_id', $farmer)
            ->get();

        return view('bitanic.garden.index', $data);
    }

    public function create(Farmer $farmer, Land $land)
    {
        if ($farmer->id != $land->farmer_id) {
            return back()->with('failed', 'Lahan tidak sama dengan yang ada di petani');
        }

        $data['crops'] = Crop::get();
        $data['devices'] = Device::query()
            ->where('farmer_id', $farmer->id)
            ->withCount('gardens')
            // ->where(function($query){
            //     $query->doesntHave('garden')
            //     ->orWhereDoesntHave('garden', function($gardens){
            //         $gardens->whereIn('harvest_status', [0,1,2]);
            //     });
            // })
            // ->where('gardens_count', '<', 4)
            ->having('gardens_count', '<', 4)
            ->pluck('device_series', 'id');
        $data['lands'] = Land::query()
            ->where('farmer_id', $farmer->id)
            // ->whereDoesntHave('gardens', function($gardens){
            //     $gardens->whereIn('harvest_status', [0,1,2]);
            // })
            ->pluck('name', 'id');

        $data['farmer'] = $farmer;
        $data['land'] = $land;

        return view('bitanic.garden.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Farmer $farmer, Land $land)
    {
        if ($farmer->id != $land->farmer_id) {
            return back()->with('failed', 'Lahan tidak sama dengan yang ada di petani');
        }

        $v = Validator::make($request->all(), [
            // 'land_id'           => ['required', 'exists:lands,id'],
            'category'          => 'required|string|in:urban,rural',
            'gardes_type'       => 'required|in:hidroponik,aquaponik,tradisional,vertical,green_house',
            'date_created'      => 'required|date',
            // 'estimated_harvest' => 'required|date',
            // 'crop_id'           => 'required|exists:crops,id',
            'device_id'         => 'required|exists:devices,id',
            'pipa'              => 'nullable|integer',
            'lubang_pipa'       => 'nullable|integer',
            'length'            => 'nullable|numeric',
            'width'             => 'nullable|numeric',
            'height'            => 'nullable|numeric',
            'fish_type'         => 'nullable|string|max:255',
            'name'              => ['required', 'string', 'max:255'],
            'area'              => ['required', 'numeric', 'min:0'],
            'color'             => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'image'             => 'required|image|mimes:jpg,png,jpeg|max:10240',
        ]);

        if ($v->fails()) {
            return back()->withErrors($v)->withInput();
        }

        $polygon = json_decode($request->polygon, true);

        $v = Validator::make([
            'polygon' => $polygon
        ], [
            'polygon' => ['required', 'array'],
            'polygon.*' => ['required', 'array'],
            'polygon.*.*' => 'required|regex:/^(-?\d+(\.\d+)?)$/',
        ]);

        if ($v->fails()) {
            return back()->withErrors($v)->withInput();
        }

        $query = [
            'picture' => image_intervention($request->file('image'), 'bitanic-photo/gardens/', 4 / 3),
            'land_id' => $land->id,
            'device_id' => $request->device_id,
            'harvest_status' => 0,
            'color' => substr($request->color, 1),
            'polygon' => $polygon,
            'estimated_harvest' => now(),
        ];

        if (in_array($request->gardes_type, ['hidroponik','aquaponik'])) {
            $query = array_merge($query, [
                'levels' => $request->pipa ?? null,
                'holes' => $request->lubang_pipa ?? null
            ]);

            if ($request->gardes_type == 'aquaponik') {
                $query = array_merge($query, [
                    'length' => $request->length ?? null,
                    'width' => $request->width ?? null,
                    'height' => $request->height ?? null,
                    'fish_type' => $request->fish_type ?? null
                ]);
            }
        }

        $garden = Garden::create($request->only([
            'gardes_type',
            'category',
            'date_created',
            'name',
            'area',
        ]) + $query);

        return redirect()
            ->route('bitanic.garden.index', [
                'farmer' => $farmer->id,
                'land' => $land->id,
            ])
            ->with('success', 'Berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Farmer $farmer, Land $land, Garden $garden)
    {
        if (auth()->user()->role == 'farmer' && auth()->user()->farmer->id != $farmer->id) {
            return back()->with("Anda tidak dapat mengakses data ini");
        }

        if (!Gate::allows('update-garden', $garden)) {
            return abort(403);
        }

        if ($farmer->id != $land->farmer_id) {
            return back()->with('failed', 'Lahan tidak sama dengan yang ada di petani');
        }

        $garden->load([
            'device',
        ]);

        $rsc_garden = RscGarden::query()
            ->with('rscGardenTelemetries')
            ->withAvg('rscGardenTelemetries as avg_n', 'samples->n')
            ->withAvg('rscGardenTelemetries as avg_p', 'samples->p')
            ->withAvg('rscGardenTelemetries as avg_k', 'samples->k')
            ->where('garden_id', $garden->id)
            ->orderByDesc('created_at')
            ->first();

        return view('bitanic.garden.show', [
            'garden' => $garden,
            'land' => $land,
            'farmer' => $farmer,
            'rsc_garden' => $rsc_garden
        ]);
    }

    public function edit(Farmer $farmer, Land $land, Garden $garden)
    {
        if (!Gate::allows('update-garden', $garden)) {
            return abort(403);
        }

        if ($farmer->id != $land->farmer_id) {
            return back()->with('failed', 'Lahan tidak sama dengan yang ada di petani');
        }

        $data['crops'] = Crop::get();
        $data['devices'] = Device::query()
            ->where('farmer_id', $farmer->id)
            ->withCount('gardens')
            // ->where(function($query){
            //     $query->doesntHave('garden')
            //     ->orWhereDoesntHave('garden', function($gardens){
            //         $gardens->whereIn('harvest_status', [0,1,2]);
            //     });
            // })
            // ->where('gardens_count', '<', 4)
            ->having('gardens_count', '<', 4)
            ->pluck('device_series', 'id');

        $data['lands'] = Land::query()
            ->where('farmer_id', $farmer->id)
            ->pluck('name', 'id');

        $data['farmer'] = $farmer;

        $garden->load(['land:id,farmer_id,latitude,longitude,altitude,polygon,area,image,address,color']);

        $data['garden'] = $garden;
        $data['land'] = $land;

        return view('bitanic.garden.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Farmer $farmer, Land $land, $garden)
    {
        if ($farmer->id != $land->farmer_id) {
            return back()->with('failed', 'Lahan tidak sama dengan yang ada di petani');
        }

        $garden = Garden::query()
            ->whereHas('land', function($land)use($farmer){
                $land->where('farmer_id', $farmer->id);
            })
            ->find($garden);

        if (!$garden) {
            return back()->withErrors([
                'kebun' => [
                    'Kebun tidak ditemukan'
                ]
            ]);
        }

        if (!Gate::allows('update-garden', $garden)) {
            return abort(403);
        }

        // $request['polygon'] = $request->polygon ? json_decode($request->polygon) : [];
        $v = Validator::make($request->all(), [
            // 'land_id'           => 'required|exists:lands,id',
            'gardes_type'       => 'required|in:hidroponik,aquaponik,tradisional,vertical,green_house',
            'category'          => 'required|string|in:urban,rural',
            'date_created'      => 'required|date',
            // 'estimated_harvest' => 'required|date',
            // 'crop_id'           => 'required|exists:crops,id',
            'device_id'         => 'required|exists:devices,id',
            'pipa'              => 'nullable|integer|min:0',
            'lubang_pipa'       => 'nullable|integer|min:0',
            'length'            => 'nullable|numeric',
            'width'             => 'nullable|numeric',
            'height'            => 'nullable|numeric',
            'fish_type'         => 'nullable|string|max:255',
            'name'              => ['required', 'string', 'max:255'],
            'area'              => ['required', 'numeric', 'min:0'],
            'color'             => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'image'             => 'nullable|image|mimes:jpg,png,jpeg|max:10240',
            'polygon'           => 'required|json',
        ]);

        if ($v->fails()) {
            return back()->withErrors($v);
        }

        $polygon = json_decode($request->polygon, true);

        $v = Validator::make([
            'polygon' => $polygon
        ], [
            'polygon' => ['required', 'array'],
            'polygon.*' => ['required', 'array'],
            'polygon.*.*' => 'required|regex:/^(-?\d+(\.\d+)?)$/',
        ]);

        if ($v->fails()) {
            return back()->withErrors($v)->withInput();
        }

        $query = [
            'color' => substr($request->color, 1),
            'polygon' => $polygon,
        ];

        if ($request->file('image')) {
            $image = image_intervention($request->file('image'), 'bitanic-photo/gardens/', 4 / 3);

            if (File::exists(public_path($garden->image))) {
                File::delete(public_path($garden->image));
            }
            $query = array_merge($query, [
                'picture' => $image,
            ]);

            $picture_new = ['image' => 'Updated'];
            $picture_old = ['image' => 'Old'];
        }

        if (in_array($request->gardes_type, ['hidroponik','aquaponik'])) {
            $query = array_merge($query, [
                'levels' => $request->pipa ?? null,
                'holes' => $request->lubang_pipa ?? null
            ]);

            if ($request->gardes_type == 'aquaponik') {
                $query = array_merge($query, [
                    'length' => $request->length ?? null,
                    'width' => $request->width ?? null,
                    'height' => $request->height ?? null,
                    'fish_type' => $request->fish_type ?? null
                ]);
            }
        }

        $garden->update($request->only([
            'gardes_type',
            'category',
            'date_created',
            // 'land_id',
            'name',
            'area',
            'device_id',
        ]) + $query);

        return redirect()
            ->route('bitanic.garden.index', [
                'farmer' => $farmer->id,
                'land' => $land->id
            ])
            ->with('success', 'Berhasil disimpan!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($farmer, $land, $id)
    {
        $garden = Garden::find($id);

        if (!$garden) {
            return response()->json([
                'messages' => (object) [
                    'text' => ["Data kebun tidak ditemukan"]
                ]
            ], 404);
        }

        if (!Gate::allows('update-garden', $garden)) {
            return response()->json([
                'messages' => (object) [
                    'warning' => ["Anda tidak dapat mengakses data ini"]
                ]
            ], 403);
        }

        if (\File::exists(public_path($garden->picture))) {
            \File::delete(public_path($garden->picture));
        }

        $garden->delete();

        return response()->json([
            'message' => "Berhasil"
        ], 200);
    }

    public function changeStatus(Request $request, Farmer $farmer, Land $land, Garden $garden)
    {
        if ($farmer->id != $land->farmer_id) {
            return back()->with('failed', 'Lahan tidak sama dengan yang ada di petani');
        }

        if (!Gate::allows('update-garden', $garden)) {
            return response()->json([
                'messages' => (object) [
                    'warning' => ["Anda tidak dapat mengakses data ini"]
                ]
            ], 403);
        }

        $v = Validator::make($request->all(), [
            'status' => 'required|in:0,1,2,3',
        ]);

        if ($v->fails()) {
            return response()->json([
                'messages' => $v->errors()
            ], 400);
        }

        $garden->harvest_status = $request->input('status');
        $garden->save();

        return response()->json([
            'message' => "Berhasil disimpan"
        ], 200);
    }

    public function getGardenPolygon($id)
    {
        $garden = Garden::query()
            ->with([
                'land:id,farmer_id,latitude,longitude,polygon'
            ])
            ->find($id);

        if (!$garden) {
            return response()->json([
                'messages' => (object) [
                    'text' => ["Data kebun tidak ditemukan"]
                ]
            ], 404);
        }

        if (auth()->user()->role == 'farmer' && auth()->user()->farmer->id === $garden->land->farmer_id) {
            return response()->json([
                'messages' => (object) [
                    'warning' => ["Anda tidak dapat mengakses data ini"]
                ]
            ], 403);
        }

        return response()->json([
            'data' => (object) [
                'polygon' => $garden->land->polygon,
                'lat' => $garden->land->latitude,
                'lng' => $garden->land->longitude
            ],
            'message' => "Berhasil"
        ], 200);
    }

    public function getGardensPolygon($farmer)
    {
        if (auth()->user()->role == 'farmer' && auth()->user()->farmer->id === $farmer) {
            return response()->json([
                'messages' => (object) [
                    'warning' => ["Anda tidak dapat mengakses data ini"]
                ]
            ], 403);
        }

        $gardens = Garden::query()
            ->select(['id', 'land_id', 'name', 'polygon', 'color'])
            ->with([
                'land:id,farmer_id,latitude,longitude,name,polygon,color'
            ])
            ->whereHas('land', function($land)use($farmer){
                $land->where('farmer_id', $farmer);
            })
            ->get();

        return response()->json([
            'gardens' => $gardens,
            'message' => 'Data kebun'
        ], 200);
    }

    public function getListFertilization($garden)
    {
        $data = Garden::query()
            ->with([
                'fertilizations' => function($query){
                    $query->orderBy('id', 'desc');
                }
            ])
            ->find($garden);

        if (!$garden) {
            return response()->json([
                'messages' => (object) [
                    'text' => ["Data kebun tidak ditemukan"]
                ]
            ], 404);
        }

        return response()->json([
            'fertilizations' => $data->fertilizations,
            'message' => "Fertilizations list"
        ], 200);
    }

    public function getFertilization($garden)
    {
        $data = Garden::query()
            ->with(['unfinished_fertilization', 'device'])
            ->find($garden);

        if (!$garden) {
            return response()->json([
                'messages' => (object) [
                    'text' => ["Data kebun tidak ditemukan"]
                ]
            ], 404);
        }

        return response()->json([
            'fertilization' => $data->unfinished_fertilization,
            'device' => $data->device,
            'message' => "Fertilization data"
        ], 200);
    }

    public function getSchedules($garden)
    {

        $data = Garden::query()
            ->with(['unfinished_fertilization.schedule', 'device'])
            ->find($garden);

        if (!$data) {
            return response()->json([
                'message' => "Data kebun tidak ditemukan!"
            ], 404);
        }

        $fertilization = $data->unfinished_fertilization;

        $telemetri = [];

        if ($fertilization) {
            $hari_pengiriman = now()->parse($fertilization->created_at)->format('N');
            $collect = collect($fertilization->schedule);
            $group = $collect->groupBy('week');

            foreach ($group->all() as $week => $val) {
                if ($week > 0) {
                    $telemetri[$week] = [];
                    foreach ($val as $schedule) {
                        $hari = $schedule->hari + 1;
                        $diff = $hari - $hari_pengiriman;
                        if ($diff >= 0) {
                            $tgl = now()->parse($fertilization->created_at)->addDays($diff)->addWeeks($schedule->week - 1)->format('Y/m/d');
                        } else {
                            $tgl = now()->parse($fertilization->created_at)->subDays(abs($diff))->addWeeks($schedule->week - 1)->format('Y/m/d');
                        }

                        $waktu = '-';
                        $end_time = ($schedule->end_time) ? today()->parse($schedule->end_time)->format('H:i:s') : "unfinished";

                        switch ($schedule->type) {
                            case 'manual_motor_1':
                                $waktu = today()->parse($schedule->start_time)->format('H:i:s')
                                    . " - "
                                    . $end_time;
                                break;
                            case 'manual_motor_2':
                                $waktu = today()->parse($schedule->start_time)->format('H:i:s')
                                    . " - "
                                    . $end_time;
                                break;

                            default:
                                $waktu = today()->parse($fertilization->set_time)->format('H:i:s')
                                    . " - "
                                    . today()->parse($fertilization->set_time)->addMinutes($fertilization->set_minute)->format('H:i:s');
                                break;
                        }

                        $telemetri[$week][] = (object)[
                            'week' => $schedule->week,
                            'day' => $schedule->day,
                            'date' => $tgl,
                            'time' => $waktu,
                            'type' => str_replace('_', " ", $schedule->type)
                        ];
                    }
                }
            }
        } else {
            $scheduleWhere = [];

            $scheduleWhere[] = ['garden_id', $garden];

            if ($data->device) {
                $scheduleWhere[] = ['device_id', $data->device->id];
            }

            $scheduleWhere[] = ['week', 0];
            $scheduleWhere[] = ['type', '<>', 'schedule'];
            $schedule = FertilizationSchedule::query()
                ->where($scheduleWhere)
                ->orderBy('id', 'desc')
                ->limit('10')
                ->get();

            $collect = collect($schedule);
            $group = $collect->groupBy('week');

            foreach ($group->all() as $week => $val) {
                $telemetri[$week] = [];
                foreach ($val as $jadwal) {
                    $tgl = now()->parse($jadwal->start_time)->format('Y/m/d');

                    $waktu = '-';
                    $end_time = ($jadwal->end_time) ? today()->parse($jadwal->end_time)->format('H:i:s') : "unfinished";

                    switch ($jadwal->type) {
                        case 'manual_motor_1':
                            $waktu = today()->parse($jadwal->start_time)->format('H:i:s')
                                . " - "
                                . $end_time;
                            break;
                        case 'manual_motor_2':
                            $waktu = today()->parse($jadwal->start_time)->format('H:i:s')
                                . " - "
                                . $end_time;
                            break;

                        default:
                            $waktu = '-';
                            break;
                    }

                    $telemetri[$week][] = (object)[
                        'week' => $jadwal->week,
                        'day' => $jadwal->day,
                        'date' => $tgl,
                        'time' => $waktu,
                        'type' => str_replace('_', " ", $jadwal->type)
                    ];
                }
            }
        }

        $data['schedules'] = $telemetri;
        $data['message'] = "Data telemetri perangkat";

        return response()->json($data, 200);
    }

    public function setStatusMotor(Request $request, $garden)
    {
        $mqtt = $request->query('mqtt', 1);

        $mqtt = is_numeric($mqtt) ? $mqtt : 0;

        $garden = Garden::query()
            ->with('device')
            ->find($garden);

        if (!$garden) {
            return response()->json([
                'messages' => (object) [
                    'text' => ["Data kebun tidak ditemukan"]
                ]
            ], 404);
        }

        $device = $garden->device;

        if (!$device) {
            return response()->json([
                'messages' => (object) [
                    'text' => ["Kebun anda tidak memiliki perangkat!"]
                ]
            ], 404);
        }

        if (! Gate::allows('update-device', $device)) {
            return response()->json([
                'messages' => (object) [
                    'warning' => ["Anda tidak dapat mengakses data ini"]
                ]
            ], 403);
        }

        $v = Validator::make($request->all(), [
            'motor' => 'required|integer|in:1,2',
            'status' => "required|integer|in:0,1"
        ], [
            'motor.required' => "Motor Pompa harus diisi!",
            'status.required' => "Status Motor Pompa harus diisi!",
            'motor.in' => "Motor Pompa tidak diketahui!",
            'status.in' => "Status Pompa tidak diketahui!"
        ]);

        if ($v->fails()) {
            return response()->json([
                'messages' => $v->errors(),
                'status' => 400
            ], 400);
        }

        $status = $request->status;
        $motor = $request->motor;
        $topic = "bitanic/$device->device_series";

        try {
            $irrigations = $device->irrigation ?? [];
            $vertigations = $device->vertigation ?? [];

            switch ($motor) {
                case 1:
                    $checkOpen = collect($irrigations)->first(function ($value, $key) {
                        return $value['status'] == 1;
                    });
                    $pe = $irrigations ? true : false;
                    break;
                case 2:
                    $checkOpen = collect($vertigations)->first(function ($value, $key) {
                        return $value['status'] == 1;
                    });
                    $pe = $vertigations ? true : false;
                    break;

                default:
                    $checkOpen = null;
                    $pe = false;
                    break;
            }

            if ($device->type == 2) {
                if ($status == 1 && $pe == true && $checkOpen == null) {
                    return response()->json([
                        'messages' => [
                            'errors' => ['Tidak ada PE yang terbuka']
                        ]
                    ], 400);
                }
            }


            MQTT::publish($topic, "MOTOR$motor,$status,*");

            if ($device->type == 2 && $status == 0) {
                if ($motor == 1 && $device->irrigation) {
                    if (isset($device->irrigation[0])) {
                        MQTT::publish($topic, "PE" . $device->irrigation[0]['id'] . "MOTOR" . $motor . ",0,*");
                    }
                    if (isset($device->irrigation[1])) {
                        MQTT::publish($topic, "PE" . $device->irrigation[1]['id'] . "MOTOR" . $motor . ",0,*");
                    }
                    if (isset($device->irrigation[2])) {
                        MQTT::publish($topic, "PE" . $device->irrigation[2]['id'] . "MOTOR" . $motor . ",0,*");
                    }
                    if (isset($device->irrigation[3])) {
                        MQTT::publish($topic, "PE" . $device->irrigation[3]['id'] . "MOTOR" . $motor . ",0,*");
                    }
                    if (isset($device->irrigation[4])) {
                        MQTT::publish($topic, "PE" . $device->irrigation[4]['id'] . "MOTOR" . $motor . ",0,*");
                    }
                    if (isset($device->irrigation[5])) {
                        MQTT::publish($topic, "PE" . $device->irrigation[5]['id'] . "MOTOR" . $motor . ",0,*");
                    }
                } elseif ($motor == 2 && $device->vertigation) {
                    if (isset($device->vertigation[0])) {
                        MQTT::publish($topic, "PE" . $device->vertigation[0]['id'] . "MOTOR" . $motor . ",0,*");
                    }
                    if (isset($device->vertigation[1])) {
                        MQTT::publish($topic, "PE" . $device->vertigation[1]['id'] . "MOTOR" . $motor . ",0,*");
                    }
                    if (isset($device->vertigation[2])) {
                        MQTT::publish($topic, "PE" . $device->vertigation[2]['id'] . "MOTOR" . $motor . ",0,*");
                    }
                    if (isset($device->vertigation[3])) {
                        MQTT::publish($topic, "PE" . $device->vertigation[3]['id'] . "MOTOR" . $motor . ",0,*");
                    }
                    if (isset($device->vertigation[4])) {
                        MQTT::publish($topic, "PE" . $device->vertigation[4]['id'] . "MOTOR" . $motor . ",0,*");
                    }
                    if (isset($device->vertigation[5])) {
                        MQTT::publish($topic, "PE" . $device->vertigation[5]['id'] . "MOTOR" . $motor . ",0,*");
                    }
                }
            }

            MQTT::publish($topic, 'GETDATA,*');

            // event(new \App\Events\PumpEvent($device->id, $pompa_irigasi, $pompa_vertigasi));

            return response()->json([
                'message' => 'Command berhasil dikirim ke alat! Silahkan check alat anda.',
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getGardens() : JsonResponse {
        $gardens = Garden::query()
            ->with([
                'land:id,name'
            ])
            ->when(auth()->user()->role != 'admin', function($query){
                return $query->whereHas('land', function($query){
                    $query->where('farmer_id', auth()->user()->farmer->id);
                });
            })
            ->when(
                (
                    auth()->user()->role == 'admin' &&
                    auth()->user()->city_id != null
                ),
                function($query){
                    return $query->whereHas(
                        'land.farmer.user.subdistrict.district',
                        function($query){
                            $query->where('city_id', auth()->user()->city_id);
                        }
                    );
                }
            )
            ->get(['id', 'land_id', 'gardes_type', 'name']);

        return response()->json([
            'message' => 'Data kebun',
            'gardens' => $gardens
        ]);
    }
}
