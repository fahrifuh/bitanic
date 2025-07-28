<x-app-layout>

    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
    <style>

      .preview-image {
        width: 100%;
        /* Adjust the width as desired */
        object-fit: cover;
        aspect-ratio: 4/3;
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
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master / <a href="{{ route('bitanic.lite-device.index') }}">Perangkat Lite</a> / <a href="{{ route('bitanic.lite-device.show', $lite_device->id) }}">{{ $lite_device->full_series }}</a> /</span> Edit data</h4>
    </x-slot>
    {{-- End Header --}}

    @if (session()->has('success'))
        <x-alert-message class="alert-success">{{ session()->get('success') }}</x-alert-message>
    @endif

    <div class="row">
        <div class="col-md-12">
            <!-- Striped Rows -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('bitanic.lite-device.update', $lite_device->id) }}" method="POST"
                        id="form-product" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />

                        <div class="row">
                            <div class="col-12 col-md-3 mb-3">
                                <label for="" class="form-label">Preview Foto</label>
                                <img src="{{ asset($lite_device->image ?? 'bitanic-landing/default-image.jpg') }}" alt="preview-img" class="preview-image img-thumbnail">
                            </div>
                            <div class="col-12 col-md-9 mb-3">
                                <div class="row g-2">
                                    <div class="col-12">
                                        <div class="bg-info text-white p-3 rounded" role="alert">Foto dapat dikosongkan</div>
                                    </div>
                                    <div class="col-12">
                                        <label for="data-input-image" class="form-label">Foto</label>
                                        <input class="form-control" type="file" id="data-input-image" name="image"
                                            accept="image/png, image/jpg, image/jpeg" aria-describedby="pictureHelp" />
                                        <div id="pictureHelp" class="form-text">Format gambar JPG, JPEG, PNG. Maks.
                                            2MB</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                              <label for="data-input-levels" class="form-label">Versi</label>
                              <input type="text" id="data-input-levels" class="form-control" name="version" value="{{ $lite_device->version }}" required />
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col mb-3">
                                <label for="data-input-tgl-produksi" class="form-label">Tanggal Produksi</label>
                                <input type="date" class="form-control" id="data-input-tgl-produksi" name="production_date" value="{{ $lite_device->production_date }}" placeholder="0" aria-label="0" aria-describedby="basic-addon13" />
                            </div>
                            <div class="col mb-3">
                                <label for="data-input-tgl-pembelian" class="form-label">Tanggal Pembelian</label>
                                <input type="date" class="form-control" id="data-input-tgl-pembelian" name="purchase_date" value="{{ $lite_device->purchase_date }}" placeholder="0" aria-label="0" aria-describedby="basic-addon13" />
                            </div>
                            <div class="col mb-3">
                                <label for="data-input-tgl-aktifkan" class="form-label">Tanggal Diaktifkan <span class="text-danger">*tidak wajib</span></label>
                                <input type="date" class="form-control" id="data-input-tgl-aktifkan" name="activate_date" value="{{ $lite_device->activate_date }}" placeholder="0" aria-label="0" aria-describedby="basic-addon13" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="data-input-petani-id" class="form-label">Petani Lite <span class="text-danger">*tidak wajib</span></label>
                                <select class="form-select" id="data-input-petani-id" name="lite_user_id" aria-label="Default select example">
                                    <option value="">Tanpa Petani</option>
                                    @forelse ($lite_users as $id => $name)
                                        <option value="{{ $id }}" @if($id == $lite_device->lite_user_id) selected @endif>
                                            {{ $name }}
                                        </option>
                                    @empty
                                        <option value="" disabled>Tidak ada data</option>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3 g-2">
                            <div class="col-12">
                                <label for="data-input-morning-time" class="form-label">Setting TDS </label>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon13">Min</span>
                                    <input type="number" step=".01" class="form-control" name="min_tds"
                                        value="{{ $lite_device->min_tds }}" />
                                    <span class="input-group-text" id="basic-addon13">ppm</span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon13">Max</span>
                                    <input type="number" step=".01" class="form-control" name="max_tds"
                                        value="{{ $lite_device->max_tds }}" />
                                    <span class="input-group-text" id="basic-addon13">ppm</span>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3 g-2">
                            <div class="col-12">
                                <label for="data-input-morning-time" class="form-label">Setting pH </label>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon13">Min</span>
                                    <input type="number" step=".01" class="form-control" name="min_ph"
                                        value="{{ $lite_device->min_ph }}" />
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon13">Max</span>
                                    <input type="number" step=".01" class="form-control" name="max_ph"
                                        value="{{ $lite_device->max_ph }}" />
                                </div>
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
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('theme/js/ui-popover.js') }}"></script>
        <script>
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

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

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
                document.querySelector('#data-input-image').addEventListener('change', e => {
                    if (e.target.files.length == 0) {
                        // $('.profile').attr('src', defaultImage);
                    } else {
                        eventFile(e.target);
                    }
                })

            })
        </script>
    @endpush
</x-app-layout>
