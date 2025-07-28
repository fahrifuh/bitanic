<x-app-layout>
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master / <a href="{{ route('bitanic.formula.index') }}">Formula</a> / </span>Buat Formula</h4>
    </x-slot>
    {{-- End Header --}}
    <div class="row">

        <div class="col-md-12">
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>

        {{-- Informasi Umum --}}
        <div class="col-lg-6 mb-4 order-0">
            {{-- Card --}}
            <div class="card">
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped table-bordered">
                        <thead class="bg-danger">
                            <tr>
                                <th colspan="3" class="align-middle text-white text-center">INFORMASI UMUM</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <tr>
                                <td class="text-uppercase">Nama Petani</td>
                                <td colspan="2" class="p-0"><input type="text" class="form-control rounded-0" id="data-input-nama-petani" placeholder="Masukan nama..." value="{{ old('nama_petani') }}"></td>
                            </tr>
                            <tr>
                                <td class="text-uppercase">Luas Lahan</td>
                                <td class="p-0"><input type="number" min="0" class="form-control" id="data-input-luas-lahan" placeholder="Masukan luas..." value="{{ old('luas_lahan') }}"></td>
                                <td>&#13217;</td>
                            </tr>
                            <tr>
                                <td class="text-uppercase">latitude</td>
                                <td class="p-0"><input type="text" class="form-control" id="data-input-latitude" placeholder="Masukan latitude..." value="{{ old('latitude') }}"></td>
                                <td>GPS</td>
                            </tr>
                            <tr>
                                <td class="text-uppercase">longitude</td>
                                <td class="p-0"><input type="text" class="form-control" id="data-input-longitude" placeholder="Masukan longitude..." value="{{ old('longitude') }}"></td>
                                <td>GPS</td>
                            </tr>
                            <tr>
                                <td class="text-uppercase">altitude</td>
                                <td class="p-0"><input type="number" class="form-control" id="data-input-altitude" placeholder="Masukan altitude..." value="{{ old('altitude') }}"></td>
                                <td>m dpl</td>
                            </tr>
                            <tr>
                                <td class="text-uppercase">alamat</td>
                                <td class="p-0" colspan="2">
                                    <textarea class="form-control" name="alamat" id="data-input-alamat" rows="2" placeholder="Masukan alamat...">{{ old('alamat') }}</textarea>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- End Card --}}
            <div class="input-group input-group-lg mt-4">
                <label class="input-group-text text-uppercase bg-white text-dark" for="inputGroupSelect01">tanaman</label>
                <select class="form-select custom-bg-cyan text-dark" id="input-data-pilih-tanaman">
                    <option value="">Pilih...</option>

                    @foreach ($crops as $id => $crop)
                    <option value="{{ $id }}" @if(old('tanaman_id') && old('tanaman_id')==$id) selected @endif>{{ $crop }}</option>
                    @endforeach

                </select>
            </div>
        </div>
        {{-- End Informasi Umum --}}

        {{-- Analisis Tanah --}}
        <div class="col-lg-6 mb-4 order-1">
            {{-- Card --}}
            <div class="card h-100">
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped table-bordered">
                        <thead class="bg-danger">
                            <tr>
                                <th colspan="3" class="align-middle text-white text-center text-uppercase">analisis tanah</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <tr>
                                <td class="text-uppercase">jenis tanah</td>
                                <td colspan="2" class="p-0"><input type="text" class="form-control @error('jenis_tanah') is-invalid @enderror" value="{{ old('jenis_tanah') }}" id="data-input-jenis-tanah" placeholder="Masukan jenis tanah..."></td>
                            </tr>
                            <tr>
                                <td>P-tersedia Mechlih-1</td>
                                <td class="p-0"><input type="number" step="0.1" class="form-control @error('input_p') is-invalid @enderror" value="{{ old('input_p_tersedia') }}" id="data-input-analisis-tanah-p" data-intepretasi-unsur="P" placeholder="Masukan ppm..."></td>
                                <td>ppm</td>
                            </tr>
                            <tr>
                                <td>K-tersedia Mechlih-1</td>
                                <td class="p-0"><input type="number" step="0.1" class="form-control @error('input_k') is-invalid @enderror" value="{{ old('input_k_tersedia') }}" id="data-input-analisis-tanah-k" data-intepretasi-unsur="K" placeholder="Masukan ppm..."></td>
                                <td>ppm</td>
                            </tr>
                            <tr>
                                <td>Mg</td>
                                <td class="p-0"><input type="number" step="0.1" class="form-control" id="data-input-analisis-tanah-mg" data-intepretasi-unsur="Mg" value="{{ old('input_mg') }}" placeholder="Masukan ppm..."></td>
                                <td>ppm</td>
                            </tr>
                            <tr>
                                <td>Ca</td>
                                <td class="p-0"><input type="number" step="0.1" class="form-control" id="data-input-analisis-tanah-ca" data-intepretasi-unsur="Ca" value="{{ old('input_ca') }}" placeholder="Masukan ppm..."></td>
                                <td>ppm</td>
                            </tr>
                            <tr>
                                <td>C-Organic</td>
                                <td class="p-0"><input type="number" step="0.1" class="form-control" id="data-input-analisis-tanah-corganic" data-intepretasi-unsur="Corg" placeholder="Masukan persen(%)" value="{{ old('input_corganik') }}"></td>
                                <td>%</td>
                            </tr>
                            <tr>
                                <td>pH</td>
                                <td class="p-0"><input type="number" step="0.1" class="form-control" id="data-input-analisis-tanah-ph" placeholder="Masukan level..." value="{{ old('input_ph') }}"></td>
                                <td>level</td>
                            </tr>
                            <tr>
                                <td>TARGET pH</td>
                                <td id="target-ph">-</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>SELISIH pH</td>
                                <td id="selisih-ph">-</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>TARGET C-Organic</td>
                                <td id="target-corganik">-</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>SELISIH C-Organic</td>
                                <td id="selisih-corganik">-</td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- End Card --}}
        </div>
        {{-- End Analisis Tanah --}}


        {{-- Intepretasi Kesuburan Tanah --}}
        <div class="col-lg-6 mb-4 order-2">
            {{-- Card --}}
            <div class="card">
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered">
                        <thead>
                            <tr class="bg-danger">
                                <th colspan="3" class="align-middle text-white text-center text-uppercase">intepretasi kesuburan tanah</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0 bg-warning">
                            <tr class="bg-mechli-1">
                                <td class="text-uppercase text-white">analisis tanah</td>
                                <td class="text-white text-center">ppm</td>
                                <td class="text-uppercase text-white">intepretasi</td>
                            </tr>
                            <tr>
                                <td data-set-unsur="P" class="intepretasi fw-bold text-dark">P</td>
                                <td data-set-unsur="P" class="input fw-bold text-dark">{{ old('input_p_tersedia') }}</td>
                                <td data-set-unsur="P" class="status fw-bold text-dark">{{ old('p_status') }}</td>
                            </tr>
                            <tr>
                                <td data-set-unsur="K" class="intepretasi fw-bold text-dark">K</td>
                                <td data-set-unsur="K" class="input fw-bold text-dark">{{ old('input_k_tersedia') }}</td>
                                <td data-set-unsur="K" class="status fw-bold text-dark">{{ old('k_status') }}</td>
                            </tr>
                            <tr>
                                <td data-set-unsur="Mg" class="intepretasi fw-bold text-dark">Mg</td>
                                <td data-set-unsur="Mg" class="input fw-bold text-dark">{{ old('input_mg') }}</td>
                                <td data-set-unsur="Mg" class="status fw-bold text-dark">{{ old('mg_status') }}</td>
                            </tr>
                            <tr>
                                <td data-set-unsur="Ca" class="intepretasi fw-bold text-dark">Ca</td>
                                <td data-set-unsur="Ca" class="input fw-bold text-dark">{{ old('input_ca') }}</td>
                                <td data-set-unsur="Ca" class="status fw-bold text-dark">{{ old('ca_status') }}</td>
                            </tr>
                            <tr>
                                <td data-set-unsur="Corg" class="intepretasi fw-bold text-dark">C-Org</td>
                                <td data-set-unsur="Corg" class="input fw-bold text-dark">{{ old('input_corganik') }}</td>
                                <td data-set-unsur="Corg" class="status fw-bold text-dark">{{ old('corganik_status') }}</td>
                            </tr>
                            <tr>
                                <td id="intepretasi-ph" class="fw-bold text-dark">pH</td>
                                <td id="input-ph" class="fw-bold text-dark">{{ old('input_ph') }}</td>
                                <td id="status-ph" class="fw-bold text-dark">Level</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- End Card --}}
        </div>
        {{-- End Intepretasi Kesuburan Tanah --}}

        {{-- Pilihan Pupuk --}}
        <div class="col-lg-6 mb-4 order-3">
            {{-- Card --}}
            <div class="card">
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered">
                        <thead>
                            <tr class="bg-danger">
                                <th colspan="3" class="align-middle text-white text-center text-uppercase">Pilihan Pupuk</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <tr>
                                <td class="text-uppercase text-dark fw-bold bg-warning">Pilihan Pupuk</td>
                                <td class="text-uppercase text-white text-center bg-danger" style="width: 20%;">choice</td>
                            </tr>
                            @foreach ($list_pilihan_pupuk as $pupuk)

                            <tr>
                                <td class="text-uppercase fw-bold text-dark custom-bg-cyan">{{ $pupuk['pilihan_pupuk'] }}</td>
                                <td class="text-uppercase fw-bold text-dark text-center p-0">
                                    <select class="form-select custom-bg-yellow-light" name="pilihan_pupuk_{{ $pupuk['name'] }}" id="pilihan-pupuk-{{ $pupuk['name'] }}" @if($pupuk['name']=="magsul" || $pupuk['name']=="amnit" ) disabled @endif @if($pupuk['name']=="magsul" || $pupuk['name']=="amnit" ) title="Belum tersedia!" @endif>
                                        <option value="0" @if($pupuk['choice']==0) selected @endif>0</option>
                                        <option value="ok" @if($pupuk['choice']==="ok" ) selected @endif>OK</option>
                                    </select>
                                </td>
                            </tr>

                            @endforeach
                            <tr>
                                <td class="text-white fw-bold bg-danger">PUPUK ORGANIK (%C-org)</td>
                                <td class="text-dark fw-bold bg-white p-0">
                                    <input type="number" class="form-control bg-white text-center text-dark" id="data-input-pupuk-organik-corg" value="{{ old('pupuk_organik_corg') ?? 12 }}">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- End Card --}}
        </div>
        {{-- End Pilihan Pupuk --}}

        {{-- Formalisasi Aplikasi Pupuk --}}
        <div class="col-lg-12 mb-4 order-4">
            {{-- Card --}}
            <div class="card">
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered">
                        <thead>
                            <tr class="bg-danger">
                                <th colspan="7" class="align-middle text-white text-center text-uppercase">Formulasi Aplikasi Pupuk</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <tr class="bg-primary">
                                <td rowspan="2" class="text-uppercase text-white fw-bold text-center">komponen</td>
                                <td rowspan="2" class="text-uppercase text-white fw-bold text-center">n</td>
                                <td colspan="2" class="text-uppercase text-white fw-bold text-center">phosphor</td>
                                <td colspan="2" class="text-uppercase text-white fw-bold text-center">potassium</td>
                                <td rowspan="2" class="text-white fw-bold text-center">Frit</td>
                            </tr>
                            <tr class="bg-primary">
                                <td class="text-uppercase text-white fw-bold text-center">p</td>
                                <td class="text-uppercase text-white fw-bold text-center">p2o5</td>
                                <td class="text-uppercase text-white fw-bold text-center">k</td>
                                <td class="text-uppercase text-white fw-bold text-center">k2o</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-dark custom-bg-cyan">DOSIS (kg/ha)</td>
                                <td class="fw-bold text-dark text-center bg-warning" id="rekomendasi-n"></td>
                                <td class="fw-bold text-dark text-center bg-warning"></td>
                                <td class="fw-bold text-dark text-center bg-warning" id="rekomendasi-p2o5"></td>
                                <td class="fw-bold text-dark text-center bg-warning"></td>
                                <td class="fw-bold text-dark text-center bg-warning" id="rekomendasi-k2o"></td>
                                <td class="fw-bold text-dark text-center bg-white p-0">
                                    <input type="number" class="form-control bg-white text-dark" id="data-input-rekomendasi-frit" value="{{ old('dosis_frit') ?? 30 }}">
                                </td>
                            </tr>
                            <tr>
                                <td colspan="7" class="fw-bold text-uppercase text-center bg-info text-dark">metode aplikasi</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-dark custom-bg-cyan">PREPLANT (%)</td>
                                <td class="fw-bold text-dark text-center bg-warning p-0" id="preplant-n"><input type="number" min="0" max="100" class="form-control bg-white text-dark" id="data-input-preplant-n" value="{{ old('preplant_n') }}" disabled></td>
                                <td class="fw-bold text-dark text-center bg-warning"></td>
                                <td class="fw-bold text-dark text-center bg-warning p-0" id="preplant-p2o5"><input type="number" min="0" max="100" class="form-control bg-white text-dark" id="data-input-preplant-p2o5" value="{{ old('preplant_p2o5') }}" disabled></td>
                                <td class="fw-bold text-dark text-center bg-warning"></td>
                                <td class="fw-bold text-dark text-center bg-warning p-0" id="preplant-k2o"><input type="number" min="0" max="100" class="form-control bg-white text-dark" id="data-input-preplant-k2o" value="{{ old('preplant_k2o') }}" disabled></td>
                                <td class="fw-bold text-dark text-center bg-white p-0" id="preplant-frit"><input type="number" min="0" max="100" class="form-control bg-white text-dark" id="data-input-preplant-frit" value="{{ old('preplant_frit') }}"></td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-dark custom-bg-cyan">DRIP (%)</td>
                                <td class="fw-bold text-dark text-center bg-warning" id="drip-n">{{ old('drip_n') }}</td>
                                <td class="fw-bold text-dark text-center bg-warning"></td>
                                <td class="fw-bold text-dark text-center bg-warning" id="drip-p2o5">{{ old('drip_p2o5') }}</td>
                                <td class="fw-bold text-dark text-center bg-warning"></td>
                                <td class="fw-bold text-dark text-center bg-warning" id="drip-k2o">{{ old('drip_k2o') }}</td>
                                <td class="fw-bold text-dark text-center bg-warning" id="drip-frit">{{ old('drip_frit') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-dark custom-bg-cyan">FREKUENSI DRIP</td>
                                <td class="fw-bold text-dark text-center bg-warning" id="frekuensi-n"></td>
                                <td class="fw-bold text-dark text-center bg-warning"></td>
                                <td class="fw-bold text-dark text-center bg-warning" id="frekuensi-p2o5"></td>
                                <td class="fw-bold text-dark text-center bg-warning"></td>
                                <td class="fw-bold text-dark text-center bg-warning" id="frekuensi-k2o"></td>
                                <td class="fw-bold text-dark text-center bg-warning" id="frekuensi-frit"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- End Card --}}
        </div>
        {{-- End Formalisasi Aplikasi Pupuk --}}



        {{-- Buttom Output --}}
        <div class="col-lg-12 mb-4 order-5">
            {{-- Button --}}
            <button class="btn btn-danger float-end" id="btn-output">Hasil Formulasi</button>
            {{-- End Button --}}
        </div>
        {{-- End Buttom Output --}}

    </div>

    <form action="{{ route('bitanic.formula.store') }}" method="POST" id="form-output">
        @csrf
        <input type="hidden" name="nama_petani">
        <input type="hidden" name="luas_lahan">
        <input type="hidden" name="latitude">
        <input type="hidden" name="longitude">
        <input type="hidden" name="altitude">
        <input type="hidden" name="alamat">
        <input type="hidden" name="tanaman_id">
        <input type="hidden" name="jenis_tanah">
        <input type="hidden" name="dosis_n">
        <input type="hidden" name="dosis_p2o5">
        <input type="hidden" name="dosis_k2o">
        <input type="hidden" name="dosis_frit">
        <input type="hidden" name="preplant_n">
        <input type="hidden" name="preplant_p2o5">
        <input type="hidden" name="preplant_k2o">
        <input type="hidden" name="preplant_frit">
        <input type="hidden" name="selisih_ph">
        <input type="hidden" name="selisih_corg">
        <input type="hidden" name="pupuk_organik_corg">
        <input type="hidden" name="drip_n">
        <input type="hidden" name="drip_p2o5">
        <input type="hidden" name="drip_k2o">
        <input type="hidden" name="drip_frit">
        <input type="hidden" name="frekuensi_drip">
        <input type="hidden" name="input_p_tersedia">
        <input type="hidden" name="p_status">
        <input type="hidden" name="input_k_tersedia">
        <input type="hidden" name="k_status">
        <input type="hidden" name="input_mg">
        <input type="hidden" name="mg_status">
        <input type="hidden" name="input_ca">
        <input type="hidden" name="ca_status">
        <input type="hidden" name="input_corganik">
        <input type="hidden" name="corganik_status">
        <input type="hidden" name="input_ph">

        <input type="hidden" name="pupuk_amsul">
        <input type="hidden" name="pupuk_sp36">
        <input type="hidden" name="pupuk_kci">
        <input type="hidden" name="pupuk_potsul">
        <input type="hidden" name="pupuk_frits">
        <input type="hidden" name="pupuk_urea">

    </form>

    @push('scripts')
    {{-- Axios --}}
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script>
        let urlOneTanaman = "{{ route('bitanic.farmer.crops', 'ID') }}"
        let urlIntepretasiStatus = "{{ route('bitanic.interpretation.get-status') }}"
        let urlOutput = ""

        document.addEventListener("DOMContentLoaded", function() {
            console.log("Ready");

            const dataInputNamaPetani = document.getElementById('data-input-nama-petani')
            const dataInputLuasLahan = document.getElementById('data-input-luas-lahan')
            const dataInputLatitude = document.getElementById('data-input-latitude')
            const dataInputLongitude = document.getElementById('data-input-longitude')
            const dataInputAltitude = document.getElementById('data-input-altitude')
            const dataInputAlamat = document.getElementById('data-input-alamat')
            const dataInputJenisTanah = document.getElementById('data-input-jenis-tanah')
            const dataInputPupukOrganikCorg = document.getElementById('data-input-pupuk-organik-corg')
            const optionTanaman = document.getElementById('input-data-pilih-tanaman')
            const optionPerangkat = document.getElementById('input-data-pilih-perangkat')
            const inputDataPh = document.getElementById('data-input-analisis-tanah-ph')
            const intepretasiPH = document.getElementById('input-ph')
            const targetPh = document.getElementById('target-ph')
            const eTargetCorg = document.getElementById('target-corganik')
            const inputDataP = document.getElementById('data-input-analisis-tanah-p')
            const inputDataK = document.getElementById('data-input-analisis-tanah-k')
            const inputDataMg = document.getElementById('data-input-analisis-tanah-mg')
            const inputDataCa = document.getElementById('data-input-analisis-tanah-ca')
            const inputDataCorg = document.getElementById('data-input-analisis-tanah-corganic')
            const rekomendasiN = document.getElementById('rekomendasi-n')
            const rekomendasiP2o5 = document.getElementById('rekomendasi-p2o5')
            const rekomendasiK2o = document.getElementById('rekomendasi-k2o')
            const dripN = document.getElementById('drip-n')
            const dripP2o5 = document.getElementById('drip-p2o5')
            const dripK2o = document.getElementById('drip-k2o')
            const dripFrit = document.getElementById('drip-frit')
            const frekuensiN = document.getElementById('frekuensi-n')
            const frekuensiP2o5 = document.getElementById('frekuensi-p2o5')
            const frekuensiK2o = document.getElementById('frekuensi-k2o')
            const frekuensiFrit = document.getElementById('frekuensi-frit')
            const dataInputPreplantN = document.getElementById('data-input-preplant-n')
            const dataInputPreplantP2o5 = document.getElementById('data-input-preplant-p2o5')
            const dataInputPreplantK2o = document.getElementById('data-input-preplant-k2o')
            const dataInputPreplantFrit = document.getElementById('data-input-preplant-frit')
            let selisihPh = document.getElementById('selisih-ph')
            let selisihCorg = document.getElementById('selisih-corganik')
            let drip = 0
            let nilaiDripN = parseInt("{{ old('drip_n') ?? 0 }}")
            let nilaiDripP2o5 = parseInt("{{ old('drip_p2o5') ?? 0 }}")
            let nilaiDripK2o = parseInt("{{ old('drip_k2o') ?? 0 }}")
            let nilaiDripFrit = parseInt("{{ old('drip_frit') ?? 0 }}")
            let dosisN = 0
            let dosisP2o5 = 0
            let dosisK2o = 0
            let nilaiSelisihPh = 0
            let targetCorg = 0
            let pStatus = ""
            let kStatus = ""
            let mgStatus = "{{ old('mg_status') }}"
            let caStatus = "{{ old('ca_status') }}"
            let corgStatus = "{{ old('corganik_status') }}"

            let config = {
                headers: {
                    'X-ferads-token': 'cPzC7advUBmnAJe1hx8P'
                }
            }

            async function getTanaman(tanamanID) {
                try {
                    if (!tanamanID) {
                        return false
                    }
                    let inputPh = parseFloat(!document.getElementById('data-input-analisis-tanah-ph').value ? 0 : document.getElementById('data-input-analisis-tanah-ph').value)
                    let inputCorg = parseFloat(!document.getElementById('data-input-analisis-tanah-corganic').value ? 0 : document.getElementById('data-input-analisis-tanah-corganic').value)
                    let response = await axios.get(urlOneTanaman.replace('ID', tanamanID), config);

                    let data = response.data


                    targetPh.innerHTML = parseFloat(data.target_ph)
                    eTargetCorg.innerHTML = parseFloat(data.target_persen_corganik)
                    selisihCorg.innerHTML = (parseFloat(data.target_persen_corganik) - inputCorg).toFixed(1)
                    selisihPh.innerHTML = (parseFloat(data.target_ph) - inputPh).toFixed(1)
                    nilaiSelisihPh = (parseFloat(data.target_ph) - inputPh).toFixed(1)
                    rekomendasiN.innerHTML = data.n_kg_ha
                    targetCorg = data.target_persen_corganik
                    dosisN = data.n_kg_ha
                    dataInputPreplantN.removeAttribute('disabled')
                    drip = data.frekuensi_siram

                    let hasil = 0

                    if (dataInputPreplantN.value) {
                        hasil = 100 - dataInputPreplantN.value
                        frekuensiN.innerHTML = (hasil > 0) ? drip : 0
                    }
                    if (dataInputPreplantP2o5.value) {
                        hasil = 100 - dataInputPreplantP2o5.value
                        frekuensiP2o5.innerHTML = (hasil > 0) ? drip : 0
                    }
                    if (dataInputPreplantK2o.value) {
                        hasil = 100 - dataInputPreplantK2o.value
                        frekuensiK2o.innerHTML = (hasil > 0) ? drip : 0
                    }
                    if (dataInputPreplantFrit.value) {
                        hasil = 100 - dataInputPreplantFrit.value
                        frekuensiFrit.innerHTML = (hasil > 0) ? drip : 0
                    }

                    let listUnsur = [
                        inputDataP,
                        inputDataK
                    ]

                    listUnsur.forEach(async ep => {
                        if (ep.value) {
                            const formData = new FormData();

                            let unsur = ep.dataset.intepretasiUnsur

                            formData.append('unsur', unsur)
                            formData.append('ppm', ep.value)

                            let responseInterpretasi = await axios.post(urlIntepretasiStatus, formData, config);

                            let statusData = responseInterpretasi.data.data

                            if (unsur == "P") {
                                pStatus = statusData
                            }

                            if (unsur == "K") {
                                kStatus = statusData
                            }

                            if (unsur === "P" || unsur === "K") {
                                let changeDosis = (unsur === "P") ? rekomendasiP2o5 : rekomendasiK2o
                                let elementInput = (unsur === "P") ? dataInputPreplantP2o5 : dataInputPreplantK2o

                                elementInput.removeAttribute('disabled')

                                let dataTanaman = data
                                if (statusData == "Sangat Rendah") {
                                    changeDosis.innerHTML = (unsur === "P") ? dataTanaman.sangat_rendah_p2o5 : dataTanaman.sangat_rendah_k2o
                                    if (unsur == "P") {
                                        dosisP2o5 = dataTanaman.sangat_rendah_p2o5
                                    } else {
                                        dosisK2o = dataTanaman.sangat_rendah_k2o
                                    }
                                }
                                if (statusData == "Rendah") {
                                    changeDosis.innerHTML = (unsur === "P") ? dataTanaman.rendah_p2o5 : dataTanaman.rendah_k2o
                                    if (unsur == "P") {
                                        dosisP2o5 = dataTanaman.rendah_p2o5
                                    } else {
                                        dosisK2o = dataTanaman.rendah_k2o
                                    }
                                }
                                if (statusData == "Sedang") {
                                    changeDosis.innerHTML = (unsur === "P") ? dataTanaman.sedang_p2o5 : dataTanaman.sedang_k2o
                                    if (unsur == "P") {
                                        dosisP2o5 = dataTanaman.sedang_p2o5
                                    } else {
                                        dosisK2o = dataTanaman.sedang_k2o
                                    }
                                }
                                if (statusData == "Tinggi") {
                                    changeDosis.innerHTML = (unsur === "P") ? dataTanaman.tinggi_p2o5 : dataTanaman.tinggi_k2o
                                    if (unsur == "P") {
                                        dosisP2o5 = dataTanaman.tinggi_p2o5
                                    } else {
                                        dosisK2o = dataTanaman.tinggi_k2o
                                    }
                                }
                                if (statusData == "Sangat Tinggi") {
                                    changeDosis.innerHTML = (unsur === "P") ? dataTanaman.sangat_tinggi_p2o5 : dataTanaman.sangat_tinggi_k2o
                                    if (unsur == "P") {
                                        dosisP2o5 = dataTanaman.sangat_tinggi_p2o5
                                    } else {
                                        dosisK2o = dataTanaman.sangat_tinggi_k2o
                                    }
                                }
                            }
                        }
                    });
                } catch (error) {
                    targetPh.innerHTML = ""
                    targetCorg.innerHTML = ""
                    selisihPh.innerHTML = ""

                    console.error(error.message);
                }
            }

            // console.log(optionTanaman.value);

            getTanaman(optionTanaman.value)

            optionTanaman.addEventListener("change", async e => {
                getTanaman(e.target.value)
            })


            const hitungSelisihPh = async e => {
                let inputPh = parseFloat(!e.target.value ? 0 : e.target.value)
                intepretasiPH.innerHTML = inputPh

                try {
                    let response = await axios.get(urlOneTanaman.replace('ID', optionTanaman.value), config);
                    let data = response.data

                    targetPh.innerHTML = parseFloat(data.target_ph)
                    selisihPh.innerHTML = (parseFloat(data.target_ph) - inputPh).toFixed(1)
                    nilaiSelisihPh = (parseFloat(data.target_ph) - inputPh).toFixed(1)
                } catch (error) {
                    console.error(error.response.data.message);
                }
            }

            inputDataPh.addEventListener("keyup", hitungSelisihPh)
            inputDataPh.addEventListener("change", hitungSelisihPh)

            const getStatus = async e => {
                try {
                    const formData = new FormData();

                    let unsur = e.target.dataset.intepretasiUnsur

                    formData.append('unsur', unsur)
                    formData.append('ppm', e.target.value)

                    let response = await axios.post(urlIntepretasiStatus, formData, config);

                    let data = response.data.data

                    document.querySelector('.input[data-set-unsur="' + unsur + '"]').innerHTML = e.target.value
                    document.querySelector('.status[data-set-unsur="' + unsur + '"]').innerHTML = data

                    if (unsur == "P") {
                        pStatus = data
                    }

                    if (unsur == "K") {
                        kStatus = data
                    }

                    if (unsur == "Mg") {
                        mgStatus = data
                    }

                    if (unsur == "Ca") {
                        caStatus = data
                    }

                    if (unsur == "Corg") {
                        if (optionTanaman.value) {
                            let response2 = await axios.get(urlOneTanaman.replace('ID', optionTanaman.value), config);
                            let data2 = response2.data

                            eTargetCorg.innerHTML = parseFloat(data2.target_persen_corganik)
                            selisihCorg.innerHTML = (parseFloat(data2.target_persen_corganik) - e.target.value).toFixed(1)
                        }

                        corgStatus = data
                    }

                    if (unsur === "P" || unsur === "K") {
                        let oneTanaman = await axios.get(urlOneTanaman.replace('ID', optionTanaman.value), config);
                        let changeDosis = (unsur === "P") ? rekomendasiP2o5 : rekomendasiK2o
                        let elementInput = (unsur === "P") ? dataInputPreplantP2o5 : dataInputPreplantK2o

                        elementInput.removeAttribute('disabled')

                        let dataTanaman = oneTanaman.data
                        if (data == "Sangat Rendah") {
                            changeDosis.innerHTML = (unsur === "P") ? dataTanaman.sangat_rendah_p2o5 : dataTanaman.sangat_rendah_k2o
                            if (unsur == "P") {
                                dosisP2o5 = dataTanaman.sangat_rendah_p2o5
                            } else {
                                dosisK2o = dataTanaman.sangat_rendah_k2o
                            }
                        }
                        if (data == "Rendah") {
                            changeDosis.innerHTML = (unsur === "P") ? dataTanaman.rendah_p2o5 : dataTanaman.rendah_k2o
                            if (unsur == "P") {
                                dosisP2o5 = dataTanaman.rendah_p2o5
                            } else {
                                dosisK2o = dataTanaman.rendah_k2o
                            }
                        }
                        if (data == "Sedang") {
                            changeDosis.innerHTML = (unsur === "P") ? dataTanaman.sedang_p2o5 : dataTanaman.sedang_k2o
                            if (unsur == "P") {
                                dosisP2o5 = dataTanaman.sedang_p2o5
                            } else {
                                dosisK2o = dataTanaman.sedang_k2o
                            }
                        }
                        if (data == "Tinggi") {
                            changeDosis.innerHTML = (unsur === "P") ? dataTanaman.tinggi_p2o5 : dataTanaman.tinggi_k2o
                            if (unsur == "P") {
                                dosisP2o5 = dataTanaman.tinggi_p2o5
                            } else {
                                dosisK2o = dataTanaman.tinggi_k2o
                            }
                        }
                        if (data == "Sangat Tinggi") {
                            changeDosis.innerHTML = (unsur === "P") ? dataTanaman.sangat_tinggi_p2o5 : dataTanaman.sangat_tinggi_k2o
                            if (unsur == "P") {
                                dosisP2o5 = dataTanaman.sangat_tinggi_p2o5
                            } else {
                                dosisK2o = dataTanaman.sangat_tinggi_k2o
                            }
                        }
                    }

                } catch (error) {
                    console.error(error.message);
                }
            }

            inputDataP.addEventListener("keyup", getStatus)
            inputDataK.addEventListener("keyup", getStatus)
            inputDataMg.addEventListener("keyup", getStatus)
            inputDataCa.addEventListener("keyup", getStatus)
            inputDataCorg.addEventListener("keyup", getStatus)
            inputDataP.addEventListener("change", getStatus)
            inputDataK.addEventListener("change", getStatus)
            inputDataMg.addEventListener("change", getStatus)
            inputDataCa.addEventListener("change", getStatus)
            inputDataCorg.addEventListener("change", getStatus)

            const funcEventPreplant = e => {
                let elementDrip = dripN
                let elementFrekuensi = frekuensiN
                let value = e.target.value

                if (value < 0) {
                    value = 0
                }

                if (value > 100) {
                    value = 100
                }

                let hasil = 100 - value

                if (e.target.id == "data-input-preplant-n") {
                    elementDrip = dripN
                    elementFrekuensi = frekuensiN
                    nilaiDripN = hasil
                }
                if (e.target.id == "data-input-preplant-p2o5") {
                    elementDrip = dripP2o5
                    elementFrekuensi = frekuensiP2o5
                    nilaiDripP2o5 = hasil
                }
                if (e.target.id == "data-input-preplant-k2o") {
                    elementDrip = dripK2o
                    elementFrekuensi = frekuensiK2o
                    nilaiDripK2o = hasil
                }
                if (e.target.id == "data-input-preplant-frit") {
                    elementDrip = dripFrit
                    elementFrekuensi = frekuensiFrit
                    nilaiDripFrit = hasil
                }

                elementDrip.innerHTML = hasil

                elementFrekuensi.innerHTML = (hasil > 0) ? drip : 0
            }

            dataInputPreplantN.addEventListener("keyup", funcEventPreplant)
            dataInputPreplantP2o5.addEventListener("keyup", funcEventPreplant)
            dataInputPreplantK2o.addEventListener("keyup", funcEventPreplant)
            dataInputPreplantFrit.addEventListener("keyup", funcEventPreplant)
            dataInputPreplantN.addEventListener("change", funcEventPreplant)
            dataInputPreplantP2o5.addEventListener("change", funcEventPreplant)
            dataInputPreplantK2o.addEventListener("change", funcEventPreplant)
            dataInputPreplantFrit.addEventListener("change", funcEventPreplant)

            const pushSessionInformasiUmum = () => {
                // const formData = new FormData();

                // informasi umum
                document.querySelector('input[name="nama_petani"]').value = dataInputNamaPetani.value
                document.querySelector('input[name="luas_lahan"]').value = dataInputLuasLahan.value
                document.querySelector('input[name="latitude"]').value = dataInputLatitude.value
                document.querySelector('input[name="longitude"]').value = dataInputLongitude.value
                document.querySelector('input[name="altitude"]').value = dataInputAltitude.value
                document.querySelector('input[name="alamat"]').value = dataInputAlamat.value
                document.querySelector('input[name="tanaman_id"]').value = optionTanaman.value
                document.querySelector('input[name="input_p_tersedia"]').value = inputDataP.value
                document.querySelector('input[name="p_status"]').value = pStatus
                document.querySelector('input[name="input_k_tersedia"]').value = inputDataK.value
                document.querySelector('input[name="k_status"]').value = kStatus
                document.querySelector('input[name="input_mg"]').value = inputDataMg.value
                document.querySelector('input[name="mg_status"]').value = mgStatus
                document.querySelector('input[name="input_ca"]').value = inputDataCa.value
                document.querySelector('input[name="ca_status"]').value = caStatus
                document.querySelector('input[name="input_corganik"]').value = inputDataCorg.value
                document.querySelector('input[name="corganik_status"]').value = corgStatus
                document.querySelector('input[name="input_ph"]').value = inputDataPh.value

                // formalisasi aplikasi pupuk
                document.querySelector('input[name="dosis_n"]').value = dosisN
                document.querySelector('input[name="dosis_p2o5"]').value = dosisP2o5
                document.querySelector('input[name="dosis_k2o"]').value = dosisK2o
                document.querySelector('input[name="dosis_frit"]').value = document.getElementById('data-input-rekomendasi-frit').value
                document.querySelector('input[name="preplant_n"]').value = dataInputPreplantN.value
                document.querySelector('input[name="preplant_p2o5"]').value = dataInputPreplantP2o5.value
                document.querySelector('input[name="preplant_k2o"]').value = dataInputPreplantK2o.value
                document.querySelector('input[name="preplant_frit"]').value = document.getElementById('data-input-preplant-frit').value
                document.querySelector('input[name="selisih_ph"]').value = nilaiSelisihPh
                document.querySelector('input[name="selisih_corg"]').value = targetCorg - inputDataCorg.value
                document.querySelector('input[name="pupuk_organik_corg"]').value = dataInputPupukOrganikCorg.value
                document.querySelector('input[name="drip_n"]').value = nilaiDripN
                document.querySelector('input[name="drip_p2o5"]').value = nilaiDripP2o5
                document.querySelector('input[name="drip_k2o"]').value = nilaiDripK2o
                document.querySelector('input[name="drip_frit"]').value = nilaiDripFrit
                document.querySelector('input[name="frekuensi_drip"]').value = drip

                //analisis tanan
                document.querySelector('input[name="jenis_tanah"]').value = dataInputJenisTanah.value

                // pilihan pupuk
                document.querySelector('input[name="pupuk_amsul"]').value = document.getElementById('pilihan-pupuk-amsul').value
                document.querySelector('input[name="pupuk_sp36"]').value = document.getElementById('pilihan-pupuk-sp36').value
                document.querySelector('input[name="pupuk_kci"]').value = document.getElementById('pilihan-pupuk-kci').value
                document.querySelector('input[name="pupuk_potsul"]').value = document.getElementById('pilihan-pupuk-potsul').value
                document.querySelector('input[name="pupuk_frits"]').value = document.getElementById('pilihan-pupuk-frits').value
                document.querySelector('input[name="pupuk_urea"]').value = document.getElementById('pilihan-pupuk-urea').value

                // let response = await axios.post(urlOutput, formData, config);


                return true
            }

            document.getElementById('btn-output').addEventListener("click", async e => {
                await pushSessionInformasiUmum()

                document.getElementById('form-output').submit()
            })
        });
    </script>
    @endpush
</x-app-layout>
