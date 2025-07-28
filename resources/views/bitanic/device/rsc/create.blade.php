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
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master / <a href="{{ route('bitanic.device.index') }}">Data Perangkat</a> </span>/ Tambah </h4>
    </x-slot>
    {{-- End Header --}}

    <div class="row">
        <div class="col-md-12">
            <!-- Striped Rows -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('bitanic.device.store') }}" method="POST" id="form-product" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="category" value="{{ $category }}">
                        <input type="hidden" name="type" value="{{ $type }}">
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />

                        <div class="row">
                            <div class="col mb-3">
                                <label for="data-input-category" class="form-label">Kategori</label>
                                <input type="text" id="data-input-category" class="form-control" value="{{ ucwords($category) }}" disabled />
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-12 col-md-6 mb-3">
                                <label for="" class="form-label">Preview Foto</label>
                                <img src="{{ asset('bitanic-landing/default-image.jpg') }}" alt="preview-foto" class="preview-image img-thumbnail">
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="data-input-foto" class="form-label">Foto</label>
                                <input class="form-control" type="file" id="data-input-foto" name="picture"
                                    accept="image/png, image/jpg, image/jpeg" aria-describedby="pictureHelp" />
                                    <div id="pictureHelp" class="form-text">Format gambar JPG, JPEG, PNG. Maks.
                                        2MB</div>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-12 col-md mb-3">
                                <label for="data-input-seri-perangkat" class="form-label">ID Perangkat</label>
                                <input type="text" id="data-input-seri-perangkat" class="form-control" name="device_series" required />
                            </div>
                            <div class="col-12 col-md mb-3">
                                <label for="data-input-versi" class="form-label">Versi</label>
                                <input type="number" min="0" id="data-input-versi" class="form-control" name="version" required />
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col mb-3">
                                <label for="data-input-tgl-produksi" class="form-label">Tanggal Produksi</label>
                                <input type="date" class="form-control" id="data-input-tgl-produksi" name="production_date" placeholder="0" aria-label="0" aria-describedby="basic-addon13" />
                            </div>
                            <div class="col mb-3">
                                <label for="data-input-tgl-pembelian" class="form-label">Tanggal Pembelian</label>
                                <input type="date" class="form-control" id="data-input-tgl-pembelian" name="purchase_date" placeholder="0" aria-label="0" aria-describedby="basic-addon13" />
                            </div>
                            <div class="col mb-3">
                                <label for="data-input-tgl-aktifkan" class="form-label">Tanggal Diaktifkan <span class="text-danger">*tidak wajib</span></label>
                                <input type="date" class="form-control" id="data-input-tgl-aktifkan" name="activate_date" placeholder="0" aria-label="0" aria-describedby="basic-addon13" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="data-input-petani-id" class="form-label">Pengguna</label>
                                <select class="form-select" id="data-input-petani-id" name="farmer_id" aria-label="Default select example">
                                    @if (auth()->user()->role != 'farmer')
                                        <option value="">Tanpa Pengguna</option>
                                    @endif
                                    @forelse ($farmers as $farmer)
                                        <option value="{{ $farmer->id }}">
                                            {{ $farmer->full_name }} | {{ $farmer->user->phone_number }}
                                        </option>
                                    @empty
                                        <option value="" disabled>Tidak ada data</option>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-2">
                                <button class="btn btn-info" id="btn-new-spesifik">Tambah Spesifikasi</button>
                            </div>
                            <div class="col-md-12" id="form-spesifik">
                                {{-- spesifikasi --}}
                            </div>
                        </div>
                        <div class="row mt-3">
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

        const newPompa = (name, inputClass, formElement) => {
            console.dir(formElement)
            formElement.insertAdjacentHTML("beforeend", `<div class="row g-2 mb-3">
                <div class="col-10 mb-0">
                    <label class="form-label">Pompa ${formElement.children.length + 1}</label>
                    <input
                        type="text"
                        class="form-control ${inputClass}"
                        name="${name}"
                        value="new"
                        readonly
                    />
                </div>
                <div class="col-2 mb-0 d-grid gap-2">
                    <button class="btn btn-danger btn-block mt-3 btn-delete-element">X</button>
                </div>
            </div>`)
        }

        const newSpesifikasi = (formElement) => {
            formElement.insertAdjacentHTML("beforeend", `<div class="row g-2 mb-3 custom-spesifikasi">
                <div class="col-5 mb-0">
                    <label class="form-label">Nama Spesifikasi</label>
                    <input
                        type="text"
                        class="form-control data-input-nama-spesifik"
                        name="spesifikasi[${spesifikasi_count}][name]"
                        data-id=""
                    />
                </div>
                <div class="col-5 mb-0">
                    <label class="form-label">Isi Spesifikasi</label>
                    <input
                        type="text"
                        class="form-control data-input-value-spesifik"
                        name="spesifikasi[${spesifikasi_count}][value]"
                        data-id=""
                    />
                </div>
                <div class="col-2 mb-0 d-grid gap-2">
                    <button class="btn btn-danger btn-block mt-3 btn-delete-element">X</button>
                </div>
            </div>`)

            spesifikasi_count++
        }

        const deleteElement = (e) => {
            e.preventDefault()

            if (e.target.classList.contains('btn-delete-element')) {
                e.target.parentNode.parentNode.remove()
            }
        }

        window.onload = function() {
            console.log('Hello world');

            $('#data-input-petani-id').select2({
                placeholder: 'Pilih Pengguna',
                allowClear: true,
                language: {
                    noResults: function () {
                        return "Pengguna tidak ditemukan";
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

            document.querySelector('#btn-new-spesifik').addEventListener('click', e => {
                e.preventDefault()
                // newPompa('list_spesifik[]', 'data-input-spesifik', document.querySelector('#form-spesifik'))
                newSpesifikasi(document.querySelector('#form-spesifik'))
            })
            document.querySelector('#form-spesifik').addEventListener('click', deleteElement)
        }
    </script>
    @endpush
</x-app-layout>
