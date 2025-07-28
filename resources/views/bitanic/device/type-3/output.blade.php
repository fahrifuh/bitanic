<x-app-layout>
    @push('styles')
    <style>
        .event-none {
            pointer-events: none;
        }
    </style>
    @endpush
    {{-- Header --}}
    <x-slot name="header">
        {{--  --}}
    </x-slot>
    {{-- End Header --}}
    <div class="row">

        <div class="col-lg-12 mb-4">
            <div class="card bg-danger p-2 text-center fw-bold text-white">APLIKASI PEMUPUKAN</div>
        </div>

        {{-- Informasi Umum --}}
        <div class="col-lg-12 mb-3">
            {{-- Card --}}
            <div class="card">
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered">
                        <thead>
                            <tr class="bg-mechli-1">
                                <td class="text-uppercase text-center">Nama Petani</td>
                                <td class="text-uppercase text-center">Luas Lahan [&#13217;]</td>
                                <td class="text-uppercase text-center">Jenis Tanaman</td>
                                <td class="text-uppercase text-center">Altitude</td>
                                <td class="text-uppercase text-center">Tanah</td>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0 bg-warning">
                            <tr class="bg-warning">
                                <td class="fw-bold text-dark text-center">{{ session('nama_petani') }}</td>
                                <td class="fw-bold text-dark text-center">{{ session('luas_lahan') }}</td>
                                <td class="fw-bold text-dark text-center">{{ session('jenis_tanaman') }}</td>
                                <td class="fw-bold text-dark text-center">{{ session('altitude') }}</td>
                                <td class="fw-bold text-dark text-center">{{ isset($request['jenis_tanah']) ? $request['jenis_tanah'] : '' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- End Card --}}
        </div>
        {{-- End Informasi Umum --}}

        @php
            // dd(session()->all());
        @endphp

        {{-- 2 --}}
        <div class="col-lg-12 mb-3">
            {{-- Card --}}
            <div class="card">
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered">
                        <thead>
                            <tr class="bg-mechli-1">
                                <td rowspan="2" class=" align-middle text-center">Fertilizer</td>
                                <td rowspan="2" colspan="3" class=" align-middle text-center">Per Hektar
                                    (Kg)</td>
                                <td colspan="2" class=" align-middle text-center">Kg/Lahan</td>
                                {{-- <td rowspan="2" class=" align-middle text-center">Per Aplikasi (g)</td> --}}
                                {{-- <td rowspan="2" colspan="2" class=" align-middle text-center">Salt INDEX</td> --}}
                            </tr>
                            <tr class="bg-mechli-1">
                                <td class=" align-middle text-center">{{ session('luas_lahan') }}</td>
                                <td class=" align-middle text-center">m2</td>
                            </tr>
                            <tr class="bg-primary">
                                <td class="text-warning align-middle text-center"></td>
                                <td class="text-warning align-middle text-center">TOTAL</td>
                                <td class="text-warning align-middle text-center">PRE</td>
                                <td class="text-warning align-middle text-center">DRIP</td>
                                <td class="text-warning align-middle text-center">PRE</td>
                                <td class="text-warning align-middle text-center">DRIP</td>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0 bg-warning">
                            <tr class="bg-warning">
                                <td class="fw-bold text-dark">---------------------------------</td>
                                <td class="fw-bold text-dark">0</td>
                                <td class="fw-bold text-dark">0</td>
                                <td class="fw-bold text-dark">0</td>
                                <td class="fw-bold text-dark">0.0</td>
                                <td class="fw-bold text-dark">0.0</td>
                            </tr>
                            @if ($request['pupuk_amsul'] === 'ok')
                                <tr class="bg-warning">
                                    <td class="fw-bold text-dark">AMMONIUM SULFATE</td>
                                    <td class="fw-bold text-dark">{{ round($amsul['per_hektar_total']) }}</td>
                                    <td class="fw-bold text-dark">{{ round($amsul['per_hektar_pre']) }}</td>
                                    <td class="fw-bold text-dark">{{ round($amsul['per_hektar_drip']) }}</td>
                                    <td class="fw-bold text-dark">
                                        {{ round($amsul['lahan_pre'], 1, PHP_ROUND_HALF_DOWN) }}</td>
                                    <td class="fw-bold text-dark">{{ round($amsul['lahan_drip'], 1) }}</td>
                                </tr>
                            @else
                                <tr class="bg-warning">
                                    <td class="fw-bold text-dark">---------------------------------</td>
                                    <td class="fw-bold text-dark">0</td>
                                    <td class="fw-bold text-dark">0</td>
                                    <td class="fw-bold text-dark">0</td>
                                    <td class="fw-bold text-dark">0.0</td>
                                    <td class="fw-bold text-dark">0.0</td>
                                </tr>
                            @endif
                            @if ($request['pupuk_sp36'] === 'ok')
                                <tr class="bg-warning">
                                    <td class="fw-bold text-dark">SUPER FOSFAT - SP36</td>
                                    <td class="fw-bold text-dark">{{ round($sp36['per_hektar_total']) }}</td>
                                    <td class="fw-bold text-dark">{{ round($sp36['per_hektar_pre']) }}</td>
                                    <td class="fw-bold text-dark">{{ round($sp36['per_hektar_drip']) }}</td>
                                    <td class="fw-bold text-dark">
                                        {{ round($sp36['lahan_pre'], 1, PHP_ROUND_HALF_DOWN) }}</td>
                                    <td class="fw-bold text-dark">{{ round($sp36['lahan_drip'], 1) }}</td>
                                </tr>
                            @else
                                <tr class="bg-warning">
                                    <td class="fw-bold text-dark">---------------------------------</td>
                                    <td class="fw-bold text-dark">0</td>
                                    <td class="fw-bold text-dark">0</td>
                                    <td class="fw-bold text-dark">0</td>
                                    <td class="fw-bold text-dark">0.0</td>
                                    <td class="fw-bold text-dark">0.0</td>
                                </tr>
                            @endif
                            @if ($request['pupuk_kci'] === 'ok')
                                <tr class="bg-warning">
                                    <td class="fw-bold text-dark">POTASIUM CHLORIDE - KCI</td>
                                    <td class="fw-bold text-dark">{{ round($kci['per_hektar_total']) }}</td>
                                    <td class="fw-bold text-dark">{{ round($kci['per_hektar_pre']) }}</td>
                                    <td class="fw-bold text-dark">{{ round($kci['per_hektar_drip']) }}</td>
                                    <td class="fw-bold text-dark">
                                        {{ round($kci['lahan_pre'], 1, PHP_ROUND_HALF_DOWN) }}</td>
                                    <td class="fw-bold text-dark">{{ round($kci['lahan_drip'], 1) }}</td>
                                </tr>
                            @else
                                <tr class="bg-warning">
                                    <td class="fw-bold text-dark">---------------------------------</td>
                                    <td class="fw-bold text-dark">0</td>
                                    <td class="fw-bold text-dark">0</td>
                                    <td class="fw-bold text-dark">0</td>
                                    <td class="fw-bold text-dark">0.0</td>
                                    <td class="fw-bold text-dark">0.0</td>
                                </tr>
                            @endif
                            @if ($request['pupuk_potsul'] === 'ok')
                                <tr class="bg-warning">
                                    <td class="fw-bold text-dark">AMMONIUM SULFATE</td>
                                    <td class="fw-bold text-dark">{{ round($potsul['per_hektar_total']) }}</td>
                                    <td class="fw-bold text-dark">{{ round($potsul['per_hektar_pre']) }}</td>
                                    <td class="fw-bold text-dark">{{ round($potsul['per_hektar_drip']) }}</td>
                                    <td class="fw-bold text-dark">
                                        {{ round($potsul['lahan_pre'], 1, PHP_ROUND_HALF_DOWN) }}</td>
                                    <td class="fw-bold text-dark">{{ round($potsul['lahan_drip'], 1) }}</td>
                                </tr>
                            @else
                                <tr class="bg-warning">
                                    <td class="fw-bold text-dark">---------------------------------</td>
                                    <td class="fw-bold text-dark">0</td>
                                    <td class="fw-bold text-dark">0</td>
                                    <td class="fw-bold text-dark">0</td>
                                    <td class="fw-bold text-dark">0.0</td>
                                    <td class="fw-bold text-dark">0.0</td>
                                </tr>
                            @endif
                            <tr class="bg-warning">
                                <td class="fw-bold text-dark">---------------------------------</td>
                                <td class="fw-bold text-dark">0</td>
                                <td class="fw-bold text-dark">0</td>
                                <td class="fw-bold text-dark">0</td>
                                <td class="fw-bold text-dark">0.0</td>
                                <td class="fw-bold text-dark">0.0</td>
                            </tr>
                            @if ($request['pupuk_frits'] === 'ok')
                                <tr class="bg-warning">
                                    <td class="fw-bold text-dark">FRITS</td>
                                    <td class="fw-bold text-dark">{{ $frit['per_hektar_total'] }}</td>
                                    <td class="fw-bold text-dark">{{ $frit['per_hektar_pre'] }}</td>
                                    <td class="fw-bold text-dark">{{ $frit['per_hektar_drip'] }}</td>
                                    <td class="fw-bold text-dark">{{ $frit['lahan_pre'] }}</td>
                                    <td class="fw-bold text-dark">{{ $frit['lahan_drip'] }}</td>
                                </tr>
                            @else
                                <tr class="bg-warning">
                                    <td class="fw-bold text-dark">---------------------------------</td>
                                    <td class="fw-bold text-dark">0</td>
                                    <td class="fw-bold text-dark">0</td>
                                    <td class="fw-bold text-dark">0</td>
                                    <td class="fw-bold text-dark">0.0</td>
                                    <td class="fw-bold text-dark">0.0</td>
                                    {{-- <td class="fw-bold text-dark">0</td> --}}
                                    {{-- <td class="fw-bold text-dark">0.00</td> --}}
                                    {{-- <td class="fw-bold text-dark">0.0</td> --}}
                                </tr>
                            @endif
                            @if ($request['pupuk_urea'] === 'ok')
                                <tr class="bg-warning">
                                    <td class="fw-bold text-dark">UREA</td>
                                    <td class="fw-bold text-dark">{{ round($urea['per_hektar_total']) }}</td>
                                    <td class="fw-bold text-dark">{{ round($urea['per_hektar_pre']) }}</td>
                                    <td class="fw-bold text-dark">{{ round($urea['per_hektar_drip']) }}</td>
                                    <td class="fw-bold text-dark">
                                        {{ round($urea['lahan_pre'], 1, PHP_ROUND_HALF_DOWN) }}</td>
                                    <td class="fw-bold text-dark">{{ round($urea['lahan_drip'], 1) }}</td>
                                    {{-- <td class="fw-bold text-dark">0</td> --}}
                                    {{-- <td class="fw-bold text-dark">{{ $urea['salt_index_unit'] }}</td> --}}
                                    {{-- <td class="fw-bold text-dark">{{ $urea['salt_index_formalition'] }}</td> --}}
                                </tr>
                            @else
                                <tr class="bg-warning">
                                    <td class="fw-bold text-dark">---------------------------------</td>
                                    <td class="fw-bold text-dark">0</td>
                                    <td class="fw-bold text-dark">0</td>
                                    <td class="fw-bold text-dark">0</td>
                                    <td class="fw-bold text-dark">0.0</td>
                                    <td class="fw-bold text-dark">0.0</td>
                                    {{-- <td class="fw-bold text-dark">0</td> --}}
                                    {{-- <td class="fw-bold text-dark">0.00</td> --}}
                                    {{-- <td class="fw-bold text-dark">0.0</td> --}}
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- End Card --}}
        </div>
        {{-- End 2 --}}


        {{-- 3 --}}
        <div class="col-lg-6 mb-4">
            {{-- Card --}}
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr class="bg-mechli-1">
                                <td class=" align-middle text-center" style="width: 50%;">PENAMBAHAN KAPUR
                                </td>
                                <td class=" align-middle text-center" style="width: 25%;">TON/ha</td>
                                <td class=" align-middle text-center" style="width: 25%;">Ton/Lahan</td>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0 bg-warning">
                            <tr class="bg-warning">
                                <td class="fw-bold text-dark">DOLOMIT</td>
                                <td class="fw-bold text-dark">{{ number_format_1($dolomit['ton_ha']) }}</td>
                                <td class="fw-bold text-dark">{{ number_format_1($dolomit['ton_lahan']) }}</td>
                            </tr>
                            <tr class="bg-warning">
                                <td class="fw-bold text-dark">BAHAN ORGANIK</td>
                                <td class="fw-bold text-dark">{{ number_format_1($bahan_organik['ton_ha']) }}</td>
                                <td class="fw-bold text-dark">{{ number_format_1($bahan_organik['ton_lahan']) }}</td>
                            </tr>
                            <tr class="bg-warning">
                                <td class="fw-bold text-dark">PUPUK ORGANIK (KOMPOS)</td>
                                <td class="fw-bold text-dark">{{ number_format_1($pupuk_organik['ton_ha']) }}</td>
                                <td class="fw-bold text-dark">{{ number_format_1($pupuk_organik['ton_lahan']) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- End Card --}}
        </div>
        {{-- End 3 --}}

        <div class="col-lg-6 mb-3">
            {{--  --}}
        </div>

        {{-- 3 --}}
        <div class="col-lg-9 mb-3">

            <div class="card bg-danger mb-4 p-2 text-center fw-bold text-white">APLIKASI PEMUPUKAN</div>
            {{-- Card --}}
            <div class="card">
                <div class="table-responsive mb-3">
                    <table class="table table-bordered">
                        <thead>
                            <tr class="bg-warning">
                                <td class=" align-middle text-center">No.</td>
                                <td colspan="4" class=" align-middle text-center">REKOMENSASI PUPUK</td>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0 bg-warning">
                            <tr class="bg-white">
                                <td class="fw-bold text-dark" style="width: 10%;">1</td>
                                <td class="fw-bold text-dark" style="width: 40%;">NAMA PETANI</td>
                                <td colspan="3" class="fw-bold text-dark">{{ session('nama_petani') }}</td>
                            </tr>
                            <tr class="bg-white">
                                <td class="fw-bold text-dark" style="width: 10%;">2</td>
                                <td class="fw-bold text-dark" style="width: 40%;">NO SAMPLE TANAH</td>
                                <td colspan="3" class="fw-bold text-dark"></td>
                            </tr>
                            <tr class="bg-white">
                                <td class="fw-bold text-dark">3</td>
                                <td class="fw-bold text-dark">LUAS TANAH (m2)</td>
                                <td colspan="3" class="fw-bold text-dark">{{ session('luas_lahan') }}</td>
                            </tr>
                            <tr class="bg-white">
                                <td class="fw-bold text-dark">4</td>
                                <td class="fw-bold text-dark">JENIS TANAMAN</td>
                                <td colspan="3" class="fw-bold text-dark">{{ session('jenis_tanaman') }}</td>
                            </tr>
                            <tr class="bg-white">
                                <td class="fw-bold text-dark">5</td>
                                <td class="fw-bold text-dark">JENIS TANAH</td>
                                <td colspan="3" class="fw-bold text-dark">{{ isset($request['jenis_tanah']) ? $request['jenis_tanah'] : '' }}</td>
                            </tr>
                            <tr class="bg-white">
                                <td class="fw-bold text-dark">6</td>
                                <td class="fw-bold text-dark">ALTITUDE m dpl</td>
                                <td colspan="3" class="fw-bold text-dark">{{ session('altitude') }}</td>
                            </tr>
                            <tr class="bg-white">
                                <td class="fw-bold text-dark">7</td>
                                <td class="fw-bold text-dark">P-Tersedia</td>
                                <td class="fw-bold text-dark">{{ $request['input_p_tersedia'] }}</td>
                                <td class="fw-bold text-dark">ppm</td>
                                <td class="fw-bold text-dark">{{ $request['p_status'] }}</td>
                            </tr>
                            <tr class="bg-white">
                                <td class="fw-bold text-dark">8</td>
                                <td class="fw-bold text-dark">K-Tersedia</td>
                                <td class="fw-bold text-dark">{{ $request['input_k_tersedia'] }}</td>
                                <td class="fw-bold text-dark">ppm</td>
                                <td class="fw-bold text-dark">{{ $request['k_status'] }}</td>
                            </tr>
                            <tr class="bg-white">
                                <td class="fw-bold text-dark">9</td>
                                <td class="fw-bold text-dark">C-Organik</td>
                                <td class="fw-bold text-dark">{{ $request['input_corganik'] }}</td>
                                <td class="fw-bold text-dark">%</td>
                                <td class="fw-bold text-dark"></td>
                            </tr>
                            <tr class="bg-white">
                                <td class="fw-bold text-dark">10</td>
                                <td class="fw-bold text-dark">pH</td>
                                <td class="fw-bold text-dark">{{ $request['input_ph'] }}</td>
                                <td class="fw-bold text-dark"></td>
                                <td class="fw-bold text-dark"></td>
                            </tr>
                            <tr class="bg-warning">
                                <td class=" align-middle text-center">No.</td>
                                <td class=" align-middle text-center">JENIS AMELIORAN</td>
                                <td class=" align-middle text-center">Per Ha (kg)</td>
                                <td class=" align-middle text-center">Per Luas Lahan (kg)</td>
                                <td class=" align-middle text-center">Per {{ $ukuran_petak['penjang_bendengan'] }}m linear bed (kg)</td>
                            </tr>
                            <tr class="bg-white">
                                <td class="fw-bold text-dark">11</td>
                                <td class="fw-bold text-dark">PUPUK UREA</td>
                                <td class="fw-bold text-dark text-end">{{ number_format_1(round($urea['per_hektar_total'])) }}</td>
                                <td class="fw-bold text-dark text-end">
                                    {{ number_format_1(round((session('luas_lahan') / 10000) * $urea['per_hektar_total'], 1)) }}
                                </td>
                                <td class="fw-bold text-dark text-end">
                                    {{ number_format_1(round(($ukuran_petak['luas_petak'] / 10000) * $urea['per_hektar_total'], 3, PHP_ROUND_HALF_UP)) }}
                                </td>
                            </tr>
                            <tr class="bg-white">
                                <td class="fw-bold text-dark">12</td>
                                <td class="fw-bold text-dark">PUPUK ZA</td>
                                <td class="fw-bold text-dark text-end">{{ number_format_1(round($amsul['per_hektar_total'])) }}</td>
                                <td class="fw-bold text-dark text-end">
                                    {{ number_format_1(round((session('luas_lahan') / 10000) * $amsul['per_hektar_total'], 1, PHP_ROUND_HALF_UP)) }}
                                </td>
                                <td class="fw-bold text-dark text-end">
                                    {{ number_format_1(round(($ukuran_petak['luas_petak'] / 10000) * $amsul['per_hektar_total'], 3, PHP_ROUND_HALF_UP)) }}
                                </td>
                            </tr>
                            <tr class="bg-white">
                                <td class="fw-bold text-dark">13</td>
                                <td class="fw-bold text-dark">PUPUK SP36</td>
                                <td class="fw-bold text-dark text-end">{{ number_format_1(round($sp36['per_hektar_total'])) }}</td>
                                <td class="fw-bold text-dark text-end">
                                    {{ number_format_1(round((session('luas_lahan') / 10000) * $sp36['per_hektar_total'], 1, PHP_ROUND_HALF_UP)) }}
                                </td>
                                <td class="fw-bold text-dark text-end">
                                    {{ number_format_1(round(($ukuran_petak['luas_petak'] / 10000) * $sp36['per_hektar_total'], 3, PHP_ROUND_HALF_UP)) }}
                                </td>
                            </tr>
                            <tr class="bg-white">
                                <td class="fw-bold text-dark">14</td>
                                <td class="fw-bold text-dark">PUPUK KCI</td>
                                <td class="fw-bold text-dark text-end">{{ number_format_1(round($kci['per_hektar_total'])) }}</td>
                                <td class="fw-bold text-dark text-end">
                                    {{ number_format_1(round((session('luas_lahan') / 10000) * $kci['per_hektar_total'], 1, PHP_ROUND_HALF_UP)) }}
                                </td>
                                <td class="fw-bold text-dark text-end">
                                    {{ number_format_1(round(($ukuran_petak['luas_petak'] / 10000) * $kci['per_hektar_total'], 3, PHP_ROUND_HALF_UP)) }}
                                </td>
                            </tr>
                            <tr class="bg-white">
                                <td class="fw-bold text-dark">15</td>
                                <td class="fw-bold text-dark">PUPUK ZK</td>
                                <td class="fw-bold text-dark text-end">{{ number_format_1(round($potsul['per_hektar_total'])) }}</td>
                                <td class="fw-bold text-dark text-end">
                                    {{ number_format_1(round((session('luas_lahan') / 10000) * $potsul['per_hektar_total'], 1, PHP_ROUND_HALF_UP)) }}
                                </td>
                                <td class="fw-bold text-dark text-end">
                                    {{ number_format_1(round(($ukuran_petak['luas_petak'] / 10000) * $potsul['per_hektar_total'], 3, PHP_ROUND_HALF_UP)) }}
                                </td>
                            </tr>
                            <tr class="bg-white">
                                <td class="fw-bold text-dark">16</td>
                                <td class="fw-bold text-dark">Dolomit</td>
                                <td class="fw-bold text-dark text-end">{{ number_format_1($dolomit['ton_ha'] * 1000) }}</td>
                                <td class="fw-bold text-dark text-end">
                                    {{ number_format_1(round((session('luas_lahan') / 10000) * ($dolomit['ton_ha'] * 1000), 1, PHP_ROUND_HALF_UP)) }}
                                </td>
                                <td class="fw-bold text-dark text-end">
                                    {{ number_format_1(round(($ukuran_petak['luas_petak'] / 10000) * ($dolomit['ton_ha'] * 1000), 3, PHP_ROUND_HALF_UP)) }}
                                </td>
                            </tr>
                            <tr class="bg-white">
                                <td class="fw-bold text-dark">17</td>
                                <td class="fw-bold text-dark">Bahan Organik</td>
                                <td class="fw-bold text-dark text-end">
                                    {{ number_format_1($bahan_organik['ton_ha'] * 1000) }}</td>
                                <td class="fw-bold text-dark text-end">
                                    {{ number_format_1(round((session('luas_lahan') / 10000) * ($bahan_organik['ton_ha'] * 1000), 1, PHP_ROUND_HALF_UP)) }}
                                </td>
                                <td class="fw-bold text-dark text-end">
                                    {{ number_format_1(round(($ukuran_petak['luas_petak'] / 10000) * ($bahan_organik['ton_ha'] * 1000), 3, PHP_ROUND_HALF_UP)) }}
                                </td>
                            </tr>
                            <tr class="bg-white">
                                <td class="fw-bold text-dark">18</td>
                                <td class="fw-bold text-dark">Bioextrim (liter)</td>
                                <td class="fw-bold text-dark text-end">{{ number_format_1(300) }}</td>
                                <td class="fw-bold text-dark text-end">
                                    {{ number_format_1(round((session('luas_lahan') / 10000) * 300, 1, PHP_ROUND_HALF_UP)) }}
                                </td>
                                <td class="fw-bold text-dark text-end">
                                    {{ number_format_1(round(($ukuran_petak['luas_petak'] / 10000) * 300, 3, PHP_ROUND_HALF_UP)) }}
                                </td>
                            </tr>
                            <tr class="bg-white">
                                <td class="fw-bold text-dark">19</td>
                                <td class="fw-bold text-dark">Trichowish (g)</td>
                                <td class="fw-bold text-dark text-end">{{ number_format_1(40000) }}</td>
                                <td class="fw-bold text-dark text-end">
                                    {{ number_format_1(round((session('luas_lahan') / 10000) * 40000, 1, PHP_ROUND_HALF_UP)) }}
                                </td>
                                <td class="fw-bold text-dark text-end">
                                    {{ number_format_1(round(($ukuran_petak['luas_petak'] / 10000) * 40000, 3, PHP_ROUND_HALF_UP)) }}
                                </td>
                            </tr>
                            <tr class="bg-white">
                                <td class="fw-bold text-dark">20</td>
                                <td class="fw-bold text-dark">Rhizomax (g)</td>
                                <td class="fw-bold text-dark text-end">{{ number_format_1(4000) }}</td>
                                <td class="fw-bold text-dark text-end">
                                    {{ number_format_1(round((session('luas_lahan') / 10000) * 4000, 1, PHP_ROUND_HALF_UP)) }}
                                </td>
                                <td class="fw-bold text-dark text-end">
                                    {{ number_format_1(round(($ukuran_petak['luas_petak'] / 10000) * 4000, 3, PHP_ROUND_HALF_UP)) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="table-responsive mb-3">
                    <table class="table table-bordered">
                        <tbody class="table-border-bottom-0 bg-warning">
                            <tr class="bg-white">
                                <td colspan="4" class="fw-bold text-dark">UKURAN PETAK</td>
                            </tr>
                            <tr class="bg-warning">
                                <td class=" align-middle text-center" style="width: 10%;">No.</td>
                                <td colspan="3" class=" align-middle text-center">UKURAN PETAK</td>
                            </tr>
                            <tr class="bg-white">
                                <td class="fw-bold text-dark">1</td>
                                <td class="fw-bold text-dark">Panjang Bedengan</td>
                                <td class="fw-bold text-dark text-center">{{ number_format_1($ukuran_petak['penjang_bendengan']) }}</td>
                                <td class="fw-bold text-dark">m</td>
                            </tr>
                            <tr class="bg-white">
                                <td class="fw-bold text-dark">2</td>
                                <td class="fw-bold text-dark">Lebar Bedengan</td>
                                <td class="fw-bold text-dark text-center">{{ number_format_1($ukuran_petak['lebar_bendengan']) }}</td>
                                <td class="fw-bold text-dark">m</td>
                            </tr>
                            <tr class="bg-white">
                                <td class="fw-bold text-dark">3</td>
                                <td class="fw-bold text-dark">Perlakuan</td>
                                <td class="fw-bold text-dark text-center"></td>
                                <td class="fw-bold text-dark"></td>
                            </tr>
                            <tr class="bg-white">
                                <td class="fw-bold text-dark">4</td>
                                <td class="fw-bold text-dark">Ulangan</td>
                                <td class="fw-bold text-dark text-center">{{ $ukuran_petak['ulangan'] }}</td>
                                <td class="fw-bold text-dark"></td>
                            </tr>
                            <tr class="bg-white">
                                <td class="fw-bold text-dark">5</td>
                                <td class="fw-bold text-dark">Luas Petak</td>
                                <td class="fw-bold text-dark text-center">{{ number_format_1($ukuran_petak['luas_petak']) }}</td>
                                <td class="fw-bold text-dark">m2</td>
                            </tr>
                            <tr class="bg-white">
                                <td class="fw-bold text-dark">6</td>
                                <td class="fw-bold text-dark">Luas total ulangan</td>
                                <td class="fw-bold text-dark text-center">{{ number_format_1($ukuran_petak['luas_total_ulangan']) }}</td>
                                <td class="fw-bold text-dark">m2</td>
                            </tr>
                            <tr class="bg-white">
                                <td class="fw-bold text-dark">7</td>
                                <td class="fw-bold text-dark">Luas alahan percobaan</td>
                                <td class="fw-bold text-dark text-center">{{ number_format_1(session('luas_lahan')) }}</td>
                                <td class="fw-bold text-dark">m2</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="table-responsive mb-3">
                    <table class="table table-bordered">
                        <thead>
                            <tr class="bg-warning">
                                <td class=" align-middle text-center">No.</td>
                                <td class=" align-middle">APLIKASI</td>
                                <td class=" align-middle text-center">Urea</td>
                                <td class=" align-middle text-center">ZA</td>
                                <td class=" align-middle text-center">SP36</td>
                                <td class=" align-middle text-center">KCI</td>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0 bg-warning">
                            <tr style="background-color: #66ff99 !important;">
                                <td class="fw-bold text-dark">A.</td>
                                <td class="fw-bold text-dark">APLIKASI PREPLANT</td>
                                <td colspan="4" class="fw-bold text-dark align-middle text-center">
                                    {{ session('luas_lahan') }}m2 (kg)</td>
                            </tr>
                            <tr class="bg-white">
                                <td class="fw-bold text-dark">1</td>
                                <td class="fw-bold text-dark">PREPLANT (40% N,K)</td>
                                <td class="fw-bold text-dark text-center">{{ number_format_1(round($urea['lahan_pre'], 1)) }}</td>
                                <td class="fw-bold text-dark text-center">{{ number_format_1(round($amsul['lahan_pre'], 1)) }}</td>
                                <td class="fw-bold text-dark text-center">{{ number_format_1(round($sp36['lahan_pre'], 1)) }}</td>
                                <td class="fw-bold text-dark text-center">{{ number_format_1(round($kci['lahan_pre'], 1)) }}</td>
                            </tr>
                            <tr style="background-color: #66ff99 !important;">
                                <td class="fw-bold text-dark">B.</td>
                                <td class="fw-bold text-dark">APLIKASI DRIP N</td>
                                <td class="fw-bold text-dark text-center">{{ number_format_1(round($urea['lahan_drip'])) }}</td>
                                <td class="fw-bold text-dark text-center">{{ number_format_1(round($amsul['lahan_drip'], 1)) }}</td>
                                <td class="fw-bold text-dark text-center">{{ number_format_1(round($sp36['lahan_drip'])) }}</td>
                                <td class="fw-bold text-dark text-center">{{ number_format_1(round($kci['lahan_drip'], 1)) }}</td>
                            </tr>
                            @php
                                $minggu = (int) $request['frekuensi_drip'];
                                $urea_drip = $urea['lahan_drip'] / $minggu;
                                $kci_drip = $kci['lahan_drip'] / $minggu;
                                $sp36_drip = $sp36['lahan_drip'] / $minggu;
                                $amsul_drip = $amsul['lahan_drip'] / $minggu;
                                $total_urea_drip = 0;
                                $total_kci_drip = 0;
                                $total_sp36_drip = 0;
                                $total_amsul_drip = 0;
                            @endphp
                            @for ($i = 1; $i <= 15; $i++)
                                @php
                                    $urea_drip = $i <= $minggu ? $urea_drip : 0;
                                    $kci_drip = $i <= $minggu ? $kci_drip : 0;
                                    $sp36_drip = $i <= $minggu ? $sp36_drip : 0;
                                    $amsul_drip = $i <= $minggu ? $amsul_drip : 0;
                                    $total_urea_drip += $urea_drip;
                                    $total_kci_drip += $kci_drip;
                                    $total_sp36_drip += $sp36_drip;
                                    $total_amsul_drip += $amsul_drip;
                                @endphp
                                <tr class="bg-white">
                                    <td class="fw-bold text-dark">{{ $i }}</td>
                                    <td class="fw-bold text-dark">DRIP MINGGU KE-{{ $i }}</td>
                                    <td class="fw-bold text-dark text-center">
                                        {{ number_format_1(round($urea_drip, 1)) }}</td>
                                    <td class="fw-bold text-dark text-center">
                                        {{ number_format_1(round($amsul_drip, 1)) }}</td>
                                    <td class="fw-bold text-dark text-center">
                                        {{ number_format_1(round($sp36_drip, 1)) }}</td>
                                    <td class="fw-bold text-dark text-center">
                                        {{ number_format_1(round($kci_drip, 1)) }}</td>
                                </tr>
                            @endfor
                            <tr>
                                <td class="fw-bold text-dark bg-white"></td>
                                <td class="fw-bold text-dark bg-white">JUMLAH</td>
                                <td class="fw-bold text-dark text-center bg-warning">{{ number_format_1(round($total_urea_drip)) }}
                                </td>
                                <td class="fw-bold text-dark text-center bg-warning">{{ number_format_1(round($total_amsul_drip, 1)) }}
                                </td>
                                <td class="fw-bold text-dark text-center bg-warning">{{ number_format_1(round($total_sp36_drip)) }}
                                </td>
                                <td class="fw-bold text-dark text-center bg-warning">{{ number_format_1(round($total_kci_drip, 1)) }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6" class="fw-bold text-dark text-center bg-white">APLIKASI MANUAL
                                    BERDASARKAN REKOMENDASI FERADS</td>
                            </tr>
                        </tbody>
                    </table>
                </div>



                <div class="table-responsive mb-3">
                    <table class="table table-bordered">
                        <thead>
                            <tr class="bg-warning">
                                <td class="fw-bold text-dark align-middle text-center">Umur.</td>
                                <td class="fw-bold text-dark align-middle text-center">Urea</td>
                                <td class="fw-bold text-dark align-middle text-center">ZA</td>
                                <td class="fw-bold text-dark align-middle text-center">SP36</td>
                                <td class="fw-bold text-dark align-middle text-center">KCI</td>
                                <td class="fw-bold text-dark align-middle text-center">DOLOMIT</td>
                                <td class="fw-bold text-dark align-middle text-center">KOHE</td>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0 bg-warning">
                            <tr class="bg-white">
                                <td colspan="2" class="fw-bold text-dark">Luas Lahan</td>
                                <td colspan="5" class="fw-bold text-dark">{{ session('luas_lahan') }}m2 (kg)</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-dark bg-white">Preplant</td>
                                <td class="fw-bold text-dark bg-white">{{ number_format_1(round($urea['lahan_pre'], 2)) }}</td>
                                <td class="fw-bold text-dark text-center bg-warning">
                                    {{ number_format_1(round($amsul['lahan_pre'], 2)) }}</td>
                                <td class="fw-bold text-dark text-center bg-warning">
                                    {{ number_format_1(round($sp36['lahan_pre'], 2)) }}</td>
                                <td class="fw-bold text-dark text-center bg-warning">{{ number_format_1(round($kci['lahan_pre'], 2)) }}
                                </td>
                                <td class="fw-bold text-dark text-center bg-warning">
                                    {{ number_format_1(round((session('luas_lahan') / 10000) * ($dolomit['ton_ha'] * 1000), 2, PHP_ROUND_HALF_UP)) }}
                                </td>
                                <td class="fw-bold text-dark text-center bg-warning">
                                    {{ number_format_1(round((session('luas_lahan') / 10000) * ($bahan_organik['ton_ha'] * 1000), 2, PHP_ROUND_HALF_UP)) }}
                                </td>
                            </tr>
                            @php
                                $total_urea_umur = $urea['lahan_pre'];
                                $total_kci_umur = $kci['lahan_pre'];
                                $total_amsul_umur = $amsul['lahan_pre'];
                                $total_sp36_umur = $sp36['lahan_pre'];
                            @endphp
                            @for ($i = 2; $i <= 8; $i += 2)
                                @php
                                    $total_urea_umur += $urea['lahan_drip'] / 4;
                                    $total_kci_umur += $kci['lahan_drip'] / 4;
                                    $total_amsul_umur += $amsul['lahan_drip'] / 4;
                                    $total_sp36_umur += $sp36['lahan_drip'] / 4;
                                @endphp
                                <tr>
                                    <td class="fw-bold text-dark bg-white">{{ $i }} MST</td>
                                    <td class="fw-bold text-dark bg-white">
                                        {{ number_format_1(round($urea['lahan_drip'] / 4, 2)) }}</td>
                                    <td class="fw-bold text-dark text-center bg-warning">
                                        {{ number_format_1(round($amsul['lahan_drip'] / 4, 2)) }}</td>
                                    <td class="fw-bold text-dark text-center bg-warning">
                                        {{ number_format_1(round($sp36['lahan_drip'] / 4, 2)) }}</td>
                                    <td class="fw-bold text-dark text-center bg-warning">
                                        {{ number_format_1(round($kci['lahan_drip'] / 4, 2)) }}</td>
                                    <td class="fw-bold text-dark text-center bg-warning">0</td>
                                    <td class="fw-bold text-dark text-center bg-warning">0</td>
                                </tr>
                            @endfor
                            <tr class="bg-warning">
                                <td class="fw-bold text-dark align-middle text-center">TOTAL</td>
                                <td class="fw-bold text-dark align-middle text-center">
                                    {{ number_format_1(round($total_urea_umur, 2)) }}</td>
                                <td class="fw-bold text-dark align-middle text-center">
                                    {{ number_format_1(round($total_amsul_umur, 2)) }}</td>
                                <td class="fw-bold text-dark align-middle text-center">
                                    {{ number_format_1(round($total_sp36_umur, 2)) }}</td>
                                <td class="fw-bold text-dark align-middle text-center">
                                    {{ number_format_1(round($total_kci_umur, 2)) }}</td>
                                <td class="fw-bold text-dark align-middle text-center">
                                    {{ number_format_1(round((session('luas_lahan') / 10000) * ($dolomit['ton_ha'] * 1000), 2, PHP_ROUND_HALF_UP)) }}
                                </td>
                                <td class="fw-bold text-dark align-middle text-center">
                                    {{ number_format_1(round((session('luas_lahan') / 10000) * ($bahan_organik['ton_ha'] * 1000), 2, PHP_ROUND_HALF_UP)) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- End Card --}}
        </div>
        {{-- End 3 --}}
    </div>
    <form action="{{ route('bitanic.v3-store-new-schedule') }}" method="POST">
        @csrf
        <input type="hidden" name="perangkat_id" value="{{ $perangkat->id }}">
        <input type="hidden" name="nama_petani" value="{{ $request['nama_petani'] }}">
        <input type="hidden" name="luas_lahan" value="{{ $request['luas_lahan'] }}">
        <input type="hidden" name="latitude" value="{{ $request['latitude'] }}">
        <input type="hidden" name="longitude" value="{{ $request['longitude'] }}">
        <input type="hidden" name="altitude" value="{{ $request['altitude'] }}">
        <input type="hidden" name="alamat" value="{{ $request['alamat'] }}">
        <input type="hidden" name="jenis_tanah" value="{{ isset($request['jenis_tanah']) ? $request['jenis_tanah'] : '' }}">
        <input type="hidden" name="dosis_n" value="{{ $request['dosis_n'] }}">
        <input type="hidden" name="dosis_p2o5" value="{{ $request['dosis_p2o5'] }}">
        <input type="hidden" name="dosis_k2o" value="{{ $request['dosis_k2o'] }}">
        <input type="hidden" name="dosis_frit" value="{{ $request['dosis_frit'] }}">
        <input type="hidden" name="preplant_n" value="{{ $request['preplant_n'] }}">
        <input type="hidden" name="preplant_p2o5" value="{{ $request['preplant_p2o5'] }}">
        <input type="hidden" name="preplant_k2o" value="{{ $request['preplant_k2o'] }}">
        <input type="hidden" name="preplant_frit" value="{{ $request['preplant_frit'] }}">
        <input type="hidden" name="selisih_ph" value="{{ $request['selisih_ph'] }}">
        <input type="hidden" name="selisih_corg" value="{{ $request['selisih_corg'] }}">
        <input type="hidden" name="pupuk_organik_corg" value="{{ $request['pupuk_organik_corg'] }}">
        <input type="hidden" name="drip_n" value="{{ $request['drip_n'] }}">
        <input type="hidden" name="drip_p2o5" value="{{ $request['drip_p2o5'] }}">
        <input type="hidden" name="drip_k2o" value="{{ $request['drip_k2o'] }}">
        <input type="hidden" name="drip_frit" value="{{ $request['drip_frit'] }}">
        <input type="hidden" name="frekuensi_drip" value="{{ $request['frekuensi_drip'] }}">
        <input type="hidden" name="input_p_tersedia" value="{{ $request['input_p_tersedia'] }}">
        <input type="hidden" name="p_status" value="{{ $request['p_status'] }}">
        <input type="hidden" name="input_k_tersedia" value="{{ $request['input_k_tersedia'] }}">
        <input type="hidden" name="k_status" value="{{ $request['k_status'] }}">
        <input type="hidden" name="input_mg" value="{{ $request['input_mg'] }}">
        <input type="hidden" name="mg_status" value="{{ $request['mg_status'] }}">
        <input type="hidden" name="input_ca" value="{{ $request['input_ca'] }}">
        <input type="hidden" name="ca_status" value="{{ $request['ca_status'] }}">
        <input type="hidden" name="input_corganik" value="{{ $request['input_corganik'] }}">
        <input type="hidden" name="corganik_status" value="{{ $request['corganik_status'] }}">
        <input type="hidden" name="input_ph" value="{{ $request['input_ph'] }}">

        <input type="hidden" name="pupuk_amsul" value="{{ $request['pupuk_amsul'] }}">
        <input type="hidden" name="pupuk_sp36" value="{{ $request['pupuk_sp36'] }}">
        <input type="hidden" name="pupuk_kci" value="{{ $request['pupuk_kci'] }}">
        <input type="hidden" name="pupuk_potsul" value="{{ $request['pupuk_potsul'] }}">
        <input type="hidden" name="pupuk_frits" value="{{ $request['pupuk_frits'] }}">
        <input type="hidden" name="pupuk_urea" value="{{ $request['pupuk_urea'] }}">

        <div class="row">
            <div class="col-12 col-md-12 col-lg-12 mb-3">
                <div class="card">
                    <div class="card-body">
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-3 text-danger" :errors="$errors" />

                        <div class="row mb-3">
                            <div class="col-12">
                                <h3>Spesifikasi Toren</h3>
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="data-input-duration" class="form-label">Jari-jari Toren (cm)</label>
                                <input type="number" min="0" step=".1" class="form-control" name="r_2_toren" value="{{ $perangkat->toren_pemupukan->r }}" disabled />
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="data-input-duration" class="form-label">Tinggi Toren (cm)</label>
                                <input type="number" min="0" step=".1" class="form-control" name="t_toren" value="{{ $perangkat->toren_pemupukan->tinggi_toren }}" disabled />
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="data-input-duration" class="form-label">Volume Toren (Liter)</label>
                                <input type="number" min="1" step="0.01" class="form-control" name="volume_toren"  value="{{ $perangkat->toren_pemupukan->v_toren }}" disabled />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <div class="row">
            <div class="col-12 col-md-12 col-lg-12 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-12 col-md-4">
                                <label for="data-input-duration" class="form-label">Tinggi Awal (CM)</label>
                                <input type="number" min="0" step=".01" class="form-control" name="t_awal"  value="{{ $perangkat->toren_pemupukan->tinggi_awal }}" disabled />
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="data-input-duration" class="form-label">Tinggi Akhir (CM)</label>
                                <input type="number" min="0" step=".01" class="form-control" name="t_akhir" value="{{ $perangkat->toren_pemupukan->tinggi_akhir }}" disabled />
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="data-input-duration" class="form-label">Volume air dari tinggi awal (Liter)</label>
                                <input type="number" step=".01" class="form-control" name="v_air_t_awal" value="{{ $perangkat->toren_pemupukan->v_fron_t_toren }}" disabled />
                            </div>
                        </div>
                        <div class="row mb-3 d-flex justify-content-center">
                            <div class="col-12 col-md-4">
                                <label for="data-input-duration" class="form-label">Debit (Liter) / menit Perangkat</label>
                                <input type="number" step=".01" class="form-control" name="debit_alat" value="{{ $perangkat->toren_pemupukan->debit }}" disabled />
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="data-input-duration" class="form-label">Durasi Pemupukan disarankan (Menit)</label>
                                <input type="text" class="form-control" name="durasi_disarankan" value="{{ $perangkat->toren_pemupukan->duration }}" disabled />
                            </div>
                        </div>
                        <div class="row mb-3 d-flex justify-content-center">
                            <div class="col-12 col-md-4">
                                <label for="data-input-duration" class="form-label">Minggu</label>
                                <input type="number" class="form-control" value="{{ $minggu }}" disabled />
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="data-input-duration" class="form-label">Volume perminggu (Liter)</label>
                                <input type="text" class="form-control" name="v_minggu" disabled />
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="data-input-duration" class="form-label">Durasi perminggu (Menit:Detik)</label>
                                <input type="text" class="form-control" name="t_minggu" disabled />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-12 col-lg-6 mb-3">
                <!-- Striped Rows -->
                <div class="card">
                    <div class="card-body" id="form-penyiraman">

                        <h2 class="card-title">Penyiraman</h2>
                        <div class="row mb-3">
                            <div class="col-12">
                                @php
                                $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jum\'at', 'Sabtu'];
                                @endphp
                                <label class="form-label" for="inputGroupSelect01">Check Hari <span class="text-info">penyiraman</span> dalam seminggu</label>
                                <br>
                                @foreach ($days as $day)
                                <div class="form-check form-check-inline mt-3">
                                    <input class="form-check-input input-days" type="checkbox" value="{{ Str::lower($day) }}" id="watering-check-{{ $day }}" name="watering[days][]" />
                                    <label class="form-check-label" for="watering-check-{{ $day }}"> {{ $day }} </label>
                                </div>
                                @endforeach
                            </div>
                            <div class="col-12 mt-3">
                                <button type="button" class="btn btn-sm btn-primary" id="btn-add-waktu-penyiraman">Tambah Waktu</button>
                            </div>
                        </div>
                        <div id="setontime-penyiraman">
                        </div>
                    </div>
                </div>
                <!--/ Striped Rows -->
            </div>

            <div class="col-12 col-md-12 col-lg-6 mb-3">
                <div class="card">
                    <card class="card-body" id="form-pemupukan">
                        <h2 class="card-title">Pemupukan</h2>

                        <div class="row mb-3">
                            <div class="col-12">
                                @php
                                $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jum\'at', 'Sabtu'];
                                @endphp
                                <label class="form-label" for="inputGroupSelect01">Check Hari <span class="text-info">pemupukan</span> dalam seminggu</label>
                                <br>
                                @foreach ($days as $day)
                                <div class="form-check form-check-inline mt-3">
                                    <input class="form-check-input input-days" type="radio" value="{{ Str::lower($day) }}" id="fertilization-check-{{ $day }}" name="fertilization[days][]" />
                                    <label class="form-check-label" for="fertilization-check-{{ $day }}"> {{ $day }} </label>
                                </div>
                                @endforeach
                            </div>
                            <div class="col-12 mt-3 d-none">
                                <button type="button" class="btn btn-sm btn-primary" id="btn-add-waktu-pemupukan">Tambah Waktu</button>
                            </div>
                        </div>
                        <div class="row border-bottom mb-3 py-2">
                            <div class="col-12">
                                <div class="float-start">
                                    <h5>Kelola Waktu</h5>
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="data-input-morning-time" class="form-label">Waktu Pemupukan</label>
                                <input type="time" class="form-control" name="fertilization[setontimes][0][time]" />
                            </div>
                            <div class="col-2 mb-0 d-grid gap-2">

                            </div>
                            <div class="col-12 mt-3">
                                <label for="data-input-first-selenoid" class="form-label">Lahan</label>
                                <br>
                                <div class="d-flex gap-2">
                                    @foreach ($perangkat->selenoids as $selenoid)
                                        <button type="button" class="btn btn-outline-info btn-land" data-time="0" data-land="{{ $selenoid->selenoid_id }}" data-water-before-minutes="{{ $selenoid->selenoid_watering->water_before->minutes }}" data-water-before-seconds="{{ $selenoid->selenoid_watering->water_before->seconds }}" data-water-after-minutes="{{ $selenoid->selenoid_watering->water_after->minutes }}" data-water-after-seconds="{{ $selenoid->selenoid_watering->water_after->seconds }}">Lahan {{ $selenoid->land->name }} (SV{{ $selenoid->selenoid_id }})</button>
                                    @endforeach
                                </div>
                                <ol class="list-group mt-3">
                                </ol>
                            </div>
                            <div class="col-12 mt-1 d-none">
                                <label for="data-input-morning-time" class="form-label">Total Waktu Pemupukan</label>
                                <input type="text" class="form-control" id="total-waktu-pemupukan" disabled />
                            </div>
                        </div>
                        <div id="setontime-pemupukan">
                        </div>
                    </card>
                </div>
            </div>

            <div class="col-12 col-md-12 col-lg-12 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-12 col-md-6">
                                <label for="data-input-duration" class="form-label">Minggu</label>
                                <input type="number" min="1" max="15" class="form-control" name="minggu" value="{{ $minggu }}" readonly />
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="data-input-duration" class="form-label">Jenis Tanaman</label>
                                <input type="text" class="form-control" name="jenis_tanaman" value="{{ session('jenis_tanaman') }}" required />
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="data-input-duration" class="form-label">Perangkat</label>
                                <input type="text" class="form-control" value="{{ $perangkat->device_series }}" readonly />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <button type="submit" class="btn btn-primary"><span class='tf-icons bx bx-save'></span>&nbsp;Simpan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($perangkat->type == 1)
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <input type="hidden" name="jenis_tanaman" id="data-input-jenis-tanaman"
                                value="{{ session('jenis_tanaman') }}">
                            <input type="hidden" name="minggu" id="data-input-minggu" value="{{ $minggu }}">
                                <div class="col-md-6">
                                    <div class="input-group input-group-lg">
                                        <label class="input-group-text text-uppercase bg-white text-dark"
                                            for="inputGroupSelect01">Volume Toren (liter)</label>
                                        <input type="number" min="0" id="data-input-volume-toren"
                                            class="form-control custom-bg-cyan text-dark" name="volume_toren" value="1000"
                                            required />
                                    </div>
                                </div>
                                <div class="col-md-6 mt-2">
                                    @php
                                        $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jum\'at', 'Sabtu'];
                                    @endphp
                                    <label class="text-uppercase bg-white text-dark badge rounded-pill"
                                        for="inputGroupSelect01">Check Hari penyiraman dalam seminggu</label>
                                        <br>
                                    @foreach ($days as $day)
                                        <div class="form-check form-check-inline mt-3">
                                            <input class="form-check-input input-days" type="checkbox" value="{{ Str::lower($day) }}" id="check-{{ $day }}" name="days[]" />
                                            <label class="form-check-label" for="check-{{ $day }}"> {{ $day }} </label>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="col-md-6 mt-2">
                                    <div class="input-group input-group-lg">
                                        <label class="input-group-text text-uppercase bg-white text-dark"
                                            for="inputGroupSelect01">Set Lama Penyiraman (menit)</label>
                                        <input type="number" min="0" id="data-input-set-lama"
                                            class="form-control custom-bg-cyan text-dark" name="lama_siram"
                                            value="0"
                                            required />
                                    </div>
                                    <div class="input-group input-group-lg">
                                        <label class="input-group-text text-uppercase bg-white text-dark"
                                            for="inputGroupSelect01">Set Waktu Penyiraman 1</label>
                                        <input type="time"
                                            class="form-control custom-bg-cyan text-dark data-input-set-waktu" name="settime"
                                            required />
                                    </div>
                                    @for ($i = 2;$i < 6;$i++)
                                        <div class="input-group input-group-lg">
                                            <label class="input-group-text text-uppercase bg-white text-dark"
                                                for="inputGroupSelect01">Set Waktu Penyiraman {{ $i }}</label>
                                            <input type="time"
                                                class="form-control custom-bg-cyan text-dark data-input-set-waktu" name="settime"
                                                />
                                        </div>
                                    @endfor
                                </div>
                                <div class="col-md-12 mt-2 d-grid gap-2">
                                    <button class="btn btn-danger" id="btn-kirim">Kirim</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </form>

    @push('scripts')
        <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
        <script>
            let deviceSelenoids = []

            const createLandPick = (
                boxLands,
                boxChildren,
                landId,
                eButton,
                timeId,
                type,
                i,
                minutes,
                seconds,
                water_before_minutes = 0,
                water_before_seconds = 0,
                water_after_minutes = 0,
                water_after_seconds = 0,
            ) => {
                for (const eChild of boxChildren) {
                    if (eChild.dataset.land == landId) {
                        eChild.remove()
                        eButton.classList.remove('btn-info')
                        eButton.classList.add('btn-outline-info')
                        return true
                    }
                }

                eButton.classList.remove('btn-outline-info')
                eButton.classList.add('btn-info')

                let waterBeforeAfter = ``
                let eLandInfo = ``

                if (type == 'watering') {
                    eLandInfo = `<div class="col-12 mt-1">
                        <label for="data-input-duration" class="form-label">Durasi Penyiraman</label>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="input-group">
                            <input type="number" min="0" max="60" value="${minutes}" class="form-control minutes-pemupukan" name="${type}[setontimes][${timeId}][lands][${i}][duration]" placeholder="Menit" />
                            <span class="input-group-text" id="basic-addon13">Menit</span>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="input-group">
                            <input type="number" min="0" max="60" value="${seconds}" class="form-control seconds-pemupukan" name="${type}[setontimes][${timeId}][lands][${i}][seconds]" placeholder="Detik" />
                            <span class="input-group-text" id="basic-addon13">Detik</span>
                        </div>
                    </div>`
                } else if (type == 'fertilization') {
                    eLandInfo = `<div class="col-12 mt-1">
                        <label for="data-input-duration" class="form-label">Durasi Pemupukan</label>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="input-group">
                            <input type="number" min="0" max="60" value="${minutes}" class="form-control minutes-pemupukan" name="${type}[setontimes][${timeId}][lands][${i}][duration]" placeholder="Menit" readonly />
                            <span class="input-group-text" id="basic-addon13">Menit</span>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="input-group">
                            <input type="number" min="0" max="60" value="${seconds}" class="form-control seconds-pemupukan" name="${type}[setontimes][${timeId}][lands][${i}][seconds]" placeholder="Detik" readonly />
                            <span class="input-group-text" id="basic-addon13">Detik</span>
                        </div>
                    </div>
                    <input type="hidden" name="${type}[setontimes][${timeId}][lands][${i}][water_before][minutes]" value="${water_before_minutes}">
                    <input type="hidden" name="${type}[setontimes][${timeId}][lands][${i}][water_before][seconds]" value="${water_before_seconds}">
                    <input type="hidden" name="${type}[setontimes][${timeId}][lands][${i}][water_after][minutes]" value="${water_after_minutes}">
                    <input type="hidden" name="${type}[setontimes][${timeId}][lands][${i}][water_after][seconds]" value="${water_after_seconds}">`
                }

                // if (type == 'fertilization') {
                //     waterBeforeAfter = `
                //             <div class="col-12 mt-1">
                //                 <label for="data-input-morning-time" class="form-label">Durasi Penyiraman Sebelum Pemupukan </label>
                //             </div>
                //             <div class="col-12 col-md-6">
                //                 <div class="input-group">
                //                     <input type="number" min="0" max="60" value="${waterBeforeMinutes}" class="form-control" id="water-start-minutes" name="${type}[setontimes][${timeId}][lands][${i}][water_before][minutes]" />
                //                     <span class="input-group-text" id="basic-addon13">Menit</span>
                //                 </div>
                //             </div>
                //             <div class="col-12 col-md-6">
                //                 <div class="input-group">
                //                     <input type="number" min="0" max="60" value="${waterBeforeSeconds}" class="form-control" id="water-start-seconds" name="${type}[setontimes][${timeId}][lands][${i}][water_before][seconds]" />
                //                     <span class="input-group-text" id="basic-addon13">Detik</span>
                //                 </div>
                //             </div>
                //             <div class="col-12 mt-1">
                //                 <label for="data-input-morning-time" class="form-label">Durasi Pendorongan Pupuk dengan Air </label>
                //             </div>
                //             <div class="col-12 col-md-6">
                //                 <div class="input-group">
                //                     <input type="number" min="0" max="60" value="${waterAfterMinutes}" class="form-control" id="water-start-minutes" name="${type}[setontimes][${timeId}][lands][${i}][water_after][minutes]" />
                //                     <span class="input-group-text" id="basic-addon13">Menit</span>
                //                 </div>
                //             </div>
                //             <div class="col-12 col-md-6">
                //                 <div class="input-group">
                //                     <input type="number" min="0" max="60" value="${waterAfterSeconds}" placeholder="0" class="form-control" id="water-start-seconds" name="${type}[setontimes][${timeId}][lands][${i}][water_after][seconds]" />
                //                     <span class="input-group-text" id="basic-addon13">Detik</span>
                //                 </div>
                //             </div>`
                // }

                boxLands.insertAdjacentHTML(
                    "beforeend",
                    `<li class="list-group-item" data-land="${landId}">
                        <div class="row g-2 ${type == 'fertilization' ? 'times-lands-pemupukan' : ''}">
                            <div class="col-12 col-md-12 d-flex align-items-center">
                                <h5>Lahan (SV${landId})</h5>
                            </div>
                            ${eLandInfo}
                        </div>
                        <input type="hidden" name="${type}[setontimes][${timeId}][lands][${i}][id]" value="${landId}">
                    </li>`,
                )

                return true
            }

            const createTimeElement = (eParentTime, type, i) => {
                let eSelenoids = ``

                for (const selenoid of deviceSelenoids) {
                    eSelenoids += `<button type="button" class="btn btn-outline-info btn-land" data-time="${i}" data-land="${selenoid.selenoid_id}">Lahan ${selenoid.land.name} (SV${selenoid.selenoid_id})</button>`
                }

                eParentTime.insertAdjacentHTML(
                    "beforeend",
                    `<div class="row border-bottom mb-3 py-2 time-watering">
                        <div class="col-12">
                            <div class="float-start">
                                <h5>Kelola Waktu</h5>
                            </div>
                            <div class="float-end">
                                <button type="button" class="btn btn-danger btn-sm btn-icon btn-delete-time"><span class='tf-icons bx bx-trash event-none'></span></button>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="data-input-morning-time" class="form-label">Waktu</label>
                            <input type="time" class="form-control" name="${type}[setontimes][${i}][time]" />
                        </div>
                        <div class="col-2 mb-0 d-grid gap-2">

                        </div>
                        <div class="col-12 mt-3">
                            <label for="data-input-first-selenoid" class="form-label">Lahan</label>
                            <br>
                            <div class="d-flex gap-2">
                                ${eSelenoids}
                            </div>
                            <ol class="list-group mt-3">
                            </ol>
                        </div>
                    </div>`,
                )
            }

            const deleteTimeElement = (eTime) => {
                eTime.remove()
            }

            const durasiPemupukan = (debit_keluar, air_diperlukan) => {
                let result = (air_diperlukan / debit_keluar).toFixed(1)
                let minutes = Math.floor(result)
                let seconds = Math.floor((result - minutes) * 60)

                seconds = (seconds < 10) ? "0" + seconds : seconds

                document.querySelector('[name="durasi_disarankan"]').value = `${minutes}:${seconds}`
                // document.querySelector('[name="durasi_disarankan_d"]').value = seconds
                return result
            }

            async function getDeviceSelenoids(device_id) {
                try {
                    let response = await axios.get("{{ route('bitanic.v3-device.get-lands', 'ID') }}".replace('ID', device_id), {
                        'Accept': 'application/json'
                    });

                    console.log(response);

                    if (response.data.length > 0) {
                        deviceSelenoids = [...response.data]
                    }
                } catch (error) {
                    console.error(error.message);
                }
            }

            const totalWaktu = () => {
                let totalMinutes = 0
                let totalSeconds = 0
                document.querySelectorAll('.times-lands-pemupukan').forEach(eMinutePemupukan => {
                    totalSeconds += parseInt(eMinutePemupukan.children[2].children[1].value)
                    totalMinutes += parseInt(eMinutePemupukan.children[1].children[1].value)
                })

                const eWaterStartMinutes = document.querySelector('#water-start-minutes').value
                const eWaterEndMinutes = document.querySelector('#water-end-minutes').value
                const eWaterStartSeconds = document.querySelector('#water-start-seconds').value
                const eWaterEndSeconds = document.querySelector('#water-end-seconds').value

                totalMinutes += (parseInt(eWaterStartMinutes ? eWaterStartMinutes : 0))
                totalMinutes += (parseInt(eWaterEndMinutes ? eWaterEndMinutes : 0))
                totalSeconds += (parseInt(eWaterStartSeconds ? eWaterStartSeconds : 0))
                totalSeconds += (parseInt(eWaterEndSeconds ? eWaterEndSeconds : 0))

                if (totalSeconds >= 60) {
                    let newMinutes = totalMinutes + Math.floor(totalSeconds / 60)
                    totalSeconds = totalSeconds - ((newMinutes - totalMinutes) * 60)
                    totalMinutes = newMinutes
                }
                document.querySelector('#total-waktu-pemupukan').value = `${totalMinutes}:${totalSeconds}`
            }

            const initDurasiPerMinggu = () => {
                const tAwal = document.querySelector('[name="t_awal"]').value
                const tAkhir = document.querySelector('[name="t_akhir"]').value
                const jari2Toren = document.querySelector('[name="r_2_toren"]').value
                const volume = ((3.14 * Math.pow(jari2Toren, 2) * tAwal) / 1000).toFixed(2)
                let hasilT = (tAwal - tAkhir)
                const debit = ((3.14 * Math.pow(jari2Toren, 2) * hasilT) / 1000).toFixed(2)
                document.querySelector('[name="debit_alat"]').value = debit
                const dPemupukan = durasiPemupukan(debit, volume)
                const countMinggu = document.querySelector('[name="minggu"]').value

                let minutes = Math.floor((dPemupukan / countMinggu).toFixed(1))
                let seconds = Math.floor(((dPemupukan / countMinggu).toFixed(1) - minutes) * 60)

                seconds = (seconds < 10) ? "0" + seconds : seconds


                const vMinggu = (document.querySelector('[name="v_air_t_awal"]').value / countMinggu).toFixed(2)
                const tMinggu = `${minutes}:${seconds}`

                document.querySelector('[name="v_minggu"]').value = vMinggu
                document.querySelector('[name="t_minggu"]').value = tMinggu
            }

            document.addEventListener("DOMContentLoaded", function() {
                console.log("Ready!");

                initDurasiPerMinggu()

                getDeviceSelenoids(document.querySelector('[name="perangkat_id"]').value)

                let waktuPenyiramanCount = 0
                let waktuPemupukanCount = 0

                // durasiPemupukan(document.querySelector('[name="debit_alat"]').value, document.querySelector('[name="air_diperlukan"]').value)

                document.getElementById('btn-add-waktu-penyiraman').addEventListener('click', e => {
                    e.preventDefault()

                    createTimeElement(document.getElementById('setontime-penyiraman'), 'watering', waktuPenyiramanCount)

                    waktuPenyiramanCount++
                })
                document.getElementById('btn-add-waktu-pemupukan').addEventListener('click', e => {
                    e.preventDefault()

                    createTimeElement(document.getElementById('setontime-pemupukan'), 'fertilization', waktuPemupukanCount)

                    waktuPemupukanCount++
                })

                document.querySelector('[name="r_2_toren"]').addEventListener('change', e => {
                    e.preventDefault()

                    const tinggiToren = document.querySelector('[name="t_toren"]').value
                    const tAwal = document.querySelector('[name="t_awal"]').value
                    const vToren = ((3.14 * Math.pow(e.target.value, 2) * tinggiToren) / 1000).toFixed(2)
                    const vTorenAwal = ((3.14 * Math.pow(e.target.value, 2) * tAwal) / 1000).toFixed(2)

                    document.querySelector('[name="volume_toren"]').value = vToren
                    document.querySelector('[name="v_air_t_awal"]').value = vTorenAwal
                    // durasiPemupukan(document.querySelector('[name="debit_alat"]').value, airDiperlukan)
                })
                document.querySelector('[name="t_toren"]').addEventListener('change', e => {
                    e.preventDefault()

                    const jari2Toren = document.querySelector('[name="r_2_toren"]').value
                    const vToren = ((3.14 * Math.pow(jari2Toren, 2) * e.target.value) / 1000).toFixed(2)
                    document.querySelector('[name="volume_toren"]').value = vToren
                    // durasiPemupukan(document.querySelector('[name="debit_alat"]').value, airDiperlukan)
                })
                document.querySelector('[name="t_awal"]').addEventListener('change', e => {
                    e.preventDefault()

                    const tAwal = e.target.value
                    const tAkhir = document.querySelector('[name="t_akhir"]').value
                    const jari2Toren = document.querySelector('[name="r_2_toren"]').value
                    const volume = ((3.14 * Math.pow(jari2Toren, 2) * tAwal) / 1000).toFixed(2)
                    let hasilT = (tAwal - tAkhir)
                    const debit = ((3.14 * Math.pow(jari2Toren, 2) * hasilT) / 1000).toFixed(2)
                    document.querySelector('[name="debit_alat"]').value = debit
                    durasiPemupukan(debit, volume)

                    const vToren = ((3.14 * Math.pow(jari2Toren, 2) * tAwal) / 1000).toFixed(2)
                    document.querySelector('[name="v_air_t_awal"]').value = vToren
                    // durasiPemupukan(document.querySelector('[name="debit_alat"]').value, airDiperlukan)
                })
                document.querySelector('[name="t_akhir"]').addEventListener('change', e => {
                    e.preventDefault()

                    const tAwal = document.querySelector('[name="t_awal"]').value
                    const tAkhir = e.target.value
                    const jari2Toren = document.querySelector('[name="r_2_toren"]').value
                    const volume = ((3.14 * Math.pow(jari2Toren, 2) * tAwal) / 1000).toFixed(2)
                    let hasilT = (tAwal - tAkhir)
                    const debit = ((3.14 * Math.pow(jari2Toren, 2) * hasilT) / 1000).toFixed(2)
                    document.querySelector('[name="debit_alat"]').value = debit
                    const dPemupukan = durasiPemupukan(debit, volume)
                    const countMinggu = document.querySelector('[name="minggu"]').value

                    let minutes = Math.floor((dPemupukan / countMinggu).toFixed(1))
                    let seconds = Math.floor(((dPemupukan / countMinggu).toFixed(1) - minutes) * 60)

                    seconds = (seconds < 10) ? "0" + seconds : seconds


                    const vMinggu = (document.querySelector('[name="v_air_t_awal"]').value / countMinggu).toFixed(2)
                    const tMinggu = `${minutes}:${seconds}`

                    document.querySelector('[name="v_minggu"]').value = vMinggu
                    document.querySelector('[name="t_minggu"]').value = tMinggu
                })
                document.querySelector('[name="debit_alat"]').addEventListener('change', e => {
                    e.preventDefault()

                    const airDiperlukan = document.querySelector('[name="volume_toren"]').value

                    durasiPemupukan(e.target.value, airDiperlukan)
                })

                let wateringCount = 0
                let fertilizationCount = 0

                document.querySelector('#form-pemupukan').addEventListener('click', e => {
                    if (e.target.classList.contains('btn-land')) {

                        let times = document.querySelector('[name="t_minggu"]').value.split(':')
                        let minutes = times.length > 0 ? parseInt(times[0]) : 0
                        let seconds = times.length > 0 ? parseInt(times[1]) : 0

                        // console.log(e.target.dataset);
                        createLandPick(
                            e.target.parentElement.nextElementSibling,
                            e.target.parentElement.nextElementSibling.children,
                            e.target.dataset.land,
                            e.target,
                            e.target.dataset.time,
                            "fertilization",
                            fertilizationCount,
                            minutes,
                            seconds,
                            e.target.dataset.waterBeforeMinutes,
                            e.target.dataset.waterBeforeSeconds,
                            e.target.dataset.waterAfterMinutes,
                            e.target.dataset.waterAfterSeconds,
                        )

                        fertilizationCount++
                        // totalWaktu()

                        let landsTimes = ((minutes * 60) + seconds) / document.querySelectorAll('.times-lands-pemupukan').length

                        document.querySelectorAll('.times-lands-pemupukan').forEach(eMinutePemupukan => {
                            eMinutePemupukan.children[2].children[0].children[0].value = Math.floor(landsTimes / 60)
                            eMinutePemupukan.children[3].children[0].children[0].value = Math.floor(landsTimes % 60)
                        })
                    } else if(e.target.classList.contains('btn-delete-time')){
                        console.log('Delete time');
                        deleteTimeElement(e.target.parentElement.parentElement.parentElement)
                    }
                })
                document.querySelector('#form-penyiraman').addEventListener('click', e => {
                    if (e.target.classList.contains('btn-land')) {
                        [minutes, seconds] = document.querySelector('[name="t_minggu"]').value.split(':')
                        createLandPick(
                            e.target.parentElement.nextElementSibling,
                            e.target.parentElement.nextElementSibling.children,
                            e.target.dataset.land,
                            e.target,
                            e.target.dataset.time,
                            "watering",
                            wateringCount,
                            parseInt(minutes),
                            parseInt(seconds)
                        )

                        wateringCount++
                    } else if(e.target.classList.contains('btn-delete-time')){
                        console.log('Delete time');
                        deleteTimeElement(e.target.parentElement.parentElement.parentElement)
                    }
                })

                // Submit button handler
                const submitButton = document.getElementById('btn-kirim');
                if (submitButton) {
                    submitButton.addEventListener('click', function(e) {
                        // Prevent default button action
                        e.preventDefault();

                        // Show loading indication
                        submitButton.setAttribute('data-kt-indicator', 'on');

                        // Disable button to avoid multiple click
                        submitButton.disabled = true;

                        // Simulate form submission. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                        let url, formSubmited;
                        const formData = new FormData();

                        let daysChecked = $('.input-days:checked')
                        let eTimes = $('.data-input-set-waktu')
                        let dataDays = []
                        let setTimes = []

                        for (const element of daysChecked) {
                            dataDays.push(element.value)
                        }

                        for (const element of eTimes) {
                            if (element.value) {
                                setTimes.push(element.value)
                            }
                        }

                        console.log(setTimes);

                        formData.append("jenis_tanaman", document.getElementById('data-input-jenis-tanaman').value)
                        formData.append("volume_toren", document.getElementById('data-input-volume-toren').value)
                        formData.append("perangkat_id", document.getElementById('data-input-perangkat-id').value)
                        formData.append("minggu", document.getElementById('data-input-minggu').value)
                        formData.append("setwaktu", setTimes)
                        formData.append("setlama", document.getElementById('data-input-set-lama').value)
                        formData.append("days", dataDays)

                        url = ""
                        formSubmited = axios.post(url, formData, {
                            headers: {
                                'X-ferads-token': 'cPzC7advUBmnAJe1hx8P',
                                "Accept": "application/json"
                            }
                        })


                        formSubmited.then((response) => {

                                // Remove loading indication
                                submitButton.removeAttribute('data-kt-indicator');

                                // Enable button
                                submitButton.disabled = false;
                                let div = document.getElementById('toasts')
                                div.innerHTML = `
                                <div class="toast fade show bs-toast align-items-center bg-success" role="alert" aria-live="assertive" aria-atomic="true">
                                    <div class="d-flex">
                                        <div class="toast-body">
                                            ${response.data.message}
                                        </div>
                                        <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                                    </div>
                                </div>`

                                let selectPerangkat = document.getElementById('data-input-perangkat-id')

                                let listPerangkat = `<option value="">Pilih...</option>`

                                response.data.data.listPerangkat.forEach(perangkat => {
                                    listPerangkat +=
                                        `<option value="${perangkat.id}">${perangkat.seri_perangkat}</option>`
                                });

                                selectPerangkat.innerHTML = listPerangkat

                            })
                            .catch((error) => {
                                let errorMessage = error
                                let message = ""

                                if (error.hasOwnProperty('response')) {
                                    for (const key in error.response.data.message) {
                                        if (Object.hasOwnProperty.call(error.response.data.message, key)) {
                                            message += error.response.data.message[key] + "\n";
                                        } else {
                                            message = key
                                        }
                                    }
                                    errorMessage = message
                                    if (error.response.status == 422) {
                                        errorMessage = 'Data yang dikirim tidak sesuai'
                                    }
                                }

                                Swal.fire({
                                    text: errorMessage,
                                    icon: "error",
                                    buttonsStyling: false,
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                });

                                // Remove loading indication
                                submitButton.removeAttribute('data-kt-indicator');

                                // Enable button
                                submitButton.disabled = false;
                            });
                    });
                }
            });
        </script>
    @endpush
</x-app-layout>
