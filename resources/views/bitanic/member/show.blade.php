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

            .current-border {
                border: 2px solid #02de1f;
                transition: outline .2s ease-out;
            }
            .current-border:hover {
                outline: 5px solid #02de1f
            }
            .canceled-border {
                border: 2px solid #ff4e4e;
                transition: outline .2s ease-out;
            }
            .canceled-border:hover {
                outline: 5px solid #ff4e4e
            }
            .history-border {
                border: 2px solid #4fbeff;
                transition: outline .2s ease-out;
            }
            .history-border:hover {
                outline: 5px solid #4fbeff
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
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master / <a
                    href="{{ route('bitanic.member.index') }}">Member</a> /</span> {{ $member->name }}</h4>
        </h4>
    </x-slot>
    {{-- End Header --}}

    <div class="row d-flex justify-content-center">
        <div class="col-md-6">
            <div class="row gap-3">
                @if (session()->has('success'))
                    <div class="col-12">
                        <x-alert-message class="alert-success">{{ session()->get('success') }}</x-alert-message>
                    </div>
                @endif
                @if (session()->has('failed'))
                    <div class="col-12">
                        <x-alert-message class="alert-danger">{{ session()->get('failed') }}</x-alert-message>
                    </div>
                @endif

                <div class="col-12">
                    <!-- Striped Rows -->
                    <div class="card">
                        <div class="card-body">
                            <!-- Validation Errors -->
                            <x-auth-validation-errors class="mb-4" :errors="$errors" />
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h3 class="m-0">{{ $member->name }}</h3>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('bitanic.member.edit', $member->id) }}"
                                                class="btn btn-warning btn-sm">
                                                Edit
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm" data-id="{{ $member->id }}"
                                                data-name="{{ $member->name }}"
                                                data-delete-url="{{ route('bitanic.member.destroy', $member->id) }}"
                                                data-redirect-url="{{ route('bitanic.member.index') }}"
                                                onclick="deleteData(this.dataset)">
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label for="" class="fw-bold">Jumlah Komoditas</label>
                                    <p class="card-text">
                                        {{ $member->max_commodities }}
                                    </p>
                                </div>
                                <div class="col-12">
                                    <label for="" class="fw-bold">Harga</label>
                                    <p class="card-text">
                                        Rp&nbsp;{{ number_format($member->fee) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--/ Striped Rows -->
                </div>
                <div class="col-12">
                    <div class="d-flex flex-row gap-3">
                        <a href="{{ route('bitanic.member.subscription', ['member' => $member->id, 'type' => 'current']) }}">
                            <div class="card current-border">
                                <div class="card-body text-center">
                                    <h3 class="card-title">Member Saat ini</h3>
                                    <h4>{{ $member->current_count }}</h4>
                                </div>
                            </div>
                        </a>
                        <a href="{{ route('bitanic.member.subscription', ['member' => $member->id, 'type' => 'canceled']) }}">
                            <div class="card canceled-border">
                                <div class="card-body text-center">
                                    <h3 class="card-title">Member Dibatalkan</h3>
                                    <h4>{{ $member->canceled_count }}</h4>
                                </div>
                            </div>
                        </a>
                        <a href="{{ route('bitanic.member.subscription', ['member' => $member->id, 'type' => 'history']) }}">
                            <div class="card history-border">
                                <div class="card-body text-center">
                                    <h3 class="card-title">Riwayat Member</h3>
                                    <h4>{{ $member->history_count }}</h4>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
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
