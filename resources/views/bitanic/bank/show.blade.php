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
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master / <a
                    href="{{ route('bitanic.bank.index') }}">Bank</a> /</span> {{ $bank->name }}</h4>
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
                        <img src="{{ asset($bank->picture) }}" alt="preview-img"
                            class="preview-image img-thumbnail rounded">
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <h3 class="m-0">{{ $bank->name }}</h3>
                        <div class="d-flex gap-1">
                            <a href="{{ route('bitanic.bank.edit', $bank->id) }}"
                                class="btn btn-warning btn-sm">
                                Edit
                            </a>
                            <button type="button" class="btn btn-danger btn-sm" data-id="{{ $bank->id }}"
                                data-name="{{ $bank->name }}"
                                data-delete-url="{{ route('bitanic.bank.destroy', $bank->id) }}"
                                data-redirect-url="{{ route('bitanic.bank.index') }}"
                                onclick="deleteData(this.dataset)">
                                Hapus
                            </button>
                        </div>
                    </div>
                    <hr />
                    <div class="d-flex flex-column align-items-start gap-2 mb-3">
                        <span><strong>Kode</strong>: <br>{{ $bank->code }}</span>
                        <span><strong>Fees</strong>: <br>
                            @foreach ($bank->fees as $fee)
                                @switch($fee['type'])
                                    @case(0)
                                        Rp&nbsp;{{ number_format($fee['fee'], 0, '.', ',') }}
                                        @break
                                    @case(1)
                                        {{ number_format($fee['fee'], 1, '.', ',') }}%
                                        @break
                                    @default
                                @endswitch
                                @if (!$loop->last)
                                    +
                                @endif
                            @endforeach
                        </span>
                        <span><strong>Deskripsi</strong>: <br>{{ $bank->description }}</span>
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
