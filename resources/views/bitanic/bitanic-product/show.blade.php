<x-app-layout>
    @push('styles')
        <style>
            #pest-image {
                height: 100%;
                object-fit: cover;
            }

            .preview-image {
                width: 100%;
                /* Adjust the width as desired */
                object-fit: cover;
                aspect-ratio: 4/3;
                border: 1px solid #9f999975;
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

            @media (max-width: 600px) {
                .preview-image {
                    width: calc(100% - 10px);
                }
            }

            .description {
                background-color: #8d8d8d36;
                padding: 10px;
            }
        </style>
    @endpush
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master / <a
                    href="{{ route('bitanic.bitanic-product.index') }}">Produk Bitanic</a> /</span>
            {{ $bitanicProduct->name }}
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
        <div class="col-12 col-md-6">
            <!-- Striped Rows -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-column align-items-center">
                        <img src="{{ asset($bitanicProduct->picture) }}" alt="perangkat-foto" class="d-block"
                            style="width: 100%;" id="uploadedAvatar" />
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="d-flex justify-content-between gap-1">
                            <h3 class="m-0">{{ $bitanicProduct->name }}</h3>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('bitanic.bitanic-product.edit', $bitanicProduct->id) }}"
                                class="btn btn-warning btn-sm">
                                Edit
                            </a>
                            <button type="button" class="btn btn-danger btn-sm" data-id="{{ $bitanicProduct->id }}"
                                data-name="{{ $bitanicProduct->name }}"
                                data-delete-url="{{ route('bitanic.bitanic-product.destroy', $bitanicProduct->id) }}"
                                data-redirect-url="{{ route('bitanic.bitanic-product.index') }}"
                                onclick="deleteData(this.dataset)">
                                Hapus
                            </button>
                        </div>
                    </div>
                    <hr />
                    <div class="d-flex justify-content-between adivgn-items-center mb-3">
                        <span>Harga: Rp&nbsp;{{ number_format($bitanicProduct->price) }}</span>
                        <span>
                            Kategori:
                            @switch($bitanicProduct->category)
                                @case('controller')
                                    Kontroller Bitanic Pro
                                    @break
                                @case('tongkat')
                                    Tongkat/RSC
                                    @break
                            @endswitch
                        </span>
                        <span>Tipe: {{ $bitanicProduct->type }}</span>
                    </div>

                    <div class="description">
                        <h5>Deskripsi</h5>
                        <p class="card-text">
                           {!! $bitanicProduct->description !!}
                        </p>
                    </div>
                </div>
            </div>
            <!--/ Striped Rows -->
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/app.js') }}"></script>
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

            const changeStatus = (pumpStatus, pumpElement) => {
                if (!pumpElement) {
                    return false
                }
                switch (pumpStatus) {
                    case 0:
                        pumpElement.classList.remove("bg-on-status")
                        break;
                    case 1:
                        pumpElement.classList.add("bg-on-status")
                        break;

                    default:
                        break;
                }
            }

            function timestampToDate(timestamp) {
                const date = new Date(timestamp);
                const year = date.getFullYear();
                const month = date.getMonth() + 1; // Months are zero-based
                const day = date.getDate();
                const hours = date.getHours();
                const minutes = date.getMinutes();
                const seconds = date.getSeconds();

                // Format the date as desired (e.g., YYYY-MM-DD HH:MM:SS)
                const formattedDate = `* ${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
                return formattedDate;
            }

            const updateDataLiteSensors = ({
                tds,
                ph,
                temperature,
                water_temperature,
                humidity,
                last_updated_telemetri
            }) => {
                document.querySelector('#sensor-tds').textContent = tds + ' ppm'
                document.querySelector('#sensor-ph').textContent = ph
                document.querySelector('#sensor-temperature').textContent = parseFloat(temperature).toFixed(2) + '°C'
                document.querySelector('#sensor-water-temperature').textContent = parseFloat(water_temperature).toFixed(2) +
                    '°C'
                document.querySelector('#sensor-humidity').textContent = parseFloat(humidity).toFixed(2) + '%'
                document.querySelector('#last-timestamp').textContent = timestampToDate(last_updated_telemetri)
            }

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

            })
        </script>
    @endpush
</x-app-layout>
