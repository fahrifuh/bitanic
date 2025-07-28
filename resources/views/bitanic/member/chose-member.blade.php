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

            .color-bitanic {
                color: #3e8f55;
            }

            .members-link:hover {
                background-color: #e5f7ed !important;
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
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Bitanic+ / <a href="{{ route('bitanic.member.current') }}">Kelola Member</a> /</span> Pilih Member</h4>
        </h4>
    </x-slot>
    {{-- End Header --}}

    <div class="row d-flex justify-content-center">
        <div class="col-12 col-md-6">
            @if (session()->has('success'))
                <x-alert-message class="alert-success">{{ session()->get('success') }}</x-alert-message>
            @endif
            @if (session()->has('failed'))
                <x-alert-message class="alert-danger">{{ session()->get('failed') }}</x-alert-message>
            @endif

            <!-- Striped Rows -->
            <div class="row gap-3">
                @if ($subscription)
                    <div class="col-12 mb-3">
                        <div class="card">
                            <ul class="list-group list-group-flush border-bottom">
                                <li class="list-group-item">Member saat ini</li>
                            </ul>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <h3 class="card-title color-bitanic">{{ $subscription->member->name }}</h3>
                                        <h6 class="card-subtitle mb-2 text-muted">{{ $subscription->member->max_commodities }} Komoditas</h6>
                                    </div>
                                    <div class="col-12">
                                        <hr/>
                                        <div class="d-flex justify-content-between align-items-center align-content-center">
                                            <div>
                                                <span class="fs-5 fw-bold">Rp&nbsp;{{ number_format($subscription->member->fee) }}</span>
                                                <p class="card-text"><small class="text-muted">per tahun</small></p>
                                            </div>
                                            <div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col-12">
                    <h2 class="m-0">Paket tersedia</h2>
                </div>
                @foreach ($members as $member)
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <h3 class="card-title color-bitanic">{{ $member->name }}</h3>
                                        <h6 class="card-subtitle mb-2 text-muted">{{ $member->max_commodities }} Komoditas</h6>
                                    </div>
                                    <div class="col-12">
                                        <hr/>
                                        <div class="d-flex justify-content-between align-items-center align-content-center">
                                            <div>
                                                <span class="fs-5 fw-bold">Rp&nbsp;{{ number_format($member->fee) }}</span>
                                                <p class="card-text"><small class="text-muted">per tahun</small></p>
                                            </div>
                                            <div>
                                                <a href="{{ route('bitanic.member.purchase', $member->id) }}" class="btn btn-outline-success rounded-pill">Pilih</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                @if ($subscription)
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <h3 class="card-title color-bitanic">Free</h3>
                                        <div class="alert alert-info" role="alert"><i class='bx bx-error-circle'></i>&nbsp;Beralih ke Free akan membatalkan paket Premium kamu.</div>
                                        <h6 class="card-subtitle mb-2 text-muted">20 Komoditas</h6>
                                    </div>
                                    <div class="col-12">
                                        <hr/>
                                        <div class="d-flex justify-content-between align-items-center align-content-center">
                                            <div>
                                            </div>
                                            <div>
                                                <a href="{{ route('bitanic.member.cancel-show') }}" class="btn btn-outline-secondary rounded-pill">Batalkan member</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
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
