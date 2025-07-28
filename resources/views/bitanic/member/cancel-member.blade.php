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

            .bitanic-form-check:checked {
                background-color: #3e8f55 !important;
                border-color: #3e8f55 !important;
                box-shadow: 0 2px 4px 0 rgba(105, 255, 128, 0.4);
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
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Bitanic+ / <a href="{{ route('bitanic.member.current') }}">Kelola Member</a> / <a href="{{ route('bitanic.member.chose') }}">Pilih Member</a> /</span> Pembatalan</h4>
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
                    <div class="card">
                        <div class="card-body">
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
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('bitanic.member.cancel-update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="d-flex flex-column gap-3">
                                    <!-- Validation Errors -->
                                    <x-auth-validation-errors class="mb-4" :errors="$errors" />
                                    <div class="p-3 rounded text-dark text-center d-flex flex-column gap-3" id="payment-step" style="background-color: #dadada6c">
                                        <div>
                                            <span>Dengan melanjutkan, anda akan membatalkan member yang sudah anda beli.</span>
                                        </div>

                                        <div>
                                            <div class="form-check text-start mt-3">
                                                <input class="form-check-input" type="checkbox" name="is_accepted" value="1" id="defaultCheck1">
                                                <label class="form-check-label" for="defaultCheck1"> Saya mengerti </label>
                                            </div>
                                            <button type="submit" class="btn btn-success text-dark rounded-pill w-100">Lanjutkan Pembatalan</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
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
            const bankClicked = (e) => {
                document.querySelector('#payment-step').classList.remove('d-none')
            }
            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

            })
        </script>
    @endpush
</x-app-layout>
