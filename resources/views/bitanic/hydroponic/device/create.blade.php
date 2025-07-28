<x-app-layout>

    @push('styles')
        <style>
            .preview-image {
                width: 100%;
                /* Adjust the width as desired */
                object-fit: cover;
                aspect-ratio: 1/1;
                border: 1px solid #9f999975;
            }

            .event-none {
                pointer-events: none;
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
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb fs-4">
                <li class="breadcrumb-item">
                    <a href="javascript:void(0);">Hidroponik</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('bitanic.hydroponic.device.index') }}">Perangkat IoT</a>
                </li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </nav>
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
                    <form action="{{ route('bitanic.hydroponic.device.store') }}" method="POST" id="form-product"
                        enctype="multipart/form-data">
                        @csrf
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />

                        <div class="row g-4">
                            <div class="col-12">
                                <span class="text-danger">* Wajib diisi</span>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="row g-2">
                                    <div class="col-12">
                                        <label for="data-input-series" class="form-label">Series&nbsp;<span
                                                class="text-danger">*</span></label>
                                        <input type="text" id="data-input-series" class="form-control" name="series"
                                            value="{{ old('series') }}" required />
                                    </div>
                                    <div class="col-12">
                                        <label for="data-input-version" class="form-label">Versi&nbsp;<span
                                                class="text-danger">*</span></label>
                                        <input type="text" id="data-input-version" class="form-control"
                                            name="version" value="{{ old('version') }}" required
                                            placeholder="1.0.0"/>
                                    </div>
                                    <div class="col-12">
                                        <label for="data-input-user" class="form-label">User</label>
                                        <select class="form-select" id="data-input-user" name="user_id"
                                            aria-label="Default select example">
                                            <option value="">Pilih User</option>
                                            @foreach ($hydroponicUsers as $id => $name)
                                                <option value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <div class="row g-2">
                                            <div class="col-12 col-md-4">
                                                <label for="data-input-production-date" class="form-label">Tanggal
                                                    Produksi&nbsp;<span class="text-danger">*</span></label>
                                                <input type="date" id="data-input-production-date"
                                                    class="form-control" name="production_date"
                                                    value="{{ old('production_date') }}" />
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <label for="data-input-purchase-date" class="form-label">Tanggal
                                                    Pembelian</label>
                                                <input type="date" id="data-input-purchase-date" class="form-control"
                                                    name="purchase_date" value="{{ old('purchase_date') }}" />
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <label for="data-input-activation-date" class="form-label">Tanggal
                                                    Aktivasi</label>
                                                <input type="date" id="data-input-activation-date"
                                                    class="form-control" name="activation_date"
                                                    value="{{ old('activation_date') }}" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label for="data-input-note" class="form-label">Note</label>
                                        <textarea class="form-control" id="data-input-note" name="note" rows="5">{{ old('note') }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="row g-2">
                                    <div class="col-12 col-md-6">
                                        <label for="" class="form-label">Preview Foto</label>
                                        <img src="{{ asset('bitanic-landing/default-profile.png') }}" alt="preview-img"
                                            class="preview-image img-thumbnail">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="data-input-foto" class="form-label">Foto&nbsp;<span
                                                class="text-danger">*</span></label>
                                        <input class="form-control" type="file" id="data-input-foto" name="picture"
                                            accept="image/png, image/jpg, image/jpeg"
                                            aria-describedby="pictureHelp" />
                                        <div id="pictureHelp" class="form-text">Format gambar JPG, JPEG, PNG. Maks.
                                            2MB</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <button type="submit" class="btn btn-primary float-end"
                                    id="submit-btn">Simpan</button>
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

            const newFees = (formElement, feesCount = 0) => {
                formElement.insertAdjacentHTML("beforeend", `<div class="d-flex flex-wrap gap-2">
                    <div class="flex-fill">
                        <input
                            type="text"
                            class="form-control data-input-fees-amount"
                            name="requirements[]"
                        />
                    </div>
                    <div class="flex-shrink-1 align-self-end">
                        <button class="btn btn-icon btn-danger btn-delete-element" title="Hapus Biaya"><i class="bx bx-trash event-none"></i></button>
                    </div>
                </div>`)

                return feesCount + 1
            }

            const deleteElement = (e) => {
                e.preventDefault()

                if (e.target.classList.contains('btn-delete-element')) {
                    e.target.parentNode.parentNode.remove()
                }
            }

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

                // Handle File upload
                document.querySelector('#data-input-foto').addEventListener('change', e => {
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
