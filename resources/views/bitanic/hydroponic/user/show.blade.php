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
                width: 150px;
                /* Adjust the width as desired */
                object-fit: cover;
                aspect-ratio: 1/1;
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
                    <a href="{{ route('bitanic.hydroponic.user.index') }}">User</a>
                </li>
                <li class="breadcrumb-item active">{{ $hydroponicUser->name }}</li>
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
        <div class="col-6">
            <!-- Striped Rows -->
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="d-flex flex-column align-items-center gap-4">
                                @if ($hydroponicUser->picture)
                                    <img src="{{ asset($hydroponicUser->picture) }}" alt="perangkat-foto"
                                        class="preview-image d-block" id="uploadedAvatar" />
                                @endif
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="card-title mb-0">{{ $hydroponicUser->name }}</h3>
                                </div>
                                <div class="d-flex gap-2">
                                    <div class="btn-group">
                                        <button type="button"
                                            class="btn btn-primary btn-icon rounded-pill dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end" style="">
                                            <li>
                                                <a href="{{ route('bitanic.hydroponic.user.edit', $hydroponicUser->id) }}"
                                                    class="dropdown-item">
                                                    Edit Profile
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('bitanic.hydroponic.user.edit-password', $hydroponicUser->id) }}"
                                                    class="dropdown-item">
                                                    Edit Password
                                                </a>
                                            </li>
                                            <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <button type="button" onclick="deletePest(this)"
                                                    data-id="{{ $hydroponicUser->id }}"
                                                    data-name="{{ $hydroponicUser->name }}"
                                                    class="dropdown-item" title="Hapus User">
                                                    Hapus User
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex flex-column gap-2">
                                <div><label class="fw-bolder"
                                        for="">Username:</label><br />{{ $hydroponicUser->username }}</div>
                                <div><label class="fw-bolder"
                                        for="">Email:</label><br />{{ $hydroponicUser->email }}</div>
                                <div><label class="fw-bolder" for="">Nomor
                                        Handphone:</label><br />{{ phoneNumberFormat($hydroponicUser->phone_number) }}</div>
                                <div><label class="fw-bolder" for="">Jenis
                                        Kelamin:</label><br />{{ $hydroponicUser->gender->getLabelText() }}</div>
                                <div><label class="fw-bolder" for="">Alamat:</label><br />
                                    <p class="card-text">{{ $hydroponicUser->address }}</p>
                                </div>
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
                    "{{ route('bitanic.hydroponic.user.destroy', 'ID') }}".replace('ID',
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

                window.location = "{{ route('bitanic.hydroponic.user.index') }}"
            }

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

            })
        </script>
    @endpush
</x-app-layout>
