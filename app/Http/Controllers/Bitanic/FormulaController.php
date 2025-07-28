<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\Crop;
use App\Models\Formula;
use App\Models\NecessityDifference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FormulaController extends Controller
{
    public function index() {
        $formulas = Formula::query()
            ->when((auth()->user()->role == 'farmer'), function($query, $role){
                return $query->where('user_id', auth()->id());
            })
            ->when(request()->query('search'), function($query, $role){
                $search = request()->query('search');
                return $query->where(function($query)use($search){
                    $query->whereHas('crop', function($fm)use($search){
                        $fm->where('crop_name', 'LIKE', '%'.$search.'%');
                    });
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('bitanic.formula.index', compact('formulas'));
    }

    public function create() {
        $crops = Crop::query()
            ->pluck('crop_name', 'id');


        $list_pilihan_pupuk = [
            [
                'pilihan_pupuk' => "ammonium nitrate",
                'choice' => 0,
                'name' => 'amnit'
            ],[
                'pilihan_pupuk' => "ammonium sulfate (ZA)",
                'choice' => 0,
                'name' => 'amsul'
            ],[
                'pilihan_pupuk' => "super phospate (SP36)",
                'choice' => "ok",
                'name' => 'sp36'
            ],[
                'pilihan_pupuk' => "potassium chloride (KCI)",
                'choice' => "ok",
                'name' => 'kci'
            ],[
                'pilihan_pupuk' => "potassium sulfate (ZK)",
                'choice' => 0,
                'name' => 'potsul'
            ],[
                'pilihan_pupuk' => "magnesium sulfate",
                'choice' => 0,
                'name' => 'magsul'
            ],[
                'pilihan_pupuk' => "frits",
                'choice' => "ok",
                'name' => 'frits'
            ],[
                'pilihan_pupuk' => "urea",
                'choice' => "ok",
                'name' => 'urea'
            ]
        ];

        return view('bitanic.formula.create', compact('crops', 'list_pilihan_pupuk'));
    }

    public function store(Request $request) {
        $v = Validator::make($request->all(), [
            "nama_petani" => 'required|string|max:250',
            "luas_lahan" => 'required|numeric|min:0',
            "latitude" => 'nullable|string',
            "longitude" => 'nullable|string',
            "altitude" => 'nullable|string',
            'alamat' => 'nullable|string|max:500',
            "tanaman_id" => 'required|exists:crops,id',
            "jenis_tanah" => 'required|string',
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
                "pupuk_amsul",
                "pupuk_sp36",
                "pupuk_kci",
                "pupuk_potsul",
                "pupuk_frits",
                "pupuk_urea",
            ]),
        ]);

        return redirect()->route('bitanic.formula.index');
    }



    public function show($id)
    {
        // if (session()->missing('formula')) {
        //     return redirect()->route('ferads.dashboard.index')->withErrors([
        //         'messages' => ['Harap isi formula']
        //     ]);
        // }

        $formula = Formula::query()
            ->with([
                'crop'
            ])
            ->findOrFail($id);

        // $request = session('formula');
        $request = (object) $formula->formula;

        $data['request'] = (array) $request;
        $data['perangkat'] = $formula->perangkat;

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

        $data['jenis_tanah'] = $request->jenis_tanah;

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

        return view('bitanic.formula.show', $data);
    }

    public function destroy(Request $request, Formula $formula) {
        $formula->delete();

        if ($request->acceptsJson()) {
            return response()->json([
                'message' => 'Berhasil dihapus'
            ]);
        }

        return redirect()->route('bitanic.formula.index')->with('success', 'Berhasil dihapus');
    }
}
