<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceSpecification;
use App\Models\Farmer;
use App\Models\Fertilization;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use PhpMqtt\Client\Facades\MQTT;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['data'] = Device::with([
            'specification',
            'farmer' => function($fm){
                $fm->select('id', 'full_name');
            }
        ])
        ->when(auth()->user()->role == 'farmer', function($query, $role){
            return $query->where('farmer_id', auth()->user()->farmer->id);
        })
        ->when((auth()->user()->role == 'admin' && auth()->user()->city_id != null), function($query, $role){
            return $query->whereHas('farmer.user.subdistrict.district', function($query2){
                    $query2->where('city_id', auth()->user()->city_id);
                })
                ->orDoesntHave('farmer');
        })
        ->when(request()->query('search'), function($query, $role){
            $search = request()->query('search');
            return $query->where(function($query)use($search){
                $query->where('device_series', $search)
                    ->orWhereHas('farmer', function($fm)use($search){
                        $fm->where('full_name', 'LIKE', '%'.$search.'%');
                    });
            });
        })
        ->when((is_numeric(request()->query('pemilik')) && in_array(request()->query('pemilik'), [0, 1])), function($query, $role){
            $pemilik = request()->query('pemilik');
            return $query->where(function($query2)use($pemilik){
                if ($pemilik == 1) {
                    $query2->whereNotNull('farmer_id');
                } else {
                    $query2->whereNull('farmer_id');
                }
            });
        })
        ->when((request()->query('aktivasi') && in_array(request()->query('aktivasi'), ['sudah', 'belum'])), function($query, $role){
            $aktivasi = request()->query('aktivasi');
            return $query->where(function($query2)use($aktivasi){
                if ($aktivasi == 'sudah') {
                    $query2->whereNotNull('activate_date');
                } else {
                    $query2->whereNull('activate_date');
                }
            });
        })
        ->when((request()->query('tipe') && in_array(request()->query('tipe'), ['controller', 'tongkat'])), function($query, $role){
            $tipe = request()->query('tipe');
            return $query->where(function($query2)use($tipe){
                $query2->where('category', $tipe);
            });
        })
        ->orderBy('device_series')
        ->paginate(10)
        ->withQueryString();

        // if (auth()->user()->role == 'farmer') {
        //     $devices = $devices->where(function($query){
        //         $query->where('farmer_id', auth()->user()->farmer->id);
        //     });
        // }

        // if (auth()->user()->role == 'admin' && auth()->user()->city_id != null) {
        //     $devices = $devices->whereHas('farmer.user.subdistrict.district', function($query){
        //         $query->where('city_id', auth()->user()->city_id);
        //     })
        //     ->orDoesntHave('farmer');
        // }

        // if (request()->query('search')) {
        //     $search = request()->query('search');
        //     $devices = $devices->where(function($query)use($search){
        //         $query->where('device_series', $search)
        //             ->orWhereHas('farmer', function($fm)use($search){
        //                 $fm->where('full_name', 'LIKE', '%'.$search.'%');
        //             });
        //     });
        // }

        // if (is_numeric(request()->query('pemilik')) && in_array(request()->query('pemilik'), [0, 1])) {
        //     $pemilik = request()->query('pemilik');
        //     $devices = $devices->where(function($query)use($pemilik){
        //         if ($pemilik == 1) {
        //             $query->whereNotNull('farmer_id');
        //         } else {
        //             $query->whereNull('farmer_id');
        //         }
        //     });
        // }

        // if (request()->query('aktivasi') && in_array(request()->query('aktivasi'), ['sudah', 'belum'])) {
        //     $aktivasi = request()->query('aktivasi');
        //     $devices = $devices->where(function($query)use($aktivasi){
        //         if ($aktivasi == 'sudah') {
        //             $query->whereNotNull('activate_date');
        //         } else {
        //             $query->whereNull('activate_date');
        //         }
        //     });
        // }
        // if (request()->query('tipe') && in_array(request()->query('tipe'), ['controller', 'tongkat'])) {
        //     $tipe = request()->query('tipe');
        //     $devices = $devices->where(function($query)use($tipe){
        //         $query->where('category', $tipe);
        //     });
        // }

        // $data['data'] = $devices->paginate(10)->withQueryString();

        $farmers = Farmer::query()
            ->with([
                'user' => function ($user) {
                    $user->select('id', 'phone_number');
                },
            ]);

        if (auth()->user()->role == 'farmer') {
            $farmers = $farmers->where('user_id', auth()->user()->id);
        }

        if (auth()->user()->role == 'admin' && auth()->user()->city_id != null) {
            $farmers = $farmers->whereHas('user.subdistrict.district', function($query){
                $query->where('city_id', auth()->user()->city_id);
            });
        }

        $data['farmers'] = $farmers->get(['id', 'full_name', 'user_id']);

        return view('bitanic.device.index', $data);
    }

    public function create(Request $request)
    {
        $farmers = Farmer::query()
            ->with([
                'user' => function ($user) {
                    $user->select('id', 'phone_number');
                },
            ])
            ->when(auth()->user()->role == 'farmer', function($query, $role){
                return $query->where('user_id', auth()->user()->id);
            })
            ->when(auth()->user()->role == 'admin' && auth()->user()->city_id != null, function($query, $role){
                return $query->whereHas('user.subdistrict.district', function($query){
                    $query->where('city_id', auth()->user()->city_id);
                });
            })
            ->get(['id', 'full_name', 'user_id']);

        $category = $request->query('category');
        $type = $request->query('type');

        if ($category == 'controller' && ($type == 1 || $type == 2)) {
            return view('bitanic.device.create', compact('farmers', 'type', 'category'));
        } elseif ($category == 'controller' && $type == 3) {
            return view('bitanic.device.type-3.create', compact('farmers', 'type', 'category'));
        } elseif ($category == 'tongkat' && ($type == 1 || $type == 2)) {
            return view('bitanic.device.rsc.create', compact('farmers', 'type', 'category'));
        }

        abort(404, 'Tipe tidak diketahui');
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
            'vertigations' => 'nullable|array|min:0|max:6',
            'irrigations' => 'nullable|array|min:0|max:6',
            'device_series' => 'required|string|max:255|unique:devices,device_series',
            'farmer_id' => 'nullable|exists:farmers,id',
            'type' => 'required|integer|in:1,2',
            'category' => 'required|string|in:controller,tongkat',
            'version' => 'required|numeric|min:0',
            'production_date' => 'required|date',
            'purchase_date' => 'required|date',
            'activate_date' => 'nullable|date',
            'picture' => 'required|image|mimes:jpg,png|max:5048'
        ]);

        $picture = image_intervention($request->file('picture'), 'bitanic-photo/device/', 16/9);

        $farmer = $request->farmer_id ?? null;

        $farmer = (auth()->user()->role == 'farmer') ? auth()->user()->farmer->id : $farmer;

        $type = $request->type;
        $category = $request->category;

        $vertigations = collect([]);
        if ($category == 'controller' && $request->vertigations) {
            for ($i=1; $i <= count($request->vertigations); $i++) {
                $vertigations->push([
                    'id' => $i,
                    'status' => 0
                ]);
            }
        }

        $irrigations = collect([]);
        if ($category == 'controller' && $request->irrigations) {
            for ($i=1; $i <= count($request->irrigations); $i++) {
                $irrigations->push([
                    'id' => $i,
                    'status' => 0
                ]);
            }
        }

        $device = Device::create(
            $request->only(['device_series', 'version', 'type', 'category', 'production_date', 'purchase_date', 'activate_date']) + [
                'farmer_id' => $farmer,
                'picture' => $picture,
                'status' => 0,
                'vertigation' => $vertigations->all(),
                'irrigation' => $irrigations->all()
            ],
        );

        $specification = [];

        if ($request->spesifikasi) {
            $list_spesifikasi = [];
            $now = now();

            foreach ($request->spesifikasi as $spesifikasi) {
                if ($spesifikasi['name'] || $spesifikasi['value']) {
                    $list_spesifikasi[] = [
                        'device_id' => $device->id,
                        'name' => $spesifikasi['name'],
                        'value' => $spesifikasi['value'],
                        'created_at' => $now
                    ];

                    $specification[] = (object) [
                        'name' => $spesifikasi['name'],
                        'value' => $spesifikasi['value'],
                    ];
                }
            }

            DeviceSpecification::insert($list_spesifikasi);
        }

        activity()
            ->performedOn($device)
            ->withProperties(
                collect($device)
                    ->except(['id', 'picture', 'created_at', 'updated_at'])
                    ->merge([
                        'farmer' => $device->farmer_id == null ? null : $device->farmer->full_name,
                        'specification' => $specification
                    ]),
            )
            ->event('created')
            ->log('created');

        return redirect()->route('bitanic.device.index')->with('success', "Berhasil disimpan");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Device $device)
    {
        if (! Gate::allows('update-device', $device)) {
            return back()->withErrors([
                'warning' => ["Anda tidak dapat mengakses data ini"]
            ]);
        }

        return view('bitanic.device.show', compact('device'));
    }

    public function edit(Device $device)
    {
        if (! Gate::allows('update-device', $device)) {
            return back()->withErrors([
                'warning' => ["Anda tidak dapat mengakses data ini"]
            ]);
        }

        $farmers = Farmer::query()
            ->with([
                'user' => function ($user) {
                    $user->select('id', 'phone_number');
                },
            ])
            ->when(auth()->user()->role == 'farmer', function($query, $role){
                return $query->where('user_id', auth()->user()->id);
            })
            ->when(auth()->user()->role == 'admin' && auth()->user()->city_id != null, function($query, $role){
                return $query->whereHas('user.subdistrict.district', function($query){
                    $query->where('city_id', auth()->user()->city_id);
                });
            })
            ->get(['id', 'full_name', 'user_id']);

        $category = $device->category;
        $type = $device->type;

        if ($category == 'controller' && ($type == 1 || $type == 2)) {
            return view('bitanic.device.edit', compact('device', 'farmers', 'type', 'category'));
        } elseif ($category == 'tongkat' && ($type == 1 || $type == 2)) {
            return view('bitanic.device.rsc.edit', compact('device', 'farmers', 'type', 'category'));
        }

        abort(404, 'Tipe tidak diketahui');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Device $device)
    {
        if (! Gate::allows('update-device', $device)) {
            return back()->withErrors([
                'warning' => ["Anda tidak dapat mengakses data ini"]
            ]);
        }

        $request->validate([
            'device_series' => 'required|string|max:255|unique:devices,device_series,'.$device->id,
            // 'type' => 'required|integer|in:1,2',
            // 'category' => 'required|string|in:controller,tongkat',
            'version' => 'required|numeric|min:0',
            'production_date' => 'required|date',
            'purchase_date' => 'required|date',
            'activate_date' => 'nullable|date',
            'picture' => 'nullable|image|mimes:jpg,png|max:5048',
            'farmer_id' => 'nullable|exists:farmers,id'
        ]);

        $picture_new = [];
        $picture_old = [];

        if ($request->file('picture')) {
            $foto = image_intervention($request->file('picture'), 'bitanic-photo/device/', 16/9);

            if (File::exists(public_path($device->picture))) {
                File::delete(public_path($device->picture));
            }

            $device->picture = $foto;
            $device->save();
            $picture_new = ['picture' => 'Updated'];
            $picture_old = ['picture' => 'Old'];
        }

        $original = $device->getOriginal();

        $farmer = $request->farmer_id ?? null;

        $farmer = (auth()->user()->role == 'farmer') ? auth()->user()->farmer->id : $farmer;

        $device->update(
            $request->only(['device_series', 'version', 'production_date', 'purchase_date', 'activate_date']) + [
                'farmer_id' => $farmer,
            ],
        );

        // foreach (json_decode($request->spesifikasi) as $spesifikasi) {
        //     if ($spesifikasi->name || $spesifikasi->value) {
        //         if (!$spesifikasi->id || !($spek = DeviceSpecification::find($spesifikasi->id))) {
        //             $spek = DeviceSpecification::create([
        //                 'device_id' => $device->id,
        //                 'name' => '',
        //                 'value' => '',
        //             ]);
        //         }

        //         $spek->name = $spesifikasi->name;
        //         $spek->value = $spesifikasi->value;

        //         $spek->save();
        //     }
        // }

        $changes = collect($device->getChanges());
        $old = collect($original)->only($changes->keys());

        activity()
            ->performedOn($device)
            ->withProperties(
                collect(
                    array_merge(
                        [
                            'old' => $old
                                ->except(['picture', 'updated_at'])
                                ->merge($picture_old)
                                ->toArray(),
                        ],
                        [
                            'new' => $changes
                                ->except(['picture', 'updated_at'])
                                ->merge($picture_new)
                                ->toArray(),
                        ],
                    ),
                )->toArray(),
            )
            ->event('updated')
            ->log('updated');

        return back()->with('success', "Berhasil disimpan");
    }

    public function editSpecification(Device $device) {
        if (! Gate::allows('update-device', $device)) {
            return back()->withErrors([
                'warning' => ["Anda tidak dapat mengakses data ini"]
            ]);
        }

        $device->load(['specification']);

        return view('bitanic.device.edit-spesification', compact('device'));
    }

    public function updateSpecification(Request $request, Device $device) {
        if (! Gate::allows('update-device', $device)) {
            return back()->withErrors([
                'warning' => ["Anda tidak dapat mengakses data ini"]
            ]);
        }

        $request->validate([
            'spesifikasi' => ['required', 'array'],
            'spesifikasi.*.name' => ['required', 'string', 'max:200'],
            'spesifikasi.*.value' => ['required', 'string', 'max:200'],
        ]);

        DB::table('device_specifications')->where('device_id', $device->id)->delete();

        $list_spesifikasi = [];
        $now = now();

        if ($request->spesifikasi) {
            foreach ($request->spesifikasi as $spesifikasi) {
                if ($spesifikasi['name'] || $spesifikasi['value']) {
                    $list_spesifikasi[] = [
                        'device_id' => $device->id,
                        'name' => $spesifikasi['name'],
                        'value' => $spesifikasi['value'],
                        'created_at' => $now
                    ];
                }
            }

            DeviceSpecification::insert($list_spesifikasi);
        }

        return back()->with('success', "Berhasil disimpan");
    }

    public function editPe(Device $device, $pe) {
        if (! Gate::allows('update-device', $device)) {
            return back()->withErrors([
                'warning' => ["Anda tidak dapat mengakses data ini"]
            ]);
        }

        switch ($pe) {
            case 'irrigation':
                return view('bitanic.device.edit-irrigation', compact('device'));
                break;
            case 'vertigation':
                return view('bitanic.device.edit-vertigation', compact('device'));
                break;

            default:
                return back()->withErrors([
                    'messages' => ['PE tidak ditemukan']
                ]);
                break;
        }
    }

    public function updatePe(Request $request, Device $device, $pe) {
        if (! Gate::allows('update-device', $device)) {
            return back()->withErrors([
                'warning' => ["Anda tidak dapat mengakses data ini"]
            ]);
        }

        $request->validate([
            'vertigations' => 'nullable|array|min:0|max:6',
            'irrigations' => 'nullable|array|min:0|max:6',
        ]);

        switch ($pe) {
            case 'irrigation':
                $irrigations = collect([]);
                if ($request->irrigations) {
                    for ($i=1; $i <= count($request->irrigations); $i++) {
                        $irrigations->push([
                            'id' => $i,
                            'status' => 0
                        ]);
                    }
                }

                $device->update([
                    'irrigation' => $irrigations->all()
                ]);

                return back()->with('success', "Berhasil disimpan");
                break;
            case 'vertigation':
                $vertigations = collect([]);
                if ($request->vertigations) {
                    for ($i=1; $i <= count($request->vertigations); $i++) {
                        $vertigations->push([
                            'id' => $i,
                            'status' => 0
                        ]);
                    }
                }

                $device->update([
                    'vertigation' => $vertigations->all()
                ]);

                return back()->with('success', "Berhasil disimpan");
                break;

            default:
                return back()->with('error', "PE tidak sesuai");
                break;
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
        $data = Device::find($id);

        if (!$data) {
            return response()->json(
                [
                    'messages' => (object) [
                        'text' => ['Data hama tidak ditemukan'],
                    ],
                ],
                404,
            );
        }

        if (! Gate::allows('update-device', $data)) {
            return response()->json([
                'messages' => (object) [
                    'warning' => ["Anda tidak dapat mengakses data ini"]
                ]
            ], 403);
        }

        if (\File::exists(public_path($data->picture))) {
            \File::delete(public_path($data->picture));
        }

        activity()
            ->performedOn($data)
            ->withProperties(['device_series', $data->device_series])
            ->event('deleted')
            ->log('deleted');

        $data->delete();

        return response()->json(
            [
                'message' => 'Berhasil',
            ],
            200,
        );
    }

    public function getDevice($farmer, $garden = null)
    {
        $data = Device::query();

        if ($garden == null) {
            $data = $data->whereNull('garden_id');
        } else {
            $data = $data->where('garden_id', $garden)->orWhereNull('garden_id');
        }

        $data = $data
            ->where('farmer_id', $farmer)
            ->get(['id', 'device_series', 'device_name', 'garden_id']);

        return response()->json(
            [
                'data' => $data,
            ],
            200,
        );
    }

    public function changeStatus(Request $request, $id)
    {
        $device = Device::find($id);

        if (!$device) {
            return response()->json(
                [
                    'messages' => (object) [
                        'text' => ['Data hama tidak ditemukan'],
                    ],
                ],
                404,
            );
        }

        if (! Gate::allows('update-device', $device)) {
            return response()->json([
                'messages' => (object) [
                    'warning' => ["Anda tidak dapat mengakses data ini"]
                ]
            ], 403);
        }

        $v = Validator::make($request->all(), [
            'status' => 'required|integer|in:0,1',
        ], [
            'status.in' => "Status harus berupa 0 (menerima) atau 1 (tidak menerima)"
        ]);

        if ($v->fails()) {
            return response()->json(
                [
                    'messages' => $v->errors(),
                ],
                400,
            );
        }

        if ($device->farmer_id == null) {
            return response()->json(
                [
                    'messages' => (object) [
                        'texts' => ['Perangkat tidak dimiliki oleh petani manapun!']
                    ],
                ],
                400,
            );
        }

        $message = ($request->status)
            ? "Status data diubah menjadi tidak menerima"
            : "Status data diubah menjadi menerima";

        $device->block_data = $request->status;
        $device->save();

        return response()->json([
            'message' => $message
        ], 200);
    }

    public function resetDevice(Request $request, Device $device)
    {
        if (!Gate::allows('update-device', $device)) {
            return response()->json([
                'messages' => (object) [
                    'warning' => ["Anda tidak dapat mengakses data ini"]
                ]
            ], 403);
        }

        MQTT::publish('bitanic/' . $device->device_series, 'RESETALL,*');

        return response()->json([
            'message' => "mqtt message sended. Check your device!"
        ]);
    }

    public function setOnPump(Request $request, Device $device) : JsonResponse {
        if (! Gate::allows('update-device', $device)) {
            return response()->json([
                'messages' => (object) [
                    'warning' => ["Anda tidak dapat mengakses data ini"]
                ]
            ], 403);
        }

        $request->validate([
            'pump' => ['required', 'in:1,2'],
        ]);

        $topic = "bitanic/$device->device_series";

        MQTT::publish($topic, "MOTOR$request->pump,1,*");

        return response()->json([
            'message' => "Berhasil mengirim command ke alat. Silahkan check alat anda!"
        ], 200);
    }

    public function setOffPump(Request $request, Device $device) : JsonResponse {
        if (! Gate::allows('update-device', $device)) {
            return response()->json([
                'messages' => (object) [
                    'warning' => ["Anda tidak dapat mengakses data ini"]
                ]
            ], 403);
        }

        $request->validate([
            'pump' => ['required', 'in:1,2'],
        ]);

        $topic = "bitanic/$device->device_series";

        MQTT::publish($topic, "MOTOR$request->pump,0,*");

        return response()->json([
            'message' => "Berhasil mengirim command ke alat. Silahkan check alat anda!"
        ], 200);
    }

    public function setPeStatus(Request $request, Device $device) : JsonResponse {

        if (! Gate::allows('update-device', $device)) {
            return response()->json([
                'messages' => (object) [
                    'warning' => ["Anda tidak dapat mengakses data ini"]
                ]
            ], 403);
        }

        $request->validate([
            'status' => ['required', 'in:0,1'],
            'pe' => ['required', 'in:1,2,3,4,5,6'],
            'pump' => ['required', 'in:1,2']
        ]);

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
}
