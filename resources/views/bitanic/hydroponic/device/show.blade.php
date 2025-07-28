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
                width: 100%;
                /* Adjust the width as desired */
                object-fit: cover;
                aspect-ratio: 1/1;
            }

            .box-status {
                height: 100px;
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
                    <a href="{{ route('bitanic.hydroponic.device.index') }}">Perangkat</a>
                </li>
                <li class="breadcrumb-item active">{{ $hydroponicDevice->series }}</li>
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

    <div class="row d-flex justify-content-center g-2">
        <div class="col-12 col-md-4">
            <!-- Striped Rows -->
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="d-flex flex-column align-items-center gap-4">
                                @if ($hydroponicDevice->picture)
                                    <img src="{{ asset($hydroponicDevice->picture) }}" alt="perangkat-foto"
                                        class="preview-image d-block" id="uploadedAvatar" />
                                @endif
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="card-title mb-0">{{ $hydroponicDevice->series }}</h3>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('bitanic.hydroponic.device.edit', $hydroponicDevice->id) }}"
                                        class="btn btn-warning btn-icon" title="Edit Device">
                                        <i class="bx bx-edit"></i>
                                    </a>
                                    <button type="button" onclick="deletePest(this)"
                                        data-id="{{ $hydroponicDevice->id }}"
                                        data-name="{{ $hydroponicDevice->series }}" class="btn btn-danger btn-icon"
                                        title="Hapus Device">
                                        <i class="bx bx-trash event-none"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex flex-column gap-2">
                                <div><label class="fw-bolder"
                                        for="">Versi:</label><br />{{ $hydroponicDevice->version }}</div>
                                <div><label class="fw-bolder"
                                        for="">User:</label><br />{{ $hydroponicDevice->hydroponicUser?->name ?? '-' }}
                                </div>
                                <div><label class="fw-bolder" for="">Tanggal
                                        Produksi:</label><br />{{ $hydroponicDevice->production_date }}</div>
                                <div><label class="fw-bolder" for="">Tanggal
                                        Pembelian:</label><br />{{ $hydroponicDevice->purchase_date ?? '-' }}</div>
                                <div><label class="fw-bolder" for="">Tanggal
                                        Aktivasi:</label><br />{{ $hydroponicDevice->activation_date ?? '-' }}</div>
                                <div><label class="fw-bolder" for="">Note:</label><br />
                                    <p class="card-text">{{ $hydroponicDevice->note }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Striped Rows -->
        </div>
        <div class="col-12 col-md-8">
            <div class="row g-2">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                                <div>
                                    <h3 class="card-title mb-0">Sensor</h3>
                                </div>
                                <div>
                                    <a href="{{ route('bitanic.hydroponic.device.telemetry.index', $hydroponicDevice->id) }}"
                                        class="btn btn-primary">List Telemetri</a>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap justify-content-center gap-2 fw-bold">
                                <div class="flex-grow-1 text-center bg-info p-4 text-white rounded-3">
                                    <h6 class="mb-0 text-white">Suhu(°C)</h6>
                                    <span
                                        class="fs-1">{{ $hydroponicDevice->latestTelemetry?->sensors->temperature ?? '-' }}</span>
                                </div>
                                <div class="flex-grow-1 text-center bg-info p-4 text-white rounded-3">
                                    <h6 class="mb-0 text-white">Kelembapan(%)</h6>
                                    <span
                                        class="fs-1">{{ $hydroponicDevice->latestTelemetry?->sensors->humidity ?? '-' }}</span>
                                </div>
                                <div class="flex-grow-1 text-center bg-info p-4 text-white rounded-3">
                                    <h6 class="mb-0 text-white">TDS/ppm</h6>
                                    <span class="fs-1">{{ $hydroponicDevice->latestTelemetry?->sensors->tdm ?? '-' }}</span>
                                </div>
                                <div class="flex-grow-1 text-center bg-info p-4 text-white rounded-3">
                                    <h6 class="mb-0 text-white">pH</h6>
                                    <span class="fs-1">{{ $hydroponicDevice->latestTelemetry?->sensors->ph ?? '-' }}</span>
                                </div>
                                <div class="flex-grow-1 text-center bg-info p-4 text-white rounded-3">
                                    <h6 class="mb-0 text-white">Volume Air</h6>
                                    <span
                                        class="fs-1">{{ $hydroponicDevice->latestTelemetry?->sensors->water_volume ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">Status Pompa</h3>
                            <div class="d-flex flex-wrap gap-2 text-body">
                                @foreach ($hydroponicDevice->pumps as $key => $pumpStatus)
                                    <div @class([
                                        'flex-grow-1',
                                        'rounded-3',
                                        'p-3',
                                        'box-status',
                                        'text-center',
                                        'text-white',
                                        'bg-secondary' => $pumpStatus == 0,
                                        'bg-success' => $pumpStatus == 1,
                                        'd-flex',
                                        'justify-content-center',
                                        'align-items-center',
                                        'pump-status',
                                    ]) id="pump-{{ $key }}" data-pump-name="{{ $key }}">
                                        {{ hydroponicPumpLabel($key) }}
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-3 d-flex flex-wrap gap-2">
                                <div class="d-flex flex-wrap align-items-center gap-1">
                                    <div class="bg-success" style="width: 10px;height: 10px;"></div><span>Hidup</span>
                                </div>
                                <div class="d-flex flex-wrap align-items-center gap-1">
                                    <div class="bg-secondary" style="width: 10px;height: 10px;"></div><span>Mati</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">Threshold</h3>
                            <div class="d-flex flex-wrap justify-content-center gap-2 fw-bold">
                                <div class="flex-grow-1 text-center bg-warning p-4 text-white rounded-3">
                                    <h6 class="mb-0 text-white">Pompa Air</h6>
                                    <span
                                        class="fs-1">{{ $hydroponicDevice->thresholds->water[0] ?? '-' }}&nbsp;/&nbsp;{{ $hydroponicDevice->thresholds->water[1] }}</span>
                                    <h6 class="text-white">Min/Max</h6>
                                </div>
                                <div class="flex-grow-1 text-center bg-warning p-4 text-white rounded-3">
                                    <h6 class="mb-0 text-white">Pompa Nutrisi</h6>
                                    <span
                                        class="fs-1">{{ $hydroponicDevice->thresholds->nutrient[0] ?? '-' }}&nbsp;/&nbsp;{{ $hydroponicDevice->thresholds->nutrient[1] }}</span>
                                    <h6 class="text-white">Min/Max</h6>
                                </div>
                                <div class="flex-grow-1 d-flex flex-wrap align-items-center justify-content-center bg-warning p-4 text-white rounded-3">
                                    <div class="text-center">
                                        <h6 class="mb-0 text-white">pH Basa</h6>
                                        <span class="fs-1">{{ $hydroponicDevice->thresholds->ph_basa ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 d-flex flex-wrap align-items-center justify-content-center bg-warning p-4 text-white rounded-3">
                                    <div class="text-center">
                                        <h6 class="mb-0 text-white">pH Asam</h6>
                                        <span class="fs-1">{{ $hydroponicDevice->thresholds->ph_asam ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/app.js') }}"></script>
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
                    "{{ route('bitanic.hydroponic.device.destroy', 'ID') }}".replace('ID',
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

                window.location = "{{ route('bitanic.hydroponic.device.index') }}"
            }

            const updatePumpStatus = ({ water, nutrient, ph_basa, ph_asam, mixer }) => {
                document.querySelector('#pump-water').classList.replace('bg-secondary', 'bg-success')

                for (const pumps of document.querySelectorAll('.pump-status')) {
                    switch (pumps.dataset.pumpName) {
                        case "water":
                            checkStatusForClass(pumps, water)
                            break;
                        case "nutrient":
                            checkStatusForClass(pumps, nutrient)
                            break;
                        case "ph_basa":
                            checkStatusForClass(pumps, ph_basa)
                            break;
                        case "ph_asam":
                            checkStatusForClass(pumps, ph_asam)
                            break;
                        case "mixer":
                            checkStatusForClass(pumps, mixer)
                            break;

                        default:
                            break;
                    }

                }
            }

            function checkStatusForClass(e, status) {
                if (status == 1) {
                    e.classList.replace('bg-secondary', 'bg-success')
                } else {
                    e.classList.replace('bg-success', 'bg-secondary')
                }
            }


            Echo.channel('hydroponic.device.{{ $hydroponicDevice->id }}')
                .listen('HydroponicDeviceEvent', (e) => {
                    console.log(e);
                    updatePumpStatus(e.pumps)
                })

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

                updatePumpStatus({
                    water: 1,
                    nutrient: 0,
                    ph_base: 0,
                    ph_asam: 1,
                    mixer: 1
                })
            })
        </script>
    @endpush
</x-app-layout>
