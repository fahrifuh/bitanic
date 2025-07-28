<x-app-layout>

    @push('styles')
        <style>
            .preview-image {
                width: 100%;
                /* Adjust the width as desired */
                object-fit: cover;
                aspect-ratio: 3/1;
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
                    <a href="javascript:void(0);">Pengaturan</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="javascript:void(0);">Halaman Utama</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('bitanic.career.index') }}">Karir</a>
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
                    <form action="{{ route('bitanic.career.store') }}" method="POST" id="form-product"
                        enctype="multipart/form-data">
                        @csrf
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="row g-2">
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                id="defaultCheck3" checked="" name="is_looking">
                                            <label class="form-check-label" for="defaultCheck3"> Dicari </label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label for="data-input-position" class="form-label">Posisi</label>
                                        <input type="text" id="data-input-position" class="form-control"
                                            name="position" value="{{ old('position') }}" required />
                                    </div>
                                    <div class="col-12">
                                        <label for="data-input-department" class="form-label">Departemen</label>
                                        <input type="text" id="data-input-department" class="form-control"
                                            name="department" value="{{ old('department') }}" required />
                                    </div>
                                    <div class="col-12">
                                        <label for="data-input-work-hour" class="form-label">Jam Kerja</label>
                                        <input type="text" id="data-input-work-hour" class="form-control"
                                            name="work_hour" value="{{ old('work_hour') }}" required />
                                    </div>
                                    <div class="col-12">
                                        <label for="data-input-location" class="form-label">Lokasi</label>
                                        <input type="text" id="data-input-location" class="form-control"
                                            name="location" value="{{ old('location') }}" required />
                                    </div>
                                    <div class="col-12">
                                        <label for="data-input-description" class="form-label">Deskripsi</label>
                                        <textarea class="form-control" id="data-input-description" name="description" rows="5">{{ old('description') }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="row mb-3">
                                    <div class="col-12 mb-2">
                                        <button class="btn btn-info" id="btn-new-fees">Tambah Persyaratan</button>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="d-flex flex-column gap-3" id="form-fees">
                                            {{-- requirements --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
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

                let feesCount = 0

                document.querySelector('#btn-new-fees').addEventListener('click', e => {
                    e.preventDefault()
                    feesCount = newFees(document.querySelector('#form-fees'), feesCount)
                })
                document.querySelector('#form-fees').addEventListener('click', deleteElement)

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
