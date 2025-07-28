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
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master / <a href="{{ route('bitanic.device.index') }}">Data Perangkat</a> / {{ $device->device_series }} /</span> Edit </h4>
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
                    <form action="{{ route('bitanic.device.update', $device->id) }}" method="POST" id="form-product" enctype="multipart/form-data">
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
                                <img src="{{ asset($device->picture ?? 'bitanic-landing/default-image.jpg') }}" alt="preview-img" class="preview-image img-thumbnail">
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
                                <input type="number" min="0" step=".1" id="data-input-versi" class="form-control" name="version" value="{{ $device->version }}" required />
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
        }
    </script>
    @endpush
</x-app-layout>
