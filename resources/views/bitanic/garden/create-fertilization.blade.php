<x-app-layout>

    @push('styles')
    {{-- Cluster --}}
    <link rel="stylesheet" href="{{ asset('css/extend.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
    </style>
    @endpush
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master @if(Auth::user()->role == 'admin') / <a href="{{ route('bitanic.farmer.index') }}">Data Pengguna Bitanic Pro</a> @endif / <a href="{{ route('bitanic.farmer.show', $farmer->user_id) }}">{{ $farmer->full_name }}</a> / <a href="{{ route('bitanic.garden.index', $farmer->id) }}">Data Kebun</a> / </span> Tambah Pemupukan</h4>
    </x-slot>
    {{-- End Header --}}

    <div class="row">
        <div class="col-md-12">
            <!-- Striped Rows -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('bitanic.garden.store', ['farmer' => $farmer->id]) }}" method="POST" id="form-product" enctype="multipart/form-data">
                        @csrf
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />

                        <div class="row">
                            <div class="col mb-3">
                                <label class="form-label">Nama Tanaman</label>
                                <input type="text" class="form-control" id="data-input-nama-tanaman" name="name_tanaman" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="data-input-set-minggu" class="form-label">Set Minggu</label>
                                <input type="number" min="0" max="15" class="form-control" id="data-input-set-minggu" name="set_minggu" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label class="form-label">Set Hari</label>

                                <br>

                                @php
                                $days = ['minggu', 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];
                                @endphp

                                @foreach ($days as $day)
                                <div class="form-check form-check-inline mt-2">
                                    <input class="form-check-input data-input-set-hari" type="checkbox" value="{{ $day }}" id="hari-{{ $day }}" />
                                    <label class="form-check-label" for="hari-{{ $day }}"> {{ ucwords($day) }} </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col mb-0">
                                <label for="data-input-set-waktu" class="form-label">Set Waktu</label>
                                <input type="time" class="form-control" id="data-input-set-waktu" name="set_waktu" />
                            </div>
                            <div class="col mb-0">
                                <label for="data-input-set-menit" class="form-label">Set menit</label>
                                <input type="number" min="0" max="60" class="form-control" id="data-input-set-menit" name="set_menit" />
                            </div>
                        </div>
                        <div class="row">
                            <button type="submit" class="btn btn-primary" id="submit-btn">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
            <!--/ Striped Rows -->
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('theme/js/ui-popover.js') }}"></script>
    <script src="{{ asset('js/extend.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- <script src="{{ asset('js/extra.js') }}"></script> -->
    <script>
    </script>
    @endpush
</x-app-layout>
