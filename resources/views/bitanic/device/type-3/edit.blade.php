<x-app-layout>

    @push('styles')
    {{-- Cluster --}}
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/extend.css') }}">
    <style>
        .preview-image {
            width: 100%;
            /* Adjust the width as desired */
            object-fit: cover;
            aspect-ratio: 16/9;
            border: 1px solid #9f999975;
        }

        @media (max-width: 600px) {
            .preview-image {
                width: calc(100% - 10px);
            }
        }
    </style>
    @endpush
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master / <a href="{{ route('bitanic.device.index') }}">Data Perangkat</a> / <a href="{{ route('bitanic.v3-device.show', $device->id) }}">{{ $device->device_series }}</a> </span>/ Edit </h4>
    </x-slot>
    {{-- End Header --}}

    <div class="row">
        <div class="col-md-12">
            @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session()->get('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            <!-- Striped Rows -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('bitanic.v3-device.update', $device->id) }}" method="POST" id="form-product" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />

                        <div class="row">
                            <div class="col mb-3">
                                <label for="data-input-category" class="form-label">Kategori</label>
                                <input type="text" id="data-input-category" class="form-control" value="{{ ucwords($device->category) }}" disabled />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label for="" class="form-label">Preview Foto</label>
                                <img src="{{ asset($device->picture) }}" alt="preview-img" class="preview-image img-thumbnail">
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="data-input-foto" class="form-label">Foto</label>
                                <input class="form-control" type="file" id="data-input-foto" name="picture"
                                    accept="image/png, image/jpg, image/jpeg" aria-describedby="pictureHelp" />
                                    <div id="pictureHelp" class="form-text">Format gambar JPG, JPEG, PNG. Maks.
                                        2MB</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="data-input-seri-perangkat" class="form-label">ID Perangkat</label>
                                <input type="text" id="data-input-seri-perangkat" class="form-control" name="device_series" value="{{ $device->device_series }}" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="data-input-versi" class="form-label">Versi</label>
                                <input type="number" min="0" id="data-input-versi" class="form-control" name="version" value="{{ $device->version }}" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="data-input-delay" class="form-label">Delay (Detik)</label>
                                <input type="number" min="0" id="data-input-delay" class="form-control" name="delay" value="{{ $device->delay }}" required />
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <h3>Spesifikasi Toren Pemupukan</h3>
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="data-input-duration" class="form-label">Jari-jari Toren (cm)</label>
                                <input type="number" min="0" step=".1" class="form-control" name="r" value="{{ $device->toren_pemupukan->r }}" required />
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="data-input-duration" class="form-label">Tinggi Toren (cm)</label>
                                <input type="number" min="0" step=".1" class="form-control" name="tinggi_toren" value="{{ $device->toren_pemupukan->tinggi_toren }}" required />
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="data-input-duration" class="form-label">Volume Toren (Liter)</label>
                                <input type="number" min="1" step="0.01" class="form-control" name="v_toren" value="{{ $device->toren_pemupukan->v_toren }}" disabled />
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12 col-md-4">
                                <label for="data-input-duration" class="form-label">Tinggi Awal (CM)</label>
                                <input type="number" min="0" step=".01" class="form-control" name="tinggi_awal" value="{{ $device->toren_pemupukan->tinggi_awal }}" />
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="data-input-duration" class="form-label">Tinggi Akhir (CM)</label>
                                <input type="number" min="0" step=".01" class="form-control" name="tinggi_akhir" value="{{ $device->toren_pemupukan->tinggi_akhir }}" />
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="data-input-duration" class="form-label">Volume air dari tinggi awal</label>
                                <input type="number" step=".01" class="form-control" name="v_fron_t_toren" value="{{ $device->toren_pemupukan->v_fron_t_toren }}" disabled />
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12 col-md-6">
                                <label for="data-input-duration" class="form-label">Debit (Liter) / menit</label>
                                <input type="number" step=".01" class="form-control" name="debit" value="{{ $device->toren_pemupukan->debit }}" disabled />
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="data-input-duration" class="form-label">Durasi (Menit:Detik)</label>
                                <input type="text" class="form-control" name="duration" value="{{ $device->toren_pemupukan->duration }}" disabled />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="data-input-tgl-produksi" class="form-label">Tanggal Produksi</label>
                                <input type="date" class="form-control" id="data-input-tgl-produksi" name="production_date" value="{{ $device->production_date }}" placeholder="0" aria-label="0" aria-describedby="basic-addon13" />
                            </div>
                            <div class="col mb-3">
                                <label for="data-input-tgl-pembelian" class="form-label">Tanggal Pembelian</label>
                                <input type="date" class="form-control" id="data-input-tgl-pembelian" name="purchase_date" value="{{ $device->purchase_date }}" placeholder="0" aria-label="0" aria-describedby="basic-addon13" />
                            </div>
                            <div class="col mb-3">
                                <label for="data-input-tgl-aktifkan" class="form-label">Tanggal Diaktifkan <span class="text-danger">*tidak wajib</span></label>
                                <input type="date" class="form-control" id="data-input-tgl-aktifkan" name="activate_date" value="{{ $device->activate_date }}" placeholder="0" aria-label="0" aria-describedby="basic-addon13" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="data-input-petani-id" class="form-label">Petani <span class="text-danger">*tidak wajib</span></label>
                                <select class="form-select" id="data-input-petani-id" name="farmer_id" aria-label="Default select example">
                                    @if (auth()->user()->role != 'farmer')
                                        <option value="">Tanpa Petani</option>
                                    @endif
                                    @forelse ($farmers as $farmer)
                                        <option value="{{ $farmer->id }}" @if($device->farmer_id == $farmer->id) selected @endif>
                                            {{ $farmer->full_name }} | {{ $farmer->user->phone_number }}
                                        </option>
                                    @empty
                                        <option value="" disabled>Tidak ada data</option>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary float-end" id="submit-btn">Simpan</button>
                            </div>
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
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <!-- <script src="{{ asset('js/extra.js') }}"></script> -->
    <script>
        let spesifikasi_count = 0
        function eventFile(input) {
            // Validate
            if (input.files && input.files[0]) {
                let fileSize = input.files[0].size / 1024 / 1024; //MB Format
                let fileType = input.files[0].type;

                // validate size
                if (fileSize > 10) {
                    showAlert('Ukuran File tidak boleh lebih dari 2mb !');
                    input.value = '';
                    return false;
                }

                // validate type
                if (["image/jpeg", "image/jpg", "image/png"].indexOf(fileType) < 0) {
                    showAlert('Format File tidak valid !');
                    input.value = '';
                    return false;
                }

                let reader = new FileReader();

                reader.onload = function(e) {
                    document.querySelector('.preview-image').setAttribute('src', e.target.result)
                }

                reader.readAsDataURL(input.files[0]); // convert to base64 string
            }
        }

        function showAlert(title) {
            Swal.fire({
                icon: 'warning',
                title,
                confirmButtonText: 'Kembali',
            })
        }

        const durasiPemupukan = (debit_keluar, air_diperlukan) => {
            let result = (air_diperlukan / debit_keluar).toFixed(1)
            let minutes = Math.floor(result)
            let seconds = Math.floor((result - minutes) * 60)

            seconds = (seconds < 10) ? "0" + seconds : seconds

            document.querySelector('[name="duration"]').value = `${minutes}:${seconds}`
            return result
        }

        window.onload = function() {
            console.log('Hello world');

            $('#data-input-petani-id').select2({
                placeholder: 'Pilih Petani',
                allowClear: true,
                language: {
                    noResults: function () {
                        return "Petani tidak ditemukan";
                    }
                },
                escapeMarkup: function (markup) {
                    return markup;
                }
            });

            // Handle File upload
            document.querySelector('#data-input-foto').addEventListener('change', e => {
                if (e.target.files.length == 0) {
                    // $('.profile').attr('src', defaultImage);
                } else {
                    eventFile(e.target);
                }
            })
            document.querySelector('[name="r"]').addEventListener('change', e => {
                e.preventDefault()

                const tinggiToren = document.querySelector('[name="tinggi_toren"]').value
                const tAwal = document.querySelector('[name="tinggi_awal"]').value
                const rToren = e.target.value
                const vToren = ((3.14 * Math.pow(rToren, 2) * tinggiToren) / 1000).toFixed(2)
                const vTorenAwal = ((3.14 * Math.pow(rToren, 2) * tAwal) / 1000).toFixed(2)

                document.querySelector('[name="v_toren"]').value = vToren
                document.querySelector('[name="v_fron_t_toren"]').value = vTorenAwal
            })

            document.querySelector('[name="tinggi_toren"]').addEventListener('change', e => {
                e.preventDefault()

                const tinggiToren = e.target.value
                const tAwal = document.querySelector('[name="tinggi_awal"]').value
                const rToren = document.querySelector('[name="r"]').value
                const vToren = ((3.14 * Math.pow(rToren, 2) * tinggiToren) / 1000).toFixed(2)
                const vTorenAwal = ((3.14 * Math.pow(rToren, 2) * tAwal) / 1000).toFixed(2)

                document.querySelector('[name="v_toren"]').value = vToren
                document.querySelector('[name="v_fron_t_toren"]').value = vTorenAwal
            })

            document.querySelector('[name="tinggi_awal"]').addEventListener('change', e => {
                e.preventDefault()

                const tAwal = e.target.value
                const tAkhir = document.querySelector('[name="tinggi_akhir"]').value
                const jari2Toren = document.querySelector('[name="r"]').value
                const volume = ((3.14 * Math.pow(jari2Toren, 2) * tAwal) / 1000).toFixed(2)
                let hasilT = (tAwal - tAkhir)
                const debit = ((3.14 * Math.pow(jari2Toren, 2) * hasilT) / 1000).toFixed(2)
                document.querySelector('[name="debit"]').value = debit
                durasiPemupukan(debit, volume)

                const vToren = ((3.14 * Math.pow(jari2Toren, 2) * tAwal) / 1000).toFixed(2)
                document.querySelector('[name="v_fron_t_toren"]').value = vToren
            })
            document.querySelector('[name="tinggi_akhir"]').addEventListener('change', e => {
                e.preventDefault()

                const tAwal = document.querySelector('[name="tinggi_awal"]').value
                const tAkhir = e.target.value
                const jari2Toren = document.querySelector('[name="r"]').value
                const volume = ((3.14 * Math.pow(jari2Toren, 2) * tAwal) / 1000).toFixed(2)
                let hasilT = (tAwal - tAkhir)
                const debit = ((3.14 * Math.pow(jari2Toren, 2) * hasilT) / 1000).toFixed(2)
                document.querySelector('[name="debit"]').value = debit
                durasiPemupukan(debit, volume)

                const vToren = ((3.14 * Math.pow(jari2Toren, 2) * tAwal) / 1000).toFixed(2)
                document.querySelector('[name="v_fron_t_toren"]').value = vToren
                // durasiPemupukan(document.querySelector('[name="debit_alat"]').value, airDiperlukan)
            })
        }
    </script>
    @endpush
</x-app-layout>
