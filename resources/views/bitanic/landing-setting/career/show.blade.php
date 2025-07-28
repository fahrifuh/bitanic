<x-app-layout>
    @push('styles')
        <style>
            #pest-image {
                height: 100%;
                object-fit: cover;
            }

            .device-status {
                background-color: #c3c3c3;
                width: 20px;
                height: 20px;
                border-radius: 20px;
            }

            .bg-on-status {
                background-color: #00ff08 !important;
            }

            .bg-disabled-status {
                background-color: #ff0000 !important;
            }

            .event-none {
                pointer-events: none;
            }

            .preview-image {
                width: 200px;
                height: 100%;
                /* Adjust the width as desired */
                object-fit: cover;
                aspect-ratio: 3/1;
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
                <li class="breadcrumb-item active">Detail</li>
            </ol>
        </nav>
    </x-slot>
    {{-- End Header --}}

    @if (session()->has('success'))
        <x-alert-message class="alert-success">{{ session()->get('success') }}</x-alert-message>
    @endif
    @if (session()->has('failed'))
        <x-alert-message class="alert-danger">{{ session()->get('failed') }}</x-alert-message>
    @endif

    <!-- Validation Errors -->
    <x-auth-validation-errors class="mb-4" :errors="$errors" />

    <div class="row d-flex justify-content-center">
        <div class="col-12">
            <!-- Striped Rows -->
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="card-title mb-0">Posisi {{ $career->position }}</h3>
                                    <button type="button" title="Klik untuk mengubah status" @class([
                                        'border-0',
                                        'badge',
                                        'rounded-pill',
                                        'bg-success' => $career->is_looking == 1,
                                        'bg-secondary' => $career->is_looking == 0,
                                    ]) onclick="changeStatus(this)"
                                        data-id="{{ $career->id }}"
                                        data-name="{{ $career->position }}">{{ $career->is_looking ? 'Dicari' : 'Tidak dicari' }}</button>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('bitanic.career.edit', $career->id) }}"
                                        class="btn btn-warning btn-icon" title="Edit Karir">
                                        <i class="bx bx-edit"></i>
                                    </a>
                                    <button type="button" onclick="deletePest(this)" data-id="{{ $career->id }}"
                                        data-name="{{ $career->position }}" class="btn btn-danger btn-icon"
                                        title="Hapus Karir">
                                        <i class="bx bx-trash event-none"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 border-end">
                            <div class="d-flex flex-column gap-2">
                                <div><label class="fw-bolder"
                                        for="">Departemen:</label><br />{{ $career->department }}</div>
                                <div><label class="fw-bolder" for="">Jam
                                        Kerja:</label><br />{{ $career->work_hour }}</div>
                                <div><label class="fw-bolder"
                                        for="">Lokasi:</label><br />{{ $career->location }}</div>
                                <div><label class="fw-bolder" for="">Deskripsi:</label><br />
                                    <p class="card-text">{{ $career->description }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 border-start px-4">
                            <h5>Persyaratan</h5>
                            <div class="list-group list-group-flush">
                                @foreach ($career->requirements as $requirement)
                                    <a href="javascript:void(0);"
                                        class="list-group-item list-group-item-action">{{ $requirement }}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Striped Rows -->
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('theme/js/ui-popover.js') }}"></script>
        <script>
            const deletePest = async e => {
                const result = await Swal.fire({
                    text: "Menghapus data tidak dapat dibatalkan, dan semua data yang berhubungan akan hilang",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Ya, hapus!",
                    cancelButtonText: "Tidak, batalkan",
                    customClass: {
                        confirmButton: "btn fw-bold btn-danger",
                        cancelButton: "btn fw-bold btn-active-light-primary"
                    }
                })

                if (!result.value) {
                    return false
                }

                showSpinner()

                const settings = {
                    method: 'DELETE',
                    headers: {
                        'x-csrf-token': "{{ csrf_token() }}",
                        'Accept': "application/json",
                    }
                }

                const [data, error] = await yourRequest(
                    "{{ route('bitanic.career.destroy', 'ID') }}".replace('ID',
                        e.dataset.id), settings
                )

                if (error) {

                    deleteSpinner()

                    let errorMessage = ''

                    if ("messages" in error) {
                        let element = ``
                        for (const key in error.messages) {
                            if (Object.hasOwnProperty.call(error.messages, key)) {
                                error.messages[key].forEach(message => {
                                    element += `<li>${message}</li>`;
                                });
                            }
                        }

                        errorMessage = `<ul>${element}</ul>`
                    } else {
                        errorMessage = error.message
                    }

                    Swal.fire({
                        html: errorMessage,
                        icon: "error",
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                }

                Swal.fire({
                    text: "Kamu berhasil menghapus data " + name + "!",
                    icon: "success",
                    showConfirmButton: false,
                    allowOutsideClick: false
                })

                window.location = "{{ route('bitanic.career.index') }}"
            }

            const changeStatus = async e => {
                const result = await Swal.fire({
                    title: "Ubah status?",
                    text: "Perubahan ini akan mempengaruhi data yang tampil di halaman utama.",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Ya",
                    cancelButtonText: "Batalkan",
                    customClass: {
                        confirmButton: "btn fw-bold btn-danger",
                        cancelButton: "btn fw-bold btn-active-light-primary"
                    }
                })

                if (!result.value) {
                    return false
                }

                showSpinner()

                const settings = {
                    method: 'PUT',
                    headers: {
                        'x-csrf-token': "{{ csrf_token() }}",
                        'Accept': "application/json",
                    }
                }

                const [data, error] = await yourRequest(
                    "{{ route('bitanic.career.change-status', 'ID') }}".replace('ID',
                        e.dataset.id), settings
                )

                deleteSpinner()
                if (error) {

                    let errorMessage = ''

                    if ("messages" in error) {
                        let element = ``
                        for (const key in error.messages) {
                            if (Object.hasOwnProperty.call(error.messages, key)) {
                                error.messages[key].forEach(message => {
                                    element += `<li>${message}</li>`;
                                });
                            }
                        }

                        errorMessage = `<ul>${element}</ul>`
                    } else {
                        errorMessage = error.message
                    }

                    Swal.fire({
                        html: errorMessage,
                        icon: "error",
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                }

                Swal.fire({
                    text: "Kamu berhasil mengubag data " + name + "!",
                    icon: "success",
                    showConfirmButton: true,
                    allowOutsideClick: false
                })

                window.location.reload()
            }

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

            })
        </script>
    @endpush
</x-app-layout>
