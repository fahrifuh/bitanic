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
        </style>
    @endpush
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master / <a
                    href="{{ route('bitanic.lite-device.index') }}">Perangkat Lite</a> /</span>
            {{ $lite_device->full_series }}
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
                        @if ($lite_device->image)
                            <img src="{{ asset($lite_device->image) }}" alt="perangkat-foto" class="d-block"
                                style="width: 100%;" id="uploadedAvatar" />
                        @endif
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="d-flex justify-content-between gap-1">
                            <span id="device-series-status" @class([
                                'me-1',
                                'device-status',
                                'bg-on-status' => $lite_device->status == 1,
                            ])></span>
                            <h3 class="m-0">{{ $lite_device->full_series }}</h3>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('bitanic.lite-device.edit', $lite_device->id) }}"
                                class="btn btn-warning btn-sm">
                                Edit
                            </a>
                            <button type="button" class="btn btn-danger btn-sm" data-id="{{ $lite_device->id }}"
                                data-name="{{ $lite_device->full_series }}"
                                data-delete-url="{{ route('bitanic.lite-device.destroy', $lite_device->id) }}"
                                data-redirect-url="{{ route('bitanic.lite-device.index') }}"
                                onclick="deleteData(this.dataset)">
                                Hapus
                            </button>
                        </div>
                    </div>
                    <hr />
                    <div class="d-flex flex-column align-items-start gap-2 mb-3">
                        <span><strong>Versi</strong>: {{ $lite_device->version }}</span>
                        <span><strong>Tanggal Produksi</strong>:
                            {{ now()->parse($lite_device->production_date)->formatLocalized('%d %B %Y') }}</span>
                        <span><strong>Tanggal Pembelian</strong>:
                            {{ now()->parse($lite_device->purchase_date)->formatLocalized('%d %B %Y') }}</span>
                        <span><strong>Tanggal Aktivasi</strong>:
                            {{ $lite_device->activate_date? now()->parse($lite_device->activate_date)->formatLocalized('%d %B %Y'): '-' }}</span>
                        <span><strong>TDS (min/max)</strong>: {{ $lite_device->min_tds }} ppm /
                            {{ $lite_device->max_tds }} ppm</span>
                        <span><strong>pH (min/max)</strong>: {{ $lite_device->min_ph }} /
                            {{ $lite_device->max_ph }}</span>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <span class="fs-6"><small id="last-timestamp">*
                                    {{ $lite_device->last_updated_telemetri ?? '-' }}</small></span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center flex-wrap gap-2 mb-3">
                        <div class="flex-fill">
                            <div class="card h-100 bg-primary text-white">
                                <div class="card-body text-center">
                                    <h6 class="card-title text-white">TDS</h6>
                                    <span class="fs-4" id="sensor-tds">{{ $lite_device->current_tds ?? '-' }}
                                        ppm</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex-fill">
                            <div class="card h-100 bg-primary text-white">
                                <div class="card-body text-center">
                                    <h6 class="card-title text-white">pH</h6>
                                    <span class="fs-4" id="sensor-ph">{{ $lite_device->current_ph ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex-fill">
                            <div class="card h-100 bg-primary text-white">
                                <div class="card-body text-center">
                                    <h6 class="card-title text-white">Suhu</h6>
                                    <span class="fs-4"
                                        id="sensor-temperature">{{ number_format($lite_device->temperature, 2, '.', ',') }}°C</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex-fill">
                            <div class="card h-100 bg-primary text-white">
                                <div class="card-body text-center">
                                    <h6 class="card-title text-white">Suhu Air</h6>
                                    <span class="fs-4"
                                        id="sensor-water-temperature">{{ number_format($lite_device->water_temperature, 2, '.', ',') }}°C</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex-fill">
                            <div class="card h-100 bg-primary text-white">
                                <div class="card-body text-center">
                                    <h6 class="card-title text-white">Kelembapan</h6>
                                    <span class="fs-4"
                                        id="sensor-humidity">{{ number_format($lite_device->humidity, 2, '.', ',') }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 d-flex justify-content-center">
                            <div class="btn-group" role="group" aria-label="Basic example">
                                <button type="button" @class([
                                    'btn',
                                    'btn-outline-secondary' => $lite_device->mode != 'manual',
                                    'btn-secondary' => $lite_device->mode == 'manual',
                                ])>Manual</button>
                                <button type="button" @class([
                                    'btn',
                                    'btn-outline-secondary' => $lite_device->mode != 'auto',
                                    'btn-secondary' => $lite_device->mode == 'auto',
                                ])>Auto</button>
                                <button type="button" @class([
                                    'btn',
                                    'btn-outline-secondary' => $lite_device->mode != 'jadwal',
                                    'btn-secondary' => $lite_device->mode == 'jadwal',
                                ])>Jadwal</button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 d-flex flex-column gap-2" id="devices-box">
                        @foreach ($lite_device->pumps as $pump)
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex justify-content-between gap-1">
                                    <span id="status-pump-{{ $pump->number }}" @class([
                                        'me-1',
                                        'device-status',
                                        'bg-on-status' => $pump->status == 1,
                                        'bg-disabled-status' => $pump->is_active == 0,
                                    ])></span>
                                    <span class="me-2">Pompa {{ $pump->name ?? $pump->number }}</span>
                                    <a href="{{ route('bitanic.lite-device.lite-device-pump.edit', ['lite_device' => $lite_device->id, 'lite_device_pump' => $pump->id]) }}"
                                        class="btn btn-sm btn-icon btn-warning" title="Edit Selenoid">
                                        <i class="bx bx-edit-alt"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-icon btn-danger btn-pompa-delete"
                                        data-id="{{ $pump->id }}" data-name="Pompa {{ $pump->number }}"
                                        data-delete-url="{{ route('bitanic.lite-device.lite-device-pump.destroy', ['lite_device' => $lite_device->id, 'lite_device_pump' => $pump->id]) }}"
                                        data-redirect-url="{{ route('bitanic.lite-device.show', $lite_device->id) }}"
                                        onclick="deleteData(this.dataset)"><i class="bx bx-trash event-none"></i>
                                    </button>
                                </div>
                                <div>
                                    <button type="button" data-motor="1" data-type="penyiraman"
                                        data-pe="{{ $pump->number }}" data-id="{{ $lite_device->id }}"
                                        data-status="on" class="btn btn-sm btn-pe-status btn-secondary">
                                        ON
                                    </button>
                                    <button type="button" data-motor="1" data-type="penyiraman"
                                        data-pe="{{ $pump->number }}" data-id="{{ $lite_device->id }}"
                                        data-status="off" class="btn btn-sm btn-pe-status btn-secondary">
                                        OFF
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('bitanic.lite-device.lite-device-pump.create', $lite_device->id) }}"
                            class="btn btn-sm btn-primary d-block">Tambah Pompa</a>
                    </div>
                </div>
            </div>
            <!--/ Striped Rows -->
        </div>
        @if ($lite_device->schedule)
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Penjadwalan Berjalan</h3>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="d-flex flex-column align-items-start gap-1">
                                    <span><strong>Tanggal Dibuat</strong>:
                                        {{ $lite_device->schedule->created_at }}</span>
                                    <span><strong>Jenis Tanaman</strong>:
                                        {{ $lite_device->schedule->crop_name ?? 'null' }}</span>
                                    <span><strong>Jumlah Pekan</strong>: {{ $lite_device->schedule->weeks }}</span>
                                    <span><strong>Tanggal Selesai</strong>:
                                        {{ $lite_device->schedule->end_datetime }}</span>
                                </div>
                            </div>
                            <div class="col-12 px-2">
                                <div class="my-3">
                                    <h5>Penyiraman</h5>
                                    <span><strong>Hari Penyiraman</strong>:
                                        {{ collect($lite_device->schedule->days)->map(fn($item, $key) => ucfirst($item))->join(', ') }}</span>
                                </div>
                                <div class="row pb-1">
                                    @foreach ($lite_device->schedule->setontimes as $setontime)
                                        <div class="col-12 my-1">
                                            <a class="btn btn-primary d-block" data-bs-toggle="collapse"
                                                href="#kelolaWaktuPenyiraman{{ $loop->iteration }}" role="button"
                                                aria-expanded="false"
                                                aria-controls="kelolaWaktuPenyiraman{{ $loop->iteration }}">Kelola
                                                Waktu {{ $loop->iteration }} {{ $setontime['time'] }}</a>
                                            <div class="collapse multi-collapse mt-2"
                                                id="kelolaWaktuPenyiraman{{ $loop->iteration }}">
                                                <div class="flex-column align-items-start p-3 border">
                                                    @php
                                                        $totalDuration = now()
                                                            ->parse($setontime['time'])
                                                            ->addMinutes($setontime['duration']['minute'])
                                                            ->addSeconds($setontime['duration']['seconds']);
                                                    @endphp
                                                    <p>
                                                        <strong>Waktu Mulai</strong>: {{ $setontime['time'] }}:00 <br />
                                                        <strong>Durasi</strong>: {{ $setontime['duration']['minute'] }}
                                                        Menit {{ $setontime['duration']['seconds'] }} Detik <br />
                                                        <strong>Perkiraan Selesai</strong>:
                                                        {{ $totalDuration->format('H:i:s') }} <br />
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
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

            Echo.channel('lites.{{ $lite_device->id }}')
                .listen('LiteEvent', (e) => {
                    updateDataLiteSensors(e.data)

                    for (const pump of e.data.pumps_status) {
                        changeStatus(pump.status, document.querySelector('#status-pump-' + pump.number))
                    }
                    // for (const selenoid of e.selenoid) {
                    //     changeStatus(selenoid.status, document.querySelector("#status-sv-" + selenoid.id))
                    //     changeStatus(selenoid.status == 1 ? e.air : 0, document.querySelector("#status-penyiraman-" + selenoid.id))
                    //     changeStatus(selenoid.status == 1 ? e.pemupukan : 0, document.querySelector("#status-pemupukan-" + selenoid.id))
                    // }
                })

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

            })
        </script>
    @endpush
</x-app-layout>
