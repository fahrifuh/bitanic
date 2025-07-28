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
                aspect-ratio: 1/1;
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
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master / <a
                    href="{{ route('bitanic.lite-user.index') }}">Petani Lite</a> /</span> {{ $lite_user->name }}
        </h4>
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
        <div class="col-md-6">
            <!-- Striped Rows -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-column">
                        @if ($lite_user->picture)
                        <img src="{{ asset($lite_user->picture) }}" alt="preview-img"
                            class="preview-image img-thumbnail rounded">
                        @endif
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <h3 class="m-0">{{ $lite_user->name }}</h3>
                        <div class="d-flex gap-1">
                            <a href="{{ route('bitanic.lite-user.edit', $lite_user->id) }}"
                                class="btn btn-warning btn-sm" title="Edit Profile">
                                Edit Profile
                            </a>
                            <a href="{{ route('bitanic.lite-user.edit-password', $lite_user->id) }}"
                                class="btn btn-warning btn-sm" title="Edit Password">
                                Edit Password
                            </a>
                            <button type="button" class="btn btn-danger btn-sm" data-id="{{ $lite_user->id }}"
                                data-name="{{ $lite_user->full_series }}"
                                data-delete-url="{{ route('bitanic.lite-user.destroy', $lite_user->id) }}"
                                data-redirect-url="{{ route('bitanic.lite-user.index') }}"
                                onclick="deleteData(this.dataset)" title="Hapus Pengguna">
                                Hapus
                            </button>
                        </div>
                    </div>
                    <hr />
                    <div class="d-flex flex-column align-items-start gap-2 mb-3">
                        <span><strong>Jenis Kelamin</strong>: <br>{{ gender_format($lite_user->gender) }}</span>
                        <span><strong>Nomor Handphone</strong>: <br>{{ $lite_user->phone_number }}</span>
                        <span><strong>NIK</strong>: <br>{{ $lite_user->nik }}</span>
                        <span><strong>Tanggal Lahir</strong>:<br>
                            {{ now()->parse($lite_user->birth_date)->formatLocalized('%d %B %Y') }}</span>
                        <span><strong>Alamat</strong>: <br>{{ $lite_user->address }}</span>
                    </div>
                </div>
            </div>
            <!--/ Striped Rows -->
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('theme/js/ui-popover.js') }}"></script>
        <script>
            const deleteData = async ({
                id,
                name,
                deleteUrl,
                redirectUrl = null
            }) => {
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
                    deleteUrl.replace('ID',
                        id), settings
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

                if (redirectUrl) {
                    window.location = redirectUrl
                } else {
                    window.location.reload();
                }
            }
            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

            })
        </script>
    @endpush
</x-app-layout>
