<?php

namespace App\Http\Controllers\Bitanic\v2;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceSpecification;
use App\Models\Farmer;
use App\Models\Fertilization;
use App\Models\FertilizationSchedule;
use App\Models\Formula;
use App\Models\Land;
use Illuminate\Support\Str;
use App\Models\NecessityDifference;
use App\Models\Selenoid;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use PhpMqtt\Client\Facades\MQTT;

class DeviceController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'vertigations' => 'nullable|array|min:0|max:6',
            'irrigations' => 'nullable|array|min:0|max:6',
            'device_series' => 'required|string|max:255|unique:devices,device_series',
            'farmer_id' => 'nullable|exists:farmers,id',
            'type' => 'required|integer|in:3',
            'category' => 'required|string|in:controller,tongkat',
            'version' => 'required|numeric|min:0',
            'production_date' => 'required|date',
            'purchase_date' => 'required|date',
            'activate_date' => 'nullable|date',
            'picture' => 'required|image|mimes:jpg,png|max:5048',
            'r'            => 'required|numeric|min:0',
            'tinggi_toren'            => 'required|numeric|min:0',
            'tinggi_awal'            => 'required|numeric|min:0',
            'tinggi_akhir'            => 'required|numeric|min:0',
            'delay'            => 'required|integer|min:0|max:60',
        ]);

        $picture = image_intervention($request->file('picture'), 'bitanic-photo/device/', 16/9);

        $farmer = $request->farmer_id ?? null;

        $farmer = (auth()->user()->role == 'farmer') ? auth()->user()->farmer->id : $farmer;

        $vertigations = collect([]);
        if ($request->vertigations) {
            for ($i=1; $i <= count($request->vertigations); $i++) {
                $vertigations->push([
                    'id' => $i,
                    'status' => 0
                ]);
            }
        }

        $irrigations = collect([]);
        if ($request->irrigations) {
            for ($i=1; $i <= count($request->irrigations); $i++) {
                $irrigations->push([
                    'id' => $i,
                    'status' => 0
                ]);
            }
        }

        $toren_pemupukan = null;

        if ($request->type == 3) {
            $pi = 3.14;

            $v_toren = round((($pi * pow($request->r, 2) * $request->tinggi_toren) / 1000), 2);
            $v_fron_t_toren = round((($pi * pow($request->r, 2) * $request->tinggi_awal) / 1000), 2);
            $downT = $request->tinggi_awal - $request->tinggi_akhir;
            $debit = round((($pi * pow($request->r, 2) * $downT) / 1000), 2);

            $result = $v_fron_t_toren / $debit;
            $minutes = floor($result);
            $seconds = floor(($result - $minutes) * 60);

            $minutes = $minutes < 10 ? "0" . $minutes : $minutes;
            $seconds = $seconds < 10 ? "0" . $seconds : $seconds;

            $toren_pemupukan = (object) [
                "r" => (double) $request->r,
                "tinggi_toren" => (double) $request->tinggi_toren,
                "v_toren" => $v_toren,
                "tinggi_awal" => (double) $request->tinggi_awal,
                "tinggi_akhir" => (double) $request->tinggi_akhir,
                "v_fron_t_toren" => $v_fron_t_toren,
                "debit" => $debit,
                "duration" => "$minutes:$seconds",
            ];
        }

        $device = Device::create(
            $request->only(['device_series', 'version', 'type', 'category', 'production_date', 'purchase_date', 'activate_date', 'delay']) + [
                'farmer_id' => $farmer,
                'picture' => $picture,
                'status' => 0,
                'vertigation' => $vertigations->all(),
                'irrigation' => $irrigations->all(),
                'toren_pemupukan' => $toren_pemupukan,
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

    public function show(Device $device)
    {
        if (! Gate::allows('update-device', $device)) {
            abort(403);
        }

        $device->load([
            'fertilization',
            'selenoids:id,device_id,land_id,selenoid_status,selenoid_id',
            'selenoids.land:id,name',
        ]);

        $finished_fertilizations = Fertilization::query()
            ->where('device_id', $device->id)
            ->where('is_finished', 1)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('bitanic.device.type-3.show', compact('device', 'finished_fertilizations'));
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
            'version' => 'required|numeric|min:0',
            'production_date' => 'required|date',
            'purchase_date' => 'required|date',
            'activate_date' => 'nullable|date',
            'picture' => 'nullable|image|mimes:jpg,png|max:5048',
            'farmer_id' => 'nullable|exists:farmers,id',
            'r'            => 'required|numeric|min:0',
            'tinggi_toren'            => 'required|numeric|min:0',
            'tinggi_awal'            => 'required|numeric|min:0',
            'tinggi_akhir'            => 'required|numeric|min:0',
            'delay'            => 'required|integer|min:0|max:60',
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

        $pi = 3.14;

        $v_toren = round((($pi * pow($request->r, 2) * $request->tinggi_toren) / 1000), 2);
        $v_fron_t_toren = round((($pi * pow($request->r, 2) * $request->tinggi_awal) / 1000), 2);
        $downT = $request->tinggi_awal - $request->tinggi_akhir;
        $debit = round((($pi * pow($request->r, 2) * $downT) / 1000), 2);

        $result = $v_fron_t_toren / $debit;
        $minutes = floor($result);
        $seconds = floor(($result - $minutes) * 60);

        $minutes = $minutes < 10 ? "0" . $minutes : $minutes;
        $seconds = $seconds < 10 ? "0" . $seconds : $seconds;

        $toren_pemupukan = (object) [
            "r" => (double) $request->r,
            "tinggi_toren" => (double) $request->tinggi_toren,
            "v_toren" => $v_toren,
            "tinggi_awal" => (double) $request->tinggi_awal,
            "tinggi_akhir" => (double) $request->tinggi_akhir,
            "v_fron_t_toren" => $v_fron_t_toren,
            "debit" => $debit,
            "duration" => "$minutes:$seconds",
        ];

        $device->update(
            $request->only(['device_series', 'version', 'category', 'production_date', 'purchase_date', 'activate_date']) + [
                'farmer_id' => $request->farmer_id ?? null,
                'toren_pemupukan' => $toren_pemupukan,
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

    public function edit(Device $device) {
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

        $farmers = $farmers->get(['id', 'full_name', 'user_id']);

        return view('bitanic.device.type-3.edit', compact('device', 'farmers'));
    }

    public function formulas(Device $device) {
        if (! Gate::allows('update-device', $device)) {
            abort(403);
        }

        $device->load(['fertilization:id,device_id']);

        $formulas = Formula::query()
            ->when(auth()->user()->role == 'farmer', function($query, $role){
                return $query->where('user_id', auth()->id());
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('bitanic.device.type-3.formula', compact('device', 'formulas'));
    }

    public function output(Device $device, $formula)
    {
        if (! Gate::allows('update-device', $device)) {
            abort(403);
        }

        $device->load([
            'selenoids:id,device_id,land_id,selenoid_id,selenoid_watering',
            'selenoids.land:id,name',
        ]);

        $formula = Formula::query()
            ->with([
                'crop'
            ])
            ->findOrFail($formula);

        // $request = session('formula');
        $request = $formula->formula;

        $data['request'] = (array) $request;
        $data['perangkat'] = $device;

        $data['amsul']['per_hektar_total'] = $request->pupuk_amsul !== "ok" ? 0 : ($request->dosis_n * 100 / 21);
        $data['amsul']['per_hektar_pre'] = $request->pupuk_amsul !== "ok" ? 0 : $request->preplant_n / 100 * $data['amsul']['per_hektar_total'];
        $data['amsul']['per_hektar_drip'] = $request->pupuk_amsul !== "ok" ? 0 : $data['amsul']['per_hektar_total'] - $data['amsul']['per_hektar_pre'];
        $data['amsul']['lahan_pre'] = $request->pupuk_amsul !== "ok" ? 0 : ($data['amsul']['per_hektar_pre'] * $request->luas_lahan / 10000);
        $data['amsul']['lahan_drip'] = $request->pupuk_amsul !== "ok" ? 0 : ($data['amsul']['per_hektar_drip'] * $request->luas_lahan / 10000);
        $data['amsul']['salt_index_unit'] = $request->pupuk_amsul !== "ok" ? 0 : round(1.618, 3);
        $data['amsul']['salt_index_formalition'] = $request->pupuk_amsul !== "ok" ? 0 : ((int)($request->dosis_n/9.08) * $data['amsul']['salt_index_unit']);

        $data['sp36']['per_hektar_total'] = $request->pupuk_sp36 !== "ok" ? 0 : ($request->dosis_p2o5 * 100 / 36);
        $data['sp36']['per_hektar_pre'] = $request->pupuk_sp36 !== "ok" ? 0 : ($request->preplant_p2o5 / 100 * $data['sp36']['per_hektar_total']);
        $data['sp36']['per_hektar_drip'] = $request->pupuk_sp36 !== "ok" ? 0 : $data['sp36']['per_hektar_total'] - $data['sp36']['per_hektar_pre'];
        $data['sp36']['lahan_pre'] = $request->pupuk_sp36 !== "ok" ? 0 : ($data['sp36']['per_hektar_pre'] * $request->luas_lahan / 10000);
        $data['sp36']['lahan_drip'] = $request->pupuk_sp36 !== "ok" ? 0 : ($data['sp36']['per_hektar_drip'] * $request->luas_lahan / 10000);
        $data['sp36']['salt_index_unit'] = $request->pupuk_sp36 !== "ok" ? 0 : round(0.390, 3);
        $data['sp36']['salt_index_formalition'] = $request->pupuk_sp36 !== "ok" ? 0 : ((int)($request->dosis_p2o5/9.08) * $data['sp36']['salt_index_unit']);

        $data['kci']['per_hektar_total'] = $request->pupuk_kci !== "ok" ? 0 : ($request->dosis_k2o * 100 / 50);
        $data['kci']['per_hektar_pre'] = $request->pupuk_kci !== "ok" ? 0 : ($request->preplant_k2o / 100 * $data['kci']['per_hektar_total']);
        $data['kci']['per_hektar_drip'] = $request->pupuk_kci !== "ok" ? 0 : $data['kci']['per_hektar_total'] - $data['kci']['per_hektar_pre'];
        $data['kci']['lahan_pre'] = $request->pupuk_kci !== "ok" ? 0 : ($data['kci']['per_hektar_pre'] * $request->luas_lahan / 10000);
        $data['kci']['lahan_drip'] = $request->pupuk_kci !== "ok" ? 0 : ($data['kci']['per_hektar_drip'] * $request->luas_lahan / 10000);
        $data['kci']['salt_index_unit'] = $request->pupuk_kci !== "ok" ? 0 : round(1.936, 3);
        $data['kci']['salt_index_formalition'] = $request->pupuk_kci !== "ok" ? 0 : ((int)($request->dosis_k2o/9.08) * $data['kci']['salt_index_unit']);

        $data['potsul']['per_hektar_total'] = $request->pupuk_potsul !== "ok" ? 0 : ($request->dosis_k2o * 100 / 60);
        $data['potsul']['per_hektar_pre'] = $request->pupuk_potsul !== "ok" ? 0 : ($request->preplant_k2o / 100 * $data['potsul']['per_hektar_total']);
        $data['potsul']['per_hektar_drip'] = $request->pupuk_potsul !== "ok" ? 0 : $data['potsul']['per_hektar_total'] - $data['potsul']['per_hektar_pre'];
        $data['potsul']['lahan_pre'] = $request->pupuk_potsul !== "ok" ? 0 : ($data['potsul']['per_hektar_pre'] * $request->luas_lahan / 10000);
        $data['potsul']['lahan_drip'] = $request->pupuk_potsul !== "ok" ? 0 : ($data['potsul']['per_hektar_drip'] * $request->luas_lahan / 10000);
        $data['potsul']['salt_index_unit'] = $request->pupuk_potsul !== "ok" ? 0 : round(1.936, 3);
        $data['potsul']['salt_index_formalition'] = $request->pupuk_potsul !== "ok" ? 0 : ((int)($request->dosis_k2o/9.08) * $data['potsul']['salt_index_unit']);

        $data['frit']['per_hektar_total'] = $request->pupuk_frits !== "ok" ? 0 : (int) $request->dosis_frit;
        $data['frit']['per_hektar_pre'] = $request->pupuk_frits !== "ok" ? 0 : ($request->preplant_frit / 100 * $data['frit']['per_hektar_total']);
        $data['frit']['per_hektar_drip'] = $request->pupuk_frits !== "ok" ? 0 : $data['frit']['per_hektar_total'] - $data['frit']['per_hektar_pre'];
        $data['frit']['lahan_pre'] = $request->pupuk_frits !== "ok" ? 0 : round(($data['frit']['per_hektar_pre'] * $request->luas_lahan / 10000), 1, PHP_ROUND_HALF_DOWN);
        $data['frit']['lahan_drip'] = $request->pupuk_frits !== "ok" ? 0 : round(($data['frit']['per_hektar_drip'] * $request->luas_lahan / 10000), 1, PHP_ROUND_HALF_DOWN);
        $data['frit']['salt_index_unit'] = $request->pupuk_frits !== "ok" ? 0 : round(1.0, 3);
        $data['frit']['salt_index_formalition'] = 0;

        // dd($request->dosis_n);

        $data['urea']['per_hektar_total'] = $request->pupuk_urea !== "ok" ? 0 : ($request->dosis_n * 100 / 46);
        $data['urea']['per_hektar_pre'] = $request->pupuk_urea !== "ok" ? 0 : $request->preplant_n / 100 * $data['urea']['per_hektar_total'];
        $data['urea']['per_hektar_drip'] = $request->pupuk_urea !== "ok" ? 0 : $data['urea']['per_hektar_total'] - $data['urea']['per_hektar_pre'];
        $data['urea']['lahan_pre'] = $request->pupuk_urea !== "ok" ? 0 : ($data['urea']['per_hektar_pre'] * $request->luas_lahan / 10000);
        $data['urea']['lahan_drip'] = $request->pupuk_urea !== "ok" ? 0 : ($data['urea']['per_hektar_drip'] * $request->luas_lahan / 10000);
        $data['urea']['salt_index_unit'] = $request->pupuk_urea !== "ok" ? 0 : round(1.618, 3);
        $data['urea']['salt_index_formalition'] = $request->pupuk_urea !== "ok" ? 0 : ((int)($request->dosis_n/9.08) * $data['urea']['salt_index_unit']);

        $listSelisih = NecessityDifference::orderBy('selisih_ph', 'asc')->get();
        $data['dolomit']['ton_ha'] = 0;

        $last_selisih = 0;
        for ($i=0; $i < count($listSelisih); $i++) {
            if ($i == 0 && $request->selisih_ph <= $listSelisih[$i]->selisih_ph) {
                $data['dolomit']['ton_ha'] = $listSelisih[$i]->kebutuhan_dolomit;
                break;
            } elseif ($i == (count($listSelisih) - 1) && $request->selisih_ph >= $listSelisih[$i]->selisih_ph) {
                $data['dolomit']['ton_ha'] = $listSelisih[$i]->kebutuhan_dolomit;
                break;
            } elseif ($request->selisih_ph >= $last_selisih && $request->selisih_ph <= $listSelisih[$i]->selisih_ph) {
                $data['dolomit']['ton_ha'] = $listSelisih[$i]->kebutuhan_dolomit;
                break;
            }

            $last_selisih = $listSelisih[$i]->selisih_ph;
        }

        $data['ukuran_petak']['penjang_bendengan'] = 25;
        $data['ukuran_petak']['lebar_bendengan'] = 1.5;
        $data['ukuran_petak']['ulangan'] = 17;
        $data['ukuran_petak']['luas_petak'] = $data['ukuran_petak']['penjang_bendengan'] * $data['ukuran_petak']['lebar_bendengan'];
        $data['ukuran_petak']['luas_total_ulangan'] = $data['ukuran_petak']['luas_petak'] * $data['ukuran_petak']['ulangan'];

        $data['dolomit']['ton_lahan'] = $data['dolomit']['ton_ha'] * $request->luas_lahan / 10000;

        $data['bahan_organik']['ton_ha'] = round(($request->selisih_corg * 1.724 / 100 * 2000000 / 1000), 3);
        $data['bahan_organik']['ton_lahan'] = round($data['bahan_organik']['ton_ha'] * $request->luas_lahan / 10000, 5);

        $kebutuhan_pupuk = 1.724 * $request->selisih_corg;

        $data['pupuk_organik']['ton_ha'] = round(($kebutuhan_pupuk / $request->pupuk_organik_corg * $data['bahan_organik']['ton_ha']), 3);
        $data['pupuk_organik']['ton_lahan'] = round($data['pupuk_organik']['ton_ha'] * $request->luas_lahan / 10000, 5);

        $data['jenis_tanah'] = $request?->jenis_tanah ?? '';

        $data['pupuk_urea'] = round($data['urea']['lahan_pre'] + $data['urea']['lahan_drip']);
        $data['pupuk_sp36'] = round($data['sp36']['lahan_pre'] + $data['sp36']['lahan_drip']);
        $data['pupuk_kci'] = round($data['kci']['lahan_pre'] + $data['kci']['lahan_drip']);
        $data['dolomit']['rekomendasi_pupuk'] = round($data['dolomit']['ton_lahan'] * 1000);

        // dd($data);

        if (isset($request->nama_petani)) {
            session(['nama_petani' => $request->nama_petani]);
        }
        if (isset($request->luas_lahan)) {
            session(['luas_lahan' => $request->luas_lahan]);
        }
        if (isset($request->latitude)) {
            session(['latitude' => $request->latitude]);
        }
        if (isset($request->longitude)) {
            session(['longitude' => $request->longitude]);
        }
        if (isset($request->altitude)) {
            session(['altitude' => $request->altitude]);
        }
        if (isset($formula->crop)) {
            $tanaman = $formula->crop;
            session(['jenis_tanaman' => ($tanaman) ? $tanaman->crop_name : null]);
        }
        if (isset($request->dosis_n)) {
            session(['dosis_n' => $request->dosis_n]);
        }
        if (isset($request->dosis_p2o5)) {
            session(['dosis_p2o5' => $request->dosis_p2o5]);
        }
        if (isset($request->dosis_k2o)) {
            session(['dosis_k2o' => $request->dosis_k2o]);
        }
        if (isset($request->preplant_n)) {
            session(['preplant_n' => $request->preplant_n]);
        }
        if (isset($request->preplant_p2o5)) {
            session(['preplant_p2o5' => $request->preplant_p2o5]);
        }
        if (isset($request->preplant_k2o)) {
            session(['preplant_k2o' => $request->preplant_k2o]);
        }

        return view('bitanic.device.type-3.output', $data);
    }

    public function getLands(Device $device) : JsonResponse {
        if (! Gate::allows('update-device', $device)) {
            abort(403);
        }

        $lands = Selenoid::query()
            ->select(['id', 'device_id', 'land_id', 'selenoid_id', 'selenoid_watering'])
            ->with([
                'land:id,name'
            ])
            ->where('device_id', $device->id)
            ->orderBy('selenoid_id')
            ->get();

        return response()->json($lands);
    }

    public function storePenjadwalan(Request $request)
    {
        // dd($request->all());
        $v = Validator::make($request->all(), [
            "nama_petani" => 'required|string|max:250',
            "luas_lahan" => 'required|numeric|min:0',
            "latitude" => 'nullable|string',
            "longitude" => 'nullable|string',
            "altitude" => 'nullable|string',
            'alamat' => 'nullable|string|max:500',
            "perangkat_id" => 'required|exists:devices,id',
            "jenis_tanah" => 'nullable|string',
            "dosis_n" => 'required|numeric',
            "dosis_p2o5" => 'required|numeric',
            "dosis_k2o" => 'required|numeric',
            "dosis_frit" => 'required|numeric',
            "preplant_n" => 'required|integer|min:0',
            "preplant_p2o5" => 'required|integer|min:0',
            "preplant_k2o" => 'required|integer|min:0',
            "preplant_frit" => 'required|integer|min:0',
            "selisih_ph" => 'required|numeric',
            "selisih_corg" => 'required|numeric',
            "pupuk_organik_corg" => 'required|numeric',
            "drip_n" => 'required|integer|min:0|max:100',
            "drip_p2o5" => 'required|integer|min:0|max:100',
            "drip_k2o" => 'required|integer|min:0|max:100',
            "drip_frit" => 'required|integer|min:0|max:100',
            "frekuensi_drip" => 'required|integer|min:0',
            "input_p_tersedia" => 'required|numeric',
            "p_status" => 'required|in:Sangat Rendah,Rendah,Sedang,Tinggi,Sangat Tinggi',
            "input_k_tersedia" => 'required|numeric',
            "k_status" => 'required|in:Sangat Rendah,Rendah,Sedang,Tinggi,Sangat Tinggi',
            "input_corganik" => 'required|numeric',
            "input_ph" => 'required|numeric',
            "pupuk_amsul" => 'required|in:ok,0',
            "pupuk_sp36" => 'required|in:ok,0',
            "pupuk_kci" => 'required|in:ok,0',
            "pupuk_potsul" => 'required|in:ok,0',
            "pupuk_frits" => 'required|in:ok,0',
            "pupuk_urea" => 'required|in:ok,0',
        ]);

        if ($v->fails()) {
            return back()->withErrors($v->errors())->withInput();
        }

        $v = Validator::make($request->all(), [
            'watering.days' => 'array|max:7',
            'watering.days.*' => 'in:senin,selasa,rabu,kamis,jum\'at,sabtu,minggu',
            'watering.setontimes' => 'array|min:1|max:10',
            'watering.setontimes.*.lands' => 'array',
            'watering.setontimes.*.lands.*.id' => 'required|integer|in:1,2,3,4,5,6',
            'watering.setontimes.*.lands.*.duration' => 'required_without:watering.setontimes.*.lands.*.seconds|nullable|integer|min:0|max:60',
            'watering.setontimes.*.lands.*.seconds' => 'required_without:watering.setontimes.*.lands.*.duration|nullable|integer|min:0|max:60',
            'watering.setontimes.*.time' => 'required_with:watering.setontimes.*.lands|nullable|date_format:H:i',
            'fertilization.days' => 'array|size:1',
            'fertilization.days.*' => 'in:senin,selasa,rabu,kamis,jum\'at,sabtu,minggu',
            'fertilization.setontimes' => 'array|size:1',
            'fertilization.setontimes.*.lands' => 'array|min:1',
            'fertilization.setontimes.*.lands.*.id' => 'required|integer|in:1,2,3,4,5,6',
            'fertilization.setontimes.*.lands.*.duration' => 'required_without:fertilization.setontimes.*.lands.*.seconds|nullable|integer|min:0|max:60',
            'fertilization.setontimes.*.lands.*.seconds' => 'required_without:fertilization.setontimes.*.lands.*.duration|nullable|integer|min:0|max:60',
            'fertilization.setontimes.*.time' => 'required_with:fertilization.setontimes.*.lands|nullable|date_format:H:i',
            'minggu' => 'required|integer|min:1|max:15',
            'jenis_tanaman' => 'required|string|max:250',
        ]);

        if ($v->fails()) {
            return back()->withErrors($v->errors())->withInput();
        }

        $perangkat = Device::find($request->perangkat_id);

        if (! Gate::allows('update-device', $perangkat)) {
            abort(403);
        }

        $fertilizationSetontimes = collect([]);
        $wateringSetontimes = collect([]);

        $newFertilization = null;
        $newWatering = null;

        $furthesDay = null;
        $waterLastDay = null;
        $fertilizationLastDay = null;

        $furthesTime = null;
        $waterLastTime = null;
        $fertilizationLastTime = null;

        if ($request->has('watering.days')) {
            try {
                [$wateringSetontimes, $waterTimesDuration] = $this->checkSetontimes("air", $request->watering['setontimes'], $perangkat->delay);

                $newWatering = (object) ["days" => $request->watering['days'], "setontimes" => $wateringSetontimes];
                $waterDays = $request->watering['days'];
                if (count($waterDays) == 1) {
                    $waterLastDay = $waterDays[0];
                } elseif ($waterDays[0] == 'minggu') {
                    $waterLastDay = $waterDays[0];
                } else {
                    $waterLastDay = $waterDays[count($waterDays) - 1];
                }

                $waterLastTime = $waterTimesDuration[count($waterTimesDuration) - 1]->end;
            } catch (Exception $th) {
                //throw $th;
                return back()->withErrors([
                    'watering' => [$th->getMessage()]
                ]);
            }
        }

        if ($request->has('fertilization.days')) {
            try {
                [$fertilizationSetontimes, $fertilizationTimeDuration] = $this->checkSetontimes("pupuk", $request->fertilization['setontimes'], $perangkat->delay);

                $newFertilization = (object) ["days" => $request->fertilization['days'], "setontimes" => $fertilizationSetontimes];
                $fertilizationDays = $request->fertilization['days'];
                if (count($fertilizationDays) == 1) {
                    $fertilizationLastDay = $fertilizationDays[0];
                } elseif ($fertilizationDays[0] == 'minggu') {
                    $fertilizationLastDay = $fertilizationDays[0];
                } else {
                    $fertilizationLastDay = $fertilizationDays[count($fertilizationDays) - 1];
                }

                $fertilizationLastTime = $fertilizationTimeDuration[count($fertilizationTimeDuration) - 1]->end;
            } catch (Exception $th) {
                //throw $th;
                return back()->withErrors([
                    'fertilization' => [$th->getMessage()]
                ]);
            }
        }

        if ($newFertilization && $newWatering && collect($newWatering->days)->intersect($newFertilization->days)->count() > 0) {
            for ($i=0; $i < count($waterTimesDuration); $i++) {
                for ($j=0; $j < count($fertilizationTimeDuration); $j++) {
                    if (
                        $waterTimesDuration[$i]->start->between($fertilizationTimeDuration[$j]->start, $fertilizationTimeDuration[$j]->end) ||
                        $fertilizationTimeDuration[$j]->start->between($waterTimesDuration[$i]->start, $waterTimesDuration[$i]->end)
                    ) {
                        return back()->withErrors([
                            'setontime' => [
                                "Terdapat waktu yang bentrok antara penyiraman (" .
                                $waterTimesDuration[$i]->start->copy()->format('H:i:s') .
                                ") dengan pemupukan (" .
                                $fertilizationTimeDuration[$j]->start->copy()->format('H:i:s') .
                                ")"
                            ]
                        ]);
                    }
                }
            }
        }

        $dayType = null;
        if ($waterLastDay && $fertilizationLastDay) {
            if (getDayIndex($waterLastDay) == getDayIndex($fertilizationLastDay)) {
                $furthesDay = getDayIndex($waterLastDay);
                $dayType = 2;
            } elseif (getDayIndex($waterLastDay) > getDayIndex($fertilizationLastDay)) {
                $furthesDay = getDayIndex($waterLastDay);
                $dayType = 0;
            } elseif (getDayIndex($waterLastDay) < getDayIndex($fertilizationLastDay)) {
                $furthesDay = getDayIndex($fertilizationLastDay);
                $dayType = 1;
            }
        } elseif (!$fertilizationLastDay) {
            $furthesDay = getDayIndex($waterLastDay);
            $dayType = 0;
        } elseif (!$waterLastDay) {
            $furthesDay = getDayIndex($fertilizationLastDay);
            $dayType = 1;
        }

        switch ($dayType) {
            case 0:
                $furthesTime = $waterLastTime;
                break;
            case 1:
                $furthesTime = $fertilizationLastTime;
                break;
            case 2:
                $furthesTime = ($waterLastTime->gte($fertilizationLastTime)) ? $waterLastTime : $fertilizationLastTime;
                break;
        }

        $end_date = now('Asia/Jakarta')->startOfWeek()->addWeeks($request->minggu - 1)->addDays($furthesDay)->format('Y-m-d');

        $end_datetime = $end_date . " " .$furthesTime->format('H:i:s');

        $drip = $request->volume_toren / $request->minggu;

        Fertilization::create([
            'device_id' => $request->perangkat_id,
            'farmer_id' => $perangkat->farmer_id,
            'crop_name' => $request->jenis_tanaman,
            'drip_out' => $drip,
            'weeks' => $request->minggu,
            'end_datetime' => $end_datetime,
            'valves' => (object) [
                'fertilization' => $newFertilization,
                'watering' => $newWatering,
            ],
            'formula' => $request->only([
                "nama_petani",
                "luas_lahan",
                "latitude",
                "longitude",
                "altitude",
                "alamat",
                "jenis_tanah",
                "dosis_n",
                "dosis_p2o5",
                "dosis_k2o",
                "dosis_frit",
                "preplant_n",
                "preplant_p2o5",
                "preplant_k2o",
                "preplant_frit",
                "selisih_ph",
                "selisih_corg",
                "pupuk_organik_corg",
                "drip_n",
                "drip_p2o5",
                "drip_k2o",
                "drip_frit",
                "frekuensi_drip",
                "input_p_tersedia",
                "p_status",
                "input_k_tersedia",
                "k_status",
                "input_corganik",
                "input_ph",
                "pupuk_amsul",
                "pupuk_sp36",
                "pupuk_kci",
                "pupuk_potsul",
                "pupuk_frits",
                "pupuk_urea",
            ])
        ]);

        $topic = 'bitanic/'.$perangkat->device_series;

        $message = (object) [
            "mode" => "auto",
            "jadwal" => (object) [
                "minggu" => (int) $request->minggu,
                "air" => $newWatering,
                "pemupukan" => $newFertilization
            ]
        ];

        // set minggu
        MQTT::publish($topic, json_encode($message), false, $perangkat->type == 2 ? config('app.mqtt') : null);

        return redirect()->route('bitanic.v3-device.show', $perangkat->id)->with('success', 'Berhasil');
    }

    private function checkSetontimes(string $type, array $setontimes = [], int $delay = 5) : Array {
        $collectSetontimes = collect([]);
        $timeDurations = collect([]);
        $checkTime = null;
        $k = 1;
        foreach ($setontimes as $setontime) {
            if (isset($setontime['lands'])) {
                $time = now('Asia/Jakarta')->parse($setontime['time']);
                if ($checkTime && $time->lte($checkTime)) { // check if current time is smaller than previous time
                    throw new Exception("Waktu $k tidak bisa KURANG atau SAMA dari waktu sebelum!");
                }
                $i = 0;
                $collLands = collect();
                $totalMinutes = 0;
                $totalSeconds = 0;
                foreach ($setontime['lands'] as $key => $land) {
                    $landMinutes = (int) $land['duration'] ?? 0;
                    $landSeconds = (int) $land['seconds'] ?? 0;

                    $arrVal = [
                        "id" => (int) $land['id'],
                        "duration" => $landMinutes,
                        "seconds" => $landSeconds
                    ];

                    if ($type == "pupuk") {
                        $arrVal = array_merge($arrVal, [
                            "first_water" => (object) [
                                "minutes" => (int) $land['water_before']['minutes'],
                                "seconds" => (int) $land['water_before']['seconds'],
                            ],
                            "last_water" => (object) [
                                "minutes" => (int) $land['water_after']['minutes'],
                                "seconds" => (int) $land['water_after']['seconds'],
                            ],
                        ]);

                        $landMinutes += (int) $land['water_before']['minutes'];
                        $landSeconds += (int) $land['water_before']['seconds'];
                        $landMinutes += (int) $land['water_after']['minutes'];
                        $landSeconds += (int) $land['water_after']['seconds'];
                        $landSeconds += ($delay * 2);
                    }

                    $collLands->push((object) $arrVal);
                    $totalMinutes += $landMinutes;
                    $totalSeconds += $landSeconds;
                }

                $collectSetontimes->push([
                    'time' => $time->copy()->format('H:i:s'),
                    "delay" => $delay,
                    "lands" => $collLands->all(),
                ]);

                $secondsCeil = ceil((($delay * (count($setontime['lands']) - 1)) + $totalSeconds) / 60) * 60;

                $timeDurations->push((object) [
                    "start" => $time->copy(),
                    "end" => $time->copy()->addMinute()->addMinutes($totalMinutes)->addSeconds($secondsCeil),
                ]);

                $checkTime = $time->copy();
                $k++;
            }
        }

        $timeDurations = $timeDurations->all();

        for ($i=1; $i < count($timeDurations); $i++) {
            if ($timeDurations[$i]->start->between($timeDurations[$i-1]->start, $timeDurations[$i-1]->end)) {
                throw new Exception("Terdapat waktu yang bentrok, harap cek kembali waktunya!");
            }
        }

        return [$collectSetontimes->all(), $timeDurations];
    }

    public function sendManualControlTwo(Request $request, $id) : JsonResponse {
        $perangkat = Device::query()
            ->with('selenoids:id,device_id,selenoid_id,selenoid_status')
            ->where('type', 3)
            ->where('id', $id)
            ->firstOrFail();

        $request->validate([
            'type' => 'required|in:penyiraman,pemupukan',
            'selenoid' => 'required|integer|in:1,2,3,4',
            'status' => 'required|string|in:on,off'
        ]);

        $type = $request->type;
        $status = $request->status;
        $topic = 'bitanic/'.$perangkat->device_series;

        $message = [
            "mode" => "manual",
            "tipe" => $type,
        ];

        foreach ($perangkat->selenoids as $selenoid) {
            $newStatus = $selenoid->selenoid_status;
            switch ($newStatus) {
                case 1:
                    $newStatus = "on";
                    break;
                case 0:
                default:
                    $newStatus = "off";
                    break;
            }
            $message['lahan' . $selenoid->selenoid_id] = ($request->selenoid == $selenoid->selenoid_id) ? $status : $newStatus;
        }

        for ($i=count($perangkat->selenoids) + 1; $i <= 4; $i++) {
            $message = array_merge($message, ["lahan$i" => "off"]);
        }

        MQTT::publish($topic, json_encode((object) $message), false, config('app.mqtt'));

        return response()->json([
            'message' => "Mengirim status " . $request->status . " pada alat",
        ], 200);
    }

    public function resetPerangkat(Request $request, $id)
    {
        $pemupukan = Fertilization::find($id);

        if (!$pemupukan) {
            return response()->json([
                'messages' => (object) [
                    'text' => ['Data tidak ditemukan']
                ]
            ], 404);
        }

        $topic = 'bitanic/'.$pemupukan->device->device_series;

        if ($pemupukan->device->type == 1) {
            MQTT::publish($topic, 'RESETALL,*');
        } elseif ($pemupukan->device->type == 3) {
            $message = (object) [
                "mode" => "auto",
                "jadwal" => (object) [
                    "minggu" => 0,
                    "air" => null,
                    "pemupukan" => null
                ]
            ];

            // set minggu
            MQTT::publish($topic, json_encode($message), false, config('app.mqtt'));
        }

        $pemupukan->delete();

        return response()->json([
            'message' => 'Setting dikirim dan pemupukan telah dihapus. Periksa kembali perangkat anda.'
        ], 200);
    }

    public function scheduleShow(Device $device, Fertilization $fertilization) {
        if (! Gate::allows('update-device', $device)) {
            return back()->withErrors([
                'warning' => ["Anda tidak dapat mengakses data ini"]
            ]);
        }

        $fertilization_schedules = FertilizationSchedule::query()
            ->where('fertilization_id', $fertilization->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('bitanic.device.type-3.schedule.show', compact('device', 'fertilization', 'fertilization_schedules'));
    }

    public function telemetriSelenoids(Device $device) {
        if (! Gate::allows('update-device', $device)) {
            return back()->withErrors([
                'warning' => ["Anda tidak dapat mengakses data ini"]
            ]);
        }

        $fertilization_schedules = FertilizationSchedule::query()
            ->where('device_id', $device->id)
            ->whereNull('fertilization_id')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('bitanic.device.type-3.schedule.show-outside', compact('device', 'fertilization_schedules'));
    }

    public function kirimUlangSetting(Request $request, $id)
    {
        $fertilization = Fertilization::query()
            ->with([
                'device:id,device_series,type,farmer_id'
            ])
            ->find($id);

        if (!$fertilization) {
            return response()->json([
                'messages' => (object) [
                    'text' => ['Data tidak ditemukan']
                ]
            ], 404);
        }

        if (! Gate::allows('update-device', $fertilization->device)) {
            return back()->withErrors([
                'warning' => ["Anda tidak dapat mengakses data ini"]
            ]);
        }

        $topic = 'bitanic/'.$fertilization->device->device_series;
        $message = (object) [
            "mode" => "auto",
            "jadwal" => (object) [
                "minggu" => (int) $fertilization->weeks,
                "air" => $fertilization->valves->watering,
                "pemupukan" => $fertilization->valves->fertilization
            ]
        ];

        // set minggu
        MQTT::publish($topic, json_encode($message), false, config('app.mqtt'));

        return response()->json([
            'message' => 'Setting dikirim. Periksa kembali perangkat anda.'
        ], 200);
    }

    public function hentikanPemupukan(Request $request, $id)
    {
        $fertilization = Fertilization::query()
            ->with([
                'device:id,device_series,type,farmer_id'
            ])
            ->find($id);

        if (!$fertilization) {
            return response()->json([
                'messages' => (object) [
                    'text' => ['Data tidak ditemukan']
                ]
            ], 404);
        }

        if (! Gate::allows('update-device', $fertilization->device)) {
            return back()->withErrors([
                'warning' => ["Anda tidak dapat mengakses data ini"]
            ]);
        }

        $topic = 'bitanic/'.$fertilization->device->device_series;

        $message = (object) [
            "mode" => "auto",
            "jadwal" => (object) [
                "minggu" => 0,
                "air" => null,
                "pemupukan" => null
            ]
        ];

        // set minggu
        MQTT::publish($topic, json_encode($message), false, config('app.mqtt'));

        $fertilization->is_finished = 1;
        $fertilization->save();

        return response()->json([
            'message' => 'Setting dikirim. Periksa kembali perangkat anda.'
        ], 200);
    }
}
