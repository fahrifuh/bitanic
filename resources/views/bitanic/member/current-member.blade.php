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
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Bitanic+ /</span> Kelola Member</h4>
        </h4>
    </x-slot>
    {{-- End Header --}}

    <div class="row d-flex justify-content-center">
        <div class="col-12 col-md-4">
            @if (session()->has('success'))
                <x-alert-message class="alert-success">{{ session()->get('success') }}</x-alert-message>
            @endif
            @if (session()->has('failed'))
                <x-alert-message class="alert-danger">{{ session()->get('failed') }}</x-alert-message>
            @endif

            <!-- Striped Rows -->
            <div class="row gap-3">
                <div class="col-12">
                    @if ($subscription)
                        <div class="card">
                            <div class="card-body">
                                <!-- Validation Errors -->
                                <x-auth-validation-errors class="mb-4" :errors="$errors" />
                                <div class="row">
                                    <div class="col-12">
                                        <h3 class="card-title color-bitanic">{{ $subscription->member->name }}</h3>
                                        <h6 class="card-subtitle mb-2 text-muted">{{ $subscription->member->max_commodities }} Komoditas</h6>
                                    </div>
                                    <div class="col-12">
                                        <hr/>
                                        <div class="d-flex justify-content-between align-items-center align-content-center">
                                            <div>
                                                Member berlaku sampai {{ carbon_format_id_flex(now()->parse($subscription->expired)->format('d-m-Y'), '-', ' ') }}
                                            </div>
                                            <div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="card">
                            <div class="card-body">
                                <!-- Validation Errors -->
                                <x-auth-validation-errors class="mb-4" :errors="$errors" />
                                <div class="row">
                                    <div class="col-12">
                                        <h3 class="card-title color-bitanic">Free</h3>
                                        <h6 class="card-subtitle mb-2 text-muted">20 Komoditas</h6>
                                    </div>
                                    <div class="col-12">
                                        <hr/>
                                        <div class="d-flex justify-content-between align-items-center align-content-center">
                                            <div>
                                                Gratis
                                            </div>
                                            <div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="col-12">
                    <a href="{{ route('bitanic.member.chose') }}" class="btn btn-success rounded-pill">
                        @if ($subscription)
                            Ubah member
                        @else
                            Gabung member
                        @endif
                    </a>
                </div>
            </div>
            <!--/ Striped Rows -->
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('theme/js/ui-popover.js') }}"></script>
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

            })
        </script>
    @endpush
</x-app-layout>
