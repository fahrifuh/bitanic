<?php

namespace App\Http\Controllers\APi\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Garden;
use App\Models\Land;
use App\Models\RscGarden;
use App\Models\RscGardenTelemetry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use PhpMqtt\Client\Facades\MQTT;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GardenController extends Controller
{
    public function farmerId() {
        return auth()->user()->farmer->id;
    }

    public function listKebun()
    {
        $farmer_id = $this->farmerId();
        $gardens = Garden::query()
            ->with([
                'device',
                'land:id,farmer_id,name,area,address,latitude,longitude,altitude,polygon'
            ])
            ->whereHas('land', function($land)use($farmer_id){
                $land->where('farmer_id', $farmer_id);
            })
            ->doesntHave('harvest_produce')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'data' => $gardens,
            'message' => "List data kebun",
            'status' => 200
        ], 200);
    }

    public function detailKebun($id)
    {
        $farmer_id = auth()->user()->farmer->id;
        $kebun = Garden::query()
            ->select([
                'id',
                'category',
                'land_id',
                'date_created',
                'gardes_type',
                'harvest_status',
                'estimated_harvest',
                'temperature',
                'levels',
                'holes',
                'length',
                'width',
                'height',
                'fish_type',
                'temperature',
                'moisture',
                'device_id',
                'polygon',
                'color',
                'area',
                'picture',
                'name',
                'is_indoor',
            ])
            ->with([
                'currentCommodity.crop:id,crop_name,picture',
                'device:id,device_series,status_motor_1,status_motor_2,irrigation,vertigation,status,type,category',
                'invected.pest',
                'land:id,farmer_id,name,area,image,address,latitude,longitude,altitude,polygon,color'
            ])
            ->whereHas('land', function($land)use($farmer_id){
                $land->where('farmer_id', $farmer_id);
            })
            ->firstWhere('id', $id);

        if (!$kebun) {
            return response()->json([
                'message' => 'Data tidak ditemukan',
                'status' => 404
            ], 404);
        }

        $rsc_garden = RscGarden::query()
            ->with('rscGardenTelemetries')
            ->withAvg('rscGardenTelemetries as avg_n', 'samples->n')
            ->withAvg('rscGardenTelemetries as avg_p', 'samples->p')
            ->withAvg('rscGardenTelemetries as avg_k', 'samples->k')
            ->withAvg('rscGardenTelemetries as avg_ec', 'samples->ec')
            ->withAvg('rscGardenTelemetries as avg_ambient_temp', 'samples->ambient_temperature')
            ->withAvg('rscGardenTelemetries as avg_soil_temp', 'samples->soil_temperature')
            ->withAvg('rscGardenTelemetries as avg_ambient_humidity', 'samples->ambient_humidity')
            ->withAvg('rscGardenTelemetries as avg_soil_moisture', 'samples->soil_moisture')
            ->where('garden_id', $kebun->id)
            ->orderByDesc('created_at')
            ->first();

        return response()->json([
            'data' => $kebun,
            'rsc_garden' => $rsc_garden,
            'message' => "Detail data kebun",
            'status' => 200
        ], 200);
    }

    public function getAvailableLands(Request $request) : JsonResponse {
        $land_id = $request->query('id', null);

        $lands = Land::query()
            ->where('farmer_id', auth()->user()->farmer->id)
            ->where(function($query)use($land_id){
                $query->whereDoesntHave('gardens', function($gardens){
                    $gardens->whereIn('harvest_status', [0,1,2]);
                });

                if ($land_id && is_numeric($land_id)) {
                    $query->orWhere('id', $land_id);
                }
            })
            ->get(['name', 'id']);

        return response()->json([
            'lands' => $lands,
            'status' => 200
        ], 200);
    }

    public function getAvailableDevices(Request $request) : JsonResponse {
        $device_id = $request->query('id', null);
        $farmer_id = $this->farmerId();

        $devices = Device::query()
            ->where('farmer_id', $farmer_id)
            ->where(function($query)use($device_id){
                $query->whereDoesntHave('garden', function($garden){
                    $garden->whereIn('harvest_status', [0,1,2]);
                });

                if ($device_id && is_numeric($device_id)) {
                    $query->orWhere('id', $device_id);
                }
            })
            ->get(['id', 'device_series']);

        return response()->json([
            'devices' => $devices,
            'status' => 200
        ], 200);
    }

    public function store(Request $request)
    {
        $farmer_id = auth()->user()->farmer->id;

        $v = Validator::make($request->all(),[
            'land_id' => ['required', 'integer', 'min:0'],
            'land_copy'         => 'required|in:0,1',
            'category' => 'required|string|in:urban,rural',
            'jenis'  => 'required|string|in:hidroponik,aquaponik,tradisional,vertical,green_house',
            'tgl_dibuat'    => 'required|date',
            // 'estimasi_panen'    => 'required|date',
            // 'tanaman_id'    => 'required|exists:crops,id',
            'perangkat_id'    => 'required|integer|min:0',
            'jml_pipa'            => 'nullable|integer|min:0',
            'jml_lubang'       => 'nullable|integer|min:0',
            'panjang'            => 'nullable|numeric',
            'lebar'             => 'nullable|numeric',
            'tinggi'            => 'nullable|numeric',
            'ikan'         => 'nullable|string|max:255',
            'is_indoor'         => 'required|integer|in:0,1',
            'name'              => ['required', 'string', 'max:255'],
            'area'              => ['required', 'numeric', 'min:0'],
            'color'             => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'image'             => 'required|image|mimes:jpg,png,jpeg|max:10240',
            'polygon'           => [
                Rule::requiredIf(function () use ($request) {
                    return $request->land_copy == 0;
                })
            ],
        ]);

        if ($v->fails()) {
            return response()->json([
                'messages' => $v->errors(),
                'status' => 400
            ], 400);
        }

        $land = Land::query()
            ->find($request->land_id);

        if (!$land) {
            return response()->json([
                'errors' => [
                    'land_id' => ['Lahan tidak ditemukan']
                ]
            ], 422);
        }

        $polygon = $land->polygon;

        if ($request->land_copy == 0) {
            $polygon = $request->polygon;

            if (is_string($request->polygon) && !$this->isJson($request->polygon)) {
                return response()->json([
                    'messages' => [
                        'polygon' => ['Harap masukan format polygon yang benar']
                    ],
                    'status' => 400
                ], 400);
            } else {
                $polygon = json_decode($request->polygon, true);
            }

            $v = Validator::make([
                'polygon' => $polygon
            ], [
                'polygon' => ['required', 'array'],
                'polygon.*' => ['required', 'array'],
                'polygon.*.*' => 'required|regex:/^(-?\d+(\.\d+)?)$/',
            ]);

            if ($v->fails()) {
                return response()->json([
                    'messages' => $v->errors(),
                    'status' => 400
                ], 400);
            }
        }

        // $land_count = Land::query()
        //     ->where('farmer_id', $farmer_id)
        //     ->where('id', $request->land_id)
        //     ->whereDoesntHave('gardens', function($gardens){
        //         $gardens->whereIn('harvest_status', [0,1,2]);
        //     })
        //     ->count();

        // if ($land_count != 1) {
        //     return response()->json([
        //         'messages' => [
        //             'land_id' => [
        //                 'Data lahan tidak ditemukan / lahan sedang digunakan / bukan lahan anda!'
        //             ]
        //         ]
        //     ], 404);
        // }

        $device = Device::query()
            ->where('farmer_id', $farmer_id)
            ->where('id', $request->perangkat_id)
            // ->whereDoesntHave('garden', function($gardens){
            //     $gardens->whereIn('harvest_status', [0,1,2]);
            // })
            ->first();

        if (!$device) {
            return response()->json([
                'messages' => [
                    'land_id' => [
                        'Data lahan tidak ditemukan / lahan sedang digunakan / bukan lahan anda!'
                    ]
                ]
            ], 404);
        }

        $query = [
            'picture' => image_intervention($request->file('image'), 'bitanic-photo/gardens/', 4 / 3),
            'land_id' => $request->land_id,
            'gardes_type' => $request->jenis,
            'category' => $request->category,
            'date_created' => $request->tgl_dibuat,
            // 'estimated_harvest' => $request->estimasi_panen,
            // 'crop_id' => $request->tanaman_id,
            'harvest_status' => 0,
            'color' => substr($request->color, 1),
            'polygon' => $polygon,
            'estimated_harvest' => now(),
            'name' => $request->name,
            'area' => $request->area,
            'is_indoor' => $request->is_indoor,
            'device_id' => $device->id,
        ];

        if (in_array($request->gardes_type, ['hidroponik','aquaponik'])) {
            $query = array_merge($query, [
                'levels' => $request->jml_pipa ?? null,
                'holes' => $request->jml_lubang ?? null
            ]);

            if ($request->gardes_type == 'aquaponik') {
                $query = array_merge($query, [
                    'length' => $request->panjang ?? null,
                    'width' => $request->lebar ?? null,
                    'height' => $request->tinggi ?? null,
                    'fish_type' => $request->ikan ?? null
                ]);
            }
        }

        $garden = Garden::create($query);

        // $device->garden()->associate($garden);
        // $device->save();

        return response()->json([
            'message' => "Data kebun berhasil ditambahkan!",
            'garden' => [
                'id' => $garden->id
            ],
            'status' => 200
        ], 200);
    }

    public function changeGardenStatus(Request $request, $id)
    {
        $farmer_id = $this->farmerId();
        $garden = Garden::query()
            ->whereHas('land', function($land)use($farmer_id){
                $land->where('farmer_id', $farmer_id);
            })
            ->firstWhere('id', $id);

        if (!$garden) {
            return response()->json([
                'message' => 'Data tidak ditemukan',
                'status' => 404
            ], 404);
        }

        $v = Validator::make($request->all(),[
            'status'  => 'required|in:0,1,2,3'
        ]);

        if ($v->fails()) {
            return response()->json([
                'messages' => $v->errors(),
                'status' => 400
            ], 400);
        }

        $garden->update([
            'harvest_status' => $request->status
        ]);

        return response()->json([
            'message' => "Status sudah diubah",
            'status' => 200
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $farmer_id = $this->farmerId();

        $garden = Garden::query()
            ->whereHas('land', function($land)use($farmer_id){
                $land->where('farmer_id', $farmer_id);
            })
            ->firstWhere('id', $id);

        if (!$garden) {
            return response()->json([
                'message' => 'Data tidak ditemukan',
                'status' => 404
            ], 404);
        }

        $v = Validator::make($request->all(),[
            'land_id' => ['required', 'integer', 'min:0'],
            'jenis'  => 'required|string|in:hidroponik,aquaponik,tradisional,vertical,green_house',
            'category' => 'required|string|in:urban,rural',
            'tgl_dibuat'    => 'required|date',
            // 'estimasi_panen'    => 'required|date',
            // 'tanaman_id'    => 'required|exists:crops,id',
            'perangkat_id'    => 'required|exists:devices,id',
            'jml_pipa'            => 'nullable|integer',
            'jml_lubang'       => 'nullable|integer',
            'panjang'            => 'nullable|numeric',
            'lebar'             => 'nullable|numeric',
            'tinggi'            => 'nullable|numeric',
            'ikan'         => 'nullable|string|max:255',
            'is_indoor'         => 'required|integer|in:0,1',
            'name'              => ['required', 'string', 'max:255'],
            'area'              => ['required', 'numeric', 'min:0'],
            'color'             => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'image'             => 'nullable|image|mimes:jpg,png,jpeg|max:10240',
            'polygon'           => [
                Rule::requiredIf(function () use ($request) {
                    return $request->land_copy == 0;
                })
            ],
        ]);

        if ($v->fails()) {
            return response()->json([
                'messages' => $v->errors(),
                'status' => 400
            ], 400);
        }

        $land = Land::query()
            ->find($request->land_id);

        if (!$land) {
            return response()->json([
                'errors' => [
                    'land_id' => ['Lahan tidak ditemukan']
                ]
            ], 422);
        }

        $polygon = $land->polygon;

        if ($request->land_copy == 0) {
            $polygon = $request->polygon;

            if (is_string($request->polygon) && !$this->isJson($request->polygon)) {
                return response()->json([
                    'messages' => [
                        'polygon' => ['Harap masukan format polygon yang benar']
                    ],
                    'status' => 400
                ], 400);
            } else {
                $polygon = json_decode($request->polygon, true);
            }

            $v = Validator::make([
                'polygon' => $polygon
            ], [
                'polygon' => ['required', 'array'],
                'polygon.*' => ['required', 'array'],
                'polygon.*.*' => 'required|regex:/^(-?\d+(\.\d+)?)$/',
            ]);

            if ($v->fails()) {
                return response()->json([
                    'messages' => $v->errors(),
                    'status' => 400
                ], 400);
            }
        }

        // $land_count = Land::query()
        //     ->where('farmer_id', $farmer_id)
        //     ->where('id', $request->land_id)
        //     ->whereDoesntHave('gardens', function($gardens)use($id){
        //         $gardens->whereIn('harvest_status', [0,1,2])->where('id', '<>', $id);
        //     })
        //     ->count();

        // if ($land_count != 1) {
        //     return response()->json([
        //         'messages' => [
        //             'land_id' => [
        //                 'Data lahan tidak ditemukan / lahan sedang digunakan / bukan lahan anda!'
        //             ]
        //         ],
        //         'data' => $request->all()
        //     ], 404);
        // }

        $device = Device::query()
            ->where('farmer_id', $farmer_id)
            ->where('id', $request->perangkat_id)
            // ->where(function($query)use($garden){
            //     $query->where('garden_id', $garden->id)->orDoesntHave('garden')
            //     ->orWhereDoesntHave('garden', function($gardens){
            //         $gardens->whereIn('harvest_status', [0,1,2]);
            //     });
            // })
            ->first();

        if (!$device) {
            return response()->json([
                'messages' => [
                    'land_id' => [
                        'Data perangkat tidak ditemukan / perangkat sedang digunakan / bukan perangkat anda!'
                    ]
                ]
            ], 404);
        }

        $query = [
            'land_id' => $request->land_id,
            'gardes_type' => $request->jenis,
            'category' => $request->category,
            'date_created' => $request->tgl_dibuat,
            // 'estimated_harvest' => $request->estimasi_panen,
            // 'crop_id' => $request->tanaman_id
            'harvest_status' => 0,
            'color' => substr($request->color, 1),
            'polygon' => $polygon,
            'estimated_harvest' => now(),
            'name' => $request->name,
            'area' => $request->area,
            'is_indoor' => $request->is_indoor,
            'device_id' => $device->id,
        ];

        if ($request->file('image')) {
            $image = image_intervention($request->file('image'), 'bitanic-photo/gardens/', 4 / 3);

            if (File::exists(public_path($garden->image))) {
                File::delete(public_path($garden->image));
            }
            $query = array_merge($query, [
                'picture' => $image,
            ]);
        }

        if (in_array($request->jenis, ['hidroponik','aquaponik'])) {
            $query = array_merge($query, [
                'levels' => $request->jml_pipa ?? null,
                'holes' => $request->jml_lubang ?? null
            ]);

            if ($request->jenis == 'aquaponik') {
                $query = array_merge($query, [
                    'length' => $request->panjang ?? null,
                    'width' => $request->lebar ?? null,
                    'height' => $request->tinggi ?? null,
                    'fish_type' => $request->ikan ?? null
                ]);
            }
        }

        $garden->update($query);

        return response()->json([
            'message' => 'Berhasil disimpan',
            'status' => 200
        ], 200);
    }

    public function setStatusMotor(Request $request, $id)
    {
        $garden = Garden::query()
            ->with('device')
            ->find($id);

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

            if ($device->type == 2 && $status == 1 && $pe == true && $checkOpen == null) {
                return response()->json([
                    'errors' => [
                        'messages' => ['Tidak ada PE yang terbuka']
                    ]
                ], 400);
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = auth()->user();

        $garden = Garden::query()
            ->find($id);

        if (!$garden) {
            return response()->json([
                'messages' => (object) [
                    'errors' => ["Data kebun tidak ditemukan"]
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

        $garden->delete();

        return response()->json([
            'message' => 'Berhasil dihapus.',
            'status' => 200
        ], 200);
    }

    public function setPeStatus(Request $request, $id) : JsonResponse {
        $garden = Garden::with(['device'])->findOrFail($id);

        if (!Gate::allows('update-garden', $garden)) {
            return response()->json([
                'errors' => (object) [
                    'authorize' => ["Anda tidak dapat mengakses data ini"]
                ]
            ], 403);
        }

        $request->validate([
            'status' => ['required', 'in:0,1'],
            'pe' => ['required', 'in:1,2,3,4,5,6'],
            'pump' => ['required', 'in:1,2']
        ]);

        $device = $garden->device;

        $topic = "bitanic/$device->device_series";
        $command = "PE" . $request->pe . "MOTOR" . $request->pump . ",$request->status,*";

        try {
            MQTT::publish($topic, $command);

            return response()->json([
                'message' => "Berhasil mengirim command ke alat. Silahkan check alat anda!"
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function rscGardens(Garden $garden) : JsonResponse {
        if (!Gate::allows('update-garden', $garden)) {
            return response()->json([
                'errors' => (object) [
                    'authorize' => ["Anda tidak dapat mengakses data ini"]
                ]
            ], 403);
        }

        $rscGardens = RscGarden::query()
            ->with([
                'device:id,device_series,device_name'
            ])
            ->where('garden_id', $garden->id)
            ->latest('created_at')
            ->paginate(15);

        return response()
            ->json($rscGardens);
    }

    public function recentRscGardenTelemetries(Garden $garden) : JsonResponse {
        if (!Gate::allows('update-garden', $garden)) {
            return response()->json([
                'errors' => (object) [
                    'authorize' => ["Anda tidak dapat mengakses data ini"]
                ]
            ], 403);
        }

        $rscGardenTelemetries = RscGardenTelemetry::query()
            ->whereHas('rscGarden', function(Builder $query)use($garden){
                $query->where('garden_id', $garden->id)->latest('created_at');
            })
            ->latest('created_at')
            ->paginate(15);

        return response()
            ->json($rscGardenTelemetries);
    }

    private function isJson($string) {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
