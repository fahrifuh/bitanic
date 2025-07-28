<?php

namespace App\Http\Controllers\Api\Mobile\v2;

use App\Http\Controllers\Controller;
use App\Models\Crop;
use App\Models\Device;
use App\Models\Fertilization;
use App\Models\Formula;
use App\Models\Interpretation;
use App\Models\NecessityDifference;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use PhpMqtt\Client\Facades\MQTT;

class DeviceController extends Controller
{
    public function getScheduleTimes(Request $request, Device $device) {
        if (! Gate::allows('update-device', $device)) {
            abort(403);
        }

        $request->validate([
            "nama_petani" => 'required|string|max:250',
            "luas_lahan" => 'required|numeric|min:0',
            "latitude" => 'nullable|string',
            "longitude" => 'nullable|string',
            "altitude" => 'nullable|string',
            'alamat' => 'nullable|string|max:500',
            "tanaman_id" => 'required|integer|min:0',
            // "jenis_tanah" => 'required|exists:soil_types,id',
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
            "input_mg" => 'required|numeric',
            "mg_status" => 'required|in:Sangat Rendah,Rendah,Sedang,Tinggi,Sangat Tinggi',
            "input_ca" => 'required|numeric',
            "ca_status" => 'required|in:Sangat Rendah,Rendah,Sedang,Tinggi,Sangat Tinggi',
            "input_corganik" => 'required|numeric',
            "corganik_status" => 'required|in:Sangat Rendah,Rendah,Sedang,Tinggi,Sangat Tinggi',
            "input_ph" => 'required|numeric',
            "pupuk_amsul" => 'required|in:1,0',
            "pupuk_sp36" => 'required|in:1,0',
            "pupuk_kci" => 'required|in:1,0',
            "pupuk_potsul" => 'required|in:1,0',
            "pupuk_frits" => 'required|in:1,0',
            "pupuk_urea" => 'required|in:1,0',
        ]);

        $recommend = Crop::query()
            ->findOrFail($request->tanaman_id);

        $data['request'] = $request->all();

        $data['amsul']['per_hektar_total'] = $request->pupuk_amsul != 1 ? 0 : ($request->dosis_n * 100 / 21);
        $data['amsul']['per_hektar_pre'] = $request->pupuk_amsul != 1 ? 0 : $request->preplant_n / 100 * $data['amsul']['per_hektar_total'];
        $data['amsul']['per_hektar_drip'] = $request->pupuk_amsul != 1 ? 0 : $data['amsul']['per_hektar_total'] - $data['amsul']['per_hektar_pre'];
        $data['amsul']['lahan_pre'] = $request->pupuk_amsul != 1 ? 0 : ($data['amsul']['per_hektar_pre'] * $request->luas_lahan / 10000);
        $data['amsul']['lahan_drip'] = $request->pupuk_amsul != 1 ? 0 : ($data['amsul']['per_hektar_drip'] * $request->luas_lahan / 10000);
        $data['amsul']['salt_index_unit'] = $request->pupuk_amsul != 1 ? 0 : round(1.618, 3);
        $data['amsul']['salt_index_formalition'] = $request->pupuk_amsul != 1 ? 0 : ((int)($request->dosis_n/9.08) * $data['amsul']['salt_index_unit']);

        $data['sp36']['per_hektar_total'] = $request->pupuk_sp36 != 1 ? 0 : ($request->dosis_p2o5 * 100 / 36);
        $data['sp36']['per_hektar_pre'] = $request->pupuk_sp36 != 1 ? 0 : ($request->preplant_p2o5 / 100 * $data['sp36']['per_hektar_total']);
        $data['sp36']['per_hektar_drip'] = $request->pupuk_sp36 != 1 ? 0 : $data['sp36']['per_hektar_total'] - $data['sp36']['per_hektar_pre'];
        $data['sp36']['lahan_pre'] = $request->pupuk_sp36 != 1 ? 0 : ($data['sp36']['per_hektar_pre'] * $request->luas_lahan / 10000);
        $data['sp36']['lahan_drip'] = $request->pupuk_sp36 != 1 ? 0 : ($data['sp36']['per_hektar_drip'] * $request->luas_lahan / 10000);
        $data['sp36']['salt_index_unit'] = $request->pupuk_sp36 != 1 ? 0 : round(0.390, 3);
        $data['sp36']['salt_index_formalition'] = $request->pupuk_sp36 != 1 ? 0 : ((int)($request->dosis_p2o5/9.08) * $data['sp36']['salt_index_unit']);

        $data['kci']['per_hektar_total'] = $request->pupuk_kci != 1 ? 0 : ($request->dosis_k2o * 100 / 50);
        $data['kci']['per_hektar_pre'] = $request->pupuk_kci != 1 ? 0 : ($request->preplant_k2o / 100 * $data['kci']['per_hektar_total']);
        $data['kci']['per_hektar_drip'] = $request->pupuk_kci != 1 ? 0 : $data['kci']['per_hektar_total'] - $data['kci']['per_hektar_pre'];
        $data['kci']['lahan_pre'] = $request->pupuk_kci != 1 ? 0 : ($data['kci']['per_hektar_pre'] * $request->luas_lahan / 10000);
        $data['kci']['lahan_drip'] = $request->pupuk_kci != 1 ? 0 : ($data['kci']['per_hektar_drip'] * $request->luas_lahan / 10000);
        $data['kci']['salt_index_unit'] = $request->pupuk_kci != 1 ? 0 : round(1.936, 3);
        $data['kci']['salt_index_formalition'] = $request->pupuk_kci != 1 ? 0 : ((int)($request->dosis_k2o/9.08) * $data['kci']['salt_index_unit']);

        $data['potsul']['per_hektar_total'] = $request->pupuk_potsul != 1 ? 0 : ($request->dosis_k2o * 100 / 60);
        $data['potsul']['per_hektar_pre'] = $request->pupuk_potsul != 1 ? 0 : ($request->preplant_k2o / 100 * $data['potsul']['per_hektar_total']);
        $data['potsul']['per_hektar_drip'] = $request->pupuk_potsul != 1 ? 0 : $data['potsul']['per_hektar_total'] - $data['potsul']['per_hektar_pre'];
        $data['potsul']['lahan_pre'] = $request->pupuk_potsul != 1 ? 0 : ($data['potsul']['per_hektar_pre'] * $request->luas_lahan / 10000);
        $data['potsul']['lahan_drip'] = $request->pupuk_potsul != 1 ? 0 : ($data['potsul']['per_hektar_drip'] * $request->luas_lahan / 10000);
        $data['potsul']['salt_index_unit'] = $request->pupuk_potsul != 1 ? 0 : round(1.936, 3);
        $data['potsul']['salt_index_formalition'] = $request->pupuk_potsul != 1 ? 0 : ((int)($request->dosis_k2o/9.08) * $data['potsul']['salt_index_unit']);

        $data['frit']['per_hektar_total'] = $request->pupuk_frits != 1 ? 0 : (int) $request->dosis_frit;
        $data['frit']['per_hektar_pre'] = $request->pupuk_frits != 1 ? 0 : ($request->preplant_frit / 100 * $data['frit']['per_hektar_total']);
        $data['frit']['per_hektar_drip'] = $request->pupuk_frits != 1 ? 0 : $data['frit']['per_hektar_total'] - $data['frit']['per_hektar_pre'];
        $data['frit']['lahan_pre'] = $request->pupuk_frits != 1 ? 0 : round(($data['frit']['per_hektar_pre'] * $request->luas_lahan / 10000), 1, PHP_ROUND_HALF_DOWN);
        $data['frit']['lahan_drip'] = $request->pupuk_frits != 1 ? 0 : round(($data['frit']['per_hektar_drip'] * $request->luas_lahan / 10000), 1, PHP_ROUND_HALF_DOWN);
        $data['frit']['salt_index_unit'] = $request->pupuk_frits != 1 ? 0 : round(1.0, 3);
        $data['frit']['salt_index_formalition'] = 0;

        // dd($request->dosis_n);

        $data['urea']['per_hektar_total'] = $request->pupuk_urea != 1 ? 0 : ($request->dosis_n * 100 / 46);
        $data['urea']['per_hektar_pre'] = $request->pupuk_urea != 1 ? 0 : $request->preplant_n / 100 * $data['urea']['per_hektar_total'];
        $data['urea']['per_hektar_drip'] = $request->pupuk_urea != 1 ? 0 : $data['urea']['per_hektar_total'] - $data['urea']['per_hektar_pre'];
        $data['urea']['lahan_pre'] = $request->pupuk_urea != 1 ? 0 : ($data['urea']['per_hektar_pre'] * $request->luas_lahan / 10000);
        $data['urea']['lahan_drip'] = $request->pupuk_urea != 1 ? 0 : ($data['urea']['per_hektar_drip'] * $request->luas_lahan / 10000);
        $data['urea']['salt_index_unit'] = $request->pupuk_urea != 1 ? 0 : round(1.618, 3);
        $data['urea']['salt_index_formalition'] = $request->pupuk_urea != 1 ? 0 : ((int)($request->dosis_n/9.08) * $data['urea']['salt_index_unit']);

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

        $data['ukuran_petak']['penjang_bendengan'] = 5;
        $data['ukuran_petak']['lebar_bendengan'] = 1.5;
        $data['ukuran_petak']['luas_petak'] = $data['ukuran_petak']['penjang_bendengan'] * $data['ukuran_petak']['lebar_bendengan'];

        $data['dolomit']['ton_lahan'] = $data['dolomit']['ton_ha'] * $request->luas_lahan / 10000;

        $data['bahan_organik']['ton_ha'] = round(($request->selisih_corg * 1.724 / 100 * 2000000 / 1000), 3);
        $data['bahan_organik']['ton_lahan'] = round($data['bahan_organik']['ton_ha'] * $request->luas_lahan / 10000, 5);

        $kebutuhan_pupuk = 1.724 * $request->selisih_corg;

        $data['pupuk_organik']['ton_ha'] = round(($kebutuhan_pupuk / $request->pupuk_organik_corg * $data['bahan_organik']['ton_ha']), 3);
        $data['pupuk_organik']['ton_lahan'] = round($data['pupuk_organik']['ton_ha'] * $request->luas_lahan / 10000, 5);

        // $data['jenis_tanah'] = SoilType::find($request->jenis_tanah)->name;

        $data['pupuk_urea'] = round($data['urea']['lahan_pre'] + $data['urea']['lahan_drip']);
        $data['pupuk_sp36'] = round($data['sp36']['lahan_pre'] + $data['sp36']['lahan_drip']);
        $data['pupuk_kci'] = round($data['kci']['lahan_pre'] + $data['kci']['lahan_drip']);
        $data['dolomit']['rekomendasi_pupuk'] = round($data['dolomit']['ton_lahan'] * 1000);

        $pi = 3.14;
        $r = $device->toren_pemupukan->r > 0 ? $device->toren_pemupukan->r : 1;

        $v_fron_t_toren = round((($pi * pow($r, 2) * $device->toren_pemupukan->tinggi_awal) / 1000), 2);
        $downT = $device->toren_pemupukan->tinggi_awal - $device->toren_pemupukan->tinggi_akhir;
        $debit = round((($pi * pow($r, 2) * $downT) / 1000), 2);

        $result = $v_fron_t_toren / $debit;
        $minutes = floor(round(($result / $recommend->frekuensi_siram), 1));
        $seconds = floor((round(($result / $recommend->frekuensi_siram), 1) - $minutes) * 60);

        $vMinggu = round(($v_fron_t_toren / $recommend->frekuensi_siram), 2);
        $tMinggu = "$minutes:$seconds";

        $formula = Formula::create([
            'user_id' => auth()->id(),
            'crop_id' => $request->tanaman_id,
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
                "input_mg",
                "mg_status",
                "input_ca",
                "ca_status",
                "input_corganik",
                "corganik_status",
                "input_ph",
            ]) + [
                "pupuk_amsul" => $this->formatPupukStatus($request->pupuk_amsul),
                "pupuk_sp36" => $this->formatPupukStatus($request->pupuk_sp36),
                "pupuk_kci" => $this->formatPupukStatus($request->pupuk_kci),
                "pupuk_potsul" => $this->formatPupukStatus($request->pupuk_potsul),
                "pupuk_frits" => $this->formatPupukStatus($request->pupuk_frits),
                "pupuk_urea" => $this->formatPupukStatus($request->pupuk_urea),
            ],
        ]);

        return response()->json([
            'formula' => $formula,
            'times' => (object) [
                'weeks' => $recommend->frekuensi_siram,
                'v_per_weeks' => $vMinggu,
                'duration_per_weeks' => (object) [
                    'minutes' => $minutes,
                    'seconds' => $seconds,
                ]
            ]
        ]);
    }

    private function formatPupukStatus(string $status = "0") {
        if ($status == 1) {
            return "ok";
        } else {
            return 0;
        }
    }

    public function storeSecondSchedule(Request $request) : JsonResponse {
        $request->validate([
            'perangkat_id' => 'required|integer|min:0',
            'formula_id' => 'required|integer|min:0',
            'watering.days' => 'json|max:7',
            'watering.days.*' => 'in:senin,selasa,rabu,kamis,jum\'at,sabtu,minggu',
            'watering.setontimes' => 'json|min:1|max:5',
            'watering.setontimes.*.lands' => 'json',
            'watering.setontimes.*.lands.*.id' => 'required|integer|in:1,2,3,4,5,6',
            'watering.setontimes.*.lands.*.duration' => 'required|integer|min:1|max:60',
            'watering.setontimes.*.time' => 'required_with:watering.setontimes.*.lands|nullable|date_format:H:i',
            'fertilization.days' => 'json|min:1|max:7',
            'fertilization.days.*' => 'in:senin,selasa,rabu,kamis,jum\'at,sabtu,minggu',
            'fertilization.setontimes' => 'json|size:1',
            'fertilization.setontimes.*.lands' => 'json',
            'fertilization.setontimes.*.lands.*.id' => 'required|integer|in:1,2,3,4,5,6',
            'fertilization.setontimes.*.lands.*.duration' => 'required|integer|min:0|max:60',
            'fertilization.setontimes.*.lands.*.seconds' => 'required|integer|min:0|max:60',
            'fertilization.setontimes.*.time' => 'required_with:fertilization.setontimes.*.lands|nullable|date_format:H:i',
        ]);

        $perangkat = Device::query()
            ->withCount('fertilization')
            ->where('type', 3)
            ->where('id', $request->perangkat_id)
            ->firstOrFail();

        if ($perangkat->fertilization_count > 0) {
            return response()->json([
                'message' => 'Tidak bisa membuat pemupukan baru. Masih ada pemupukan yang sedang berjalan',
                'status' => 400
            ], 400);
        }

        $formula = Formula::query()
            ->with([
                'crop:id,frekuensi_siram,crop_name'
            ])
            ->where('user_id', auth()->id())
            ->findOrFail($request->formula_id);


        $json_watering = $request->watering ? json_decode($request->watering) : null;
        $json_fertilization = $request->fertilization ? json_decode($request->fertilization) : null;

        if (!isset($json_watering->days) && !isset($json_fertilization->days)) {
            return response()->json([
                'errors' => [
                    'days' => ['Harap isi salah satu data hari!']
                ]
            ]);
        }

        $fertilizationSetontimes = collect([]);

        $newFertilization = null;
        $newWatering = null;

        if ($json_watering && isset($json_watering->days) && $json_watering->days) {
            try {
                [$wateringSetontimes, $waterTimesDuration] = $this->checkSetontimes("air", $json_watering->setontimes, $perangkat->delay);

                $newWatering = (object) ["days" => $json_watering->days, "setontimes" => $wateringSetontimes];
            } catch (Exception $th) {
                //throw $th;
                return response()->json([
                    'errors' => [
                        'setontime' => [$th->getMessage()]
                    ]
                ], 400);
            }
        }

        if ($json_fertilization && isset($json_fertilization->days) && $json_fertilization->days) {
            try {
                [$fertilizationSetontimes, $fertilizationTimeDuration] = $this->checkSetontimes("pupuk", $json_fertilization->setontimes, $perangkat->delay);

                $newFertilization = (object) ["days" => $json_fertilization->days, "setontimes" => $fertilizationSetontimes];
            } catch (Exception $th) {
                //throw $th;
                return response()->json([
                    'errors' => [
                        'setontime' => [$th->getMessage()]
                    ]
                ], 400);
            }
        }

        if ($newFertilization && $newWatering) {
            if (collect($newWatering->days)->intersect($newFertilization->days)->count() > 0) {
                for ($i=0; $i < count($waterTimesDuration); $i++) {
                    for ($j=0; $j < count($fertilizationTimeDuration); $j++) {
                        if (
                            $waterTimesDuration[$i]->start->between($fertilizationTimeDuration[$j]->start, $fertilizationTimeDuration[$j]->end) ||
                            $fertilizationTimeDuration[$j]->start->between($waterTimesDuration[$i]->start, $waterTimesDuration[$i]->end)
                        ) {
                            return response()->json([
                                'errors' => [
                                    'setontime' => ["Terdapat waktu yang bentrok antara penyiraman (" .
                                        $waterTimesDuration[$i]->start->copy()->format('H:i:s') .
                                        ") dengan pemupukan (" .
                                        $fertilizationTimeDuration[$j]->start->copy()->format('H:i:s') .
                                        ")"]
                                ]
                            ], 400);
                        }
                    }
                }
            }
        }

        $drip = $perangkat->toren_pemupukan->v_fron_t_toren / $formula->crop->frekuensi_siram;

        Fertilization::create([
            'device_id' => $perangkat->id,
            'farmer_id' => $perangkat->farmer_id,
            'crop_name' => $formula->crop->crop_name,
            'drip_out' => $drip,
            'weeks' => $formula->crop->frekuensi_siram,
            'valves' => (object) [
                'fertilization' => $newFertilization,
                'watering' => $newWatering,
            ],
            'formula' => $formula->formula
        ]);

        $topic = 'bitanic/'.$perangkat->device_series;

        $message = (object) [
            "mode" => "auto",
            "jadwal" => (object) [
                "minggu" => (int) $formula->crop->frekuensi_siram,
                "air" => $newWatering,
                "pemupukan" => $newFertilization
            ]
        ];

        // set minggu
        MQTT::publish($topic, json_encode($message), false, config('app.mqtt'));

        return response()->json([
            'message' => 'Berhasil disimpan',
            'status' => 200
        ], 200);
    }

    private function checkSetontimes(string $type, array $setontimes = [], int $delay = 5) : Array {
        $collectSetontimes = collect([]);
        $timeDurations = collect([]);
        $checkTime = null;
        $k = 1;
        foreach ($setontimes as $setontime) {
            if (isset($setontime->lands)) {
                $time = now('Asia/Jakarta')->parse($setontime->time);
                if ($checkTime && $time->lte($checkTime)) { // check if current time is smaller than previous time
                    throw new Exception("Waktu $k tidak bisa KURANG atau SAMA dari waktu sebelum!");
                }
                $i = 0;
                $collLands = collect();
                $totalMinutes = 0;
                $totalSeconds = 0;
                foreach ($setontime->lands as $key => $land) {
                    $landMinutes = (int) $land->duration ?? 0;
                    $landSeconds = (int) $land->seconds ?? 0;

                    $arrVal = [
                        "id" => (int) $land->id,
                        "duration" => $landMinutes,
                        "seconds" => $landSeconds
                    ];

                    if ($type == "pupuk") {
                        $arrVal = array_merge($arrVal, [
                            "first_water" => (object) [
                                "minutes" => (int) $land->first_water->minutes,
                                "seconds" => (int) $land->first_water->seconds,
                            ],
                            "last_water" => (object) [
                                "minutes" => (int) $land->last_water->minutes,
                                "seconds" => (int) $land->last_water->minutes,
                            ],
                        ]);

                        $landMinutes += (int) $land->first_water->minutes;
                        $landSeconds += (int) $land->first_water->seconds;
                        $landMinutes += (int) $land->last_water->minutes;
                        $landSeconds += (int) $land->last_water->seconds;
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

                $timeDurations->push((object) [
                    "start" => $time->copy(),
                    "end" => $time->copy()->addMinutes($totalMinutes)->addSeconds(($delay * (count($setontime->lands) - 1)) + $totalSeconds),
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

    public function resetPerangkat(Request $request, Fertilization $fertilization)
    {
        if (! Gate::allows('delete-fertilization', $fertilization)) {
            abort(403);
        }

        if ($fertilization->status_selesai != 0) {
            return response()->json([
                'messages' => [
                    'pemupukan' => ["Pemupukan yang dipilih sudah selesai."]
                ]
            ], 400);
        }

        $fertilization->load('device:id,device_series,type,farmer_id');


        $topic = 'bitanic/'.$fertilization->device->device_series;

        if ($fertilization->device->type == 1) {
            MQTT::publish($topic, 'RESETALL,*');
        } elseif ($fertilization->device->type == 3) {
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

        $fertilization->delete();

        return response()->json([
            'message' => 'Setting dikirim dan pemupukan telah dihapus. Periksa kembali perangkat anda.',
            'status' => 200
        ], 200);
    }

    public function getAllIntepretasiStatus() {
        $unsur = collect(Interpretation::with('level_interpretation:id,interpretation_id,sangat_rendah,rendah,sedang,tinggi,sangat_tinggi')->get());

        $collUnsur = $unsur->mapWithKeys(function ($item, $key) {
            return [$item->nama => $item->level_interpretation];
        })->all();

        return response()->json($collUnsur);
    }
}
