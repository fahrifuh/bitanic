<x-app-layout>

    @push('styles')
        {{-- Cluster --}}
        <link rel="stylesheet" href="{{ asset('css/MarkerCluster.css') }}">
        <link rel="stylesheet" href="{{ asset('css/MarkerCluster.Default.css') }}">
        <link rel="stylesheet" href="{{ asset('leaflet/leaflet.css') }}">
        <link rel="stylesheet" href="{{ asset('css/extend.css') }}">
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <style>
            #myMap {
                height: 350px;
            }

            .leaflet-legend {
                background-color: #f5f5f9;
                border-radius: 10%;
                padding: 10px;
                color: #3e8f55;
                box-shadow: 4px 3px 5px 5px #8d8989a8;
            }

            #img-land {
                height: 100%;
                object-fit: cover;
            }

            .preview-image {
                width: 100%;
                /* Adjust the width as desired */
                object-fit: cover;
                aspect-ratio: 16/9;
            }
        </style>
    @endpush
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master @if (Auth::user()->role == 'admin')
                    / <a href="{{ route('bitanic.farmer.index') }}">Data Pengguna Bitanic Pro</a>
                    / <a href="{{ route('bitanic.farmer.show', $farmer->user_id) }}">{{ $farmer->full_name }}</a>
                @else
                    / {{ $farmer->full_name }}
                @endif /
                <a href="{{ route('bitanic.land.index', $farmer->id) }}">Data Lahan</a> /
                <a
                    href="{{ route('bitanic.land.show', ['farmer' => $farmer->id, 'land' => $land->id]) }}">{{ $land->name }}</a>
                /
                <a href="{{ route('bitanic.garden.index', ['farmer' => $farmer->id, 'land' => $land->id]) }}">Data
                    Kebun</a> /
            </span>
            {{ $garden->name }}</h4>
    </x-slot>
    {{-- End Header --}}

    @if (session()->has('success'))
        <x-alert-message class="alert-success">{{ session()->get('success') }}</x-alert-message>
    @endif
    @if (session()->has('failed'))
        <x-alert-message class="alert-danger">{{ session()->get('failed') }}</x-alert-message>
    @endif

    <div class="row g-3 justify-content-center">
        <div class="col-12 col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-12">
                            <div class="text-center">
                                <img src="{{ asset($garden->picture ?? 'bitanic-landing/default-profile.png') }}"
                                    alt="user-avatar" class="preview-image"
                                    id="uploadedAvatar" />
                                <h5 class="card-title mt-3">{{ $garden->name }}</h5>
                                <a href="{{ route('bitanic.garden.edit', ['farmer' => $farmer->id, 'land' => $land->id, 'garden' => $garden->id]) }}"
                                    title="Edit Kebun" class="btn btn-sm btn-icon btn-warning"><i
                                        class="bx bx-edit-alt"></i></a>
                                <button type="button"
                                    onclick="handleDeleteRows({{ $garden->id }}, '{{ $garden->name }}')"
                                    title="Hapus Kebun"
                                    class="btn btn-sm btn-icon btn-outline-danger"><i
                                        class="bx bx-trash"></i></button>
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="" class="fw-bold">Nama</label>
                            <p class="card-text">{{ $garden->name }}</p>
                        </div>
                        <div class="col-12">
                            <label for="" class="fw-bold">Status Kebun</label>
                            <br>
                            @switch($garden->harvest_status)
                                @case(1)
                                    <span class="badge bg-label-info">Sedang Menanam</span>
                                @break

                                @case(2)
                                    <span class="badge bg-label-warning">Masa Pemeliharaan</span>
                                @break

                                @case(3)
                                    <span class="badge bg-label-primary">Masa Panen</span>
                                @break

                                @default
                                    <span class="badge bg-label-secondary">-</span>
                                @break
                            @endswitch
                            <button class="btn btn-sm btn-icon btn-warning my-1"
                                data-bs-toggle="modal" data-bs-target="#modalEditStatusGarden"
                                title="Klik untuk edit status kebun"
                                onclick="editGardenStatus({{ $garden->id }}, '{{ $garden->harvest_status }}')"><i
                                    class="bx bx-edit-alt"></i>
                            </button>
                            @if ($garden->harvest_status == 3 && $garden->currentCommodity)
                                <a class="btn btn-sm btn-icon btn-success my-1"
                                    title="Klik jika panen sudah selesai"
                                    href="{{ route('bitanic.commodity.edit-yield',[
                                        'farmer' => $farmer->id,
                                        'garden' => $garden->id,
                                        'land' => $land->id,
                                        'commodity' => $garden->currentCommodity->id,
                                    ]) }}"><i
                                        class="bx bx-check"></i>
                                </a>
                            @endif
                        </div>
                        <div class="col-12">
                            <label for="" class="fw-bold">Luas</label>
                            <p class="card-text" id="text-view-area">{{ $garden->area }}&nbsp;m²
                            </p>
                        </div>
                        <div class="col-12">
                            <label for="" class="fw-bold">Tanggal Dibuat</label>
                            <p class="card-text">{{ $garden->date_created }}</p>
                        </div>
                        <div class="col-12">
                            <label for="" class="fw-bold">Komoditi</label>
                            @if ($garden->currentCommodity)
                                <p class="card-text">
                                    {{ $garden->currentCommodity->crop->crop_name ?? '-' }}</p>
                            @else
                                <br>
                                <a href="{{ route('bitanic.commodity.create', [
                                    'farmer' => $farmer->id,
                                    'garden' => $garden->id,
                                    'land' => $land->id
                                ]) }}" class="btn btn-sm btn-primary">Tambah Komoditi</a>
                            @endif
                        </div>
                        <div class="col-12">
                            <label for="" class="fw-bold">Perangkat</label>
                            <p class="card-text">{{ $garden->device?->device_series ?? '-' }}
                            </p>
                        </div>
                        <div class="col-12">
                            <label for="" class="fw-bold">Tipe</label>
                            <p class="card-text">{{ $garden->gardes_type }} /
                                {{ $garden->is_indoor ? 'Indoor' : 'Outdoor' }}</p>
                        </div>
                        <div class="col-12">
                            <label for="" class="fw-bold">Kategori</label>
                            <p class="card-text">{{ $garden->category }}</p>
                        </div>
                        @if (in_array($garden->gardes_type, ['hidroponik', 'aquaponik']))
                            <div class="col-12">
                                <label for="" class="fw-bold">Jumlah Pipa</label>
                                <p class="card-text">{{ $garden->levels }}</p>
                            </div>
                            <div class="col-12">
                                <label for="" class="fw-bold">Jumlah Lubang Per Pipa</label>
                                <p class="card-text">{{ $garden->holes }}</p>
                            </div>
                            <div class="col-12">
                                <label for="" class="fw-bold">Total Pod (Pipa x Lubang)</label>
                                <p class="card-text">
                                    {{ $garden->levels * $garden->holes }}</p>
                            </div>
                            @if ($garden->gardes_type == 'aquaponik')
                                <div class="col-12">
                                    <label for="" class="fw-bold">Panjang Kolam</label>
                                    <p class="card-text">{{ $garden->length }}</p>
                                </div>
                                <div class="col-12">
                                    <label for="" class="fw-bold">Lebar Kolam</label>
                                    <p class="card-text">{{ $garden->width }}</p>
                                </div>
                                <div class="col-12">
                                    <label for="" class="fw-bold">Tinggi Kolam</label>
                                    <p class="card-text">{{ $garden->height }}</p>
                                </div>
                                <div class="col-12">
                                    <label for="" class="fw-bold">Volume</label>
                                    <p class="card-text">{{ $garden->length * $garden->width * $garden->height }}&nbsp;m3</p>
                                </div>
                                <div class="col-12">
                                    <label for="" class="fw-bold">Jenis Ikan</label>
                                    <p class="card-text">{{ $garden->fish_type }}</p>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-8">
            <div class="row g-3">
                <div class="col-12">
                    <!-- Striped Rows -->
                    <div class="card">
                        <div class="card-body">
                            <div id="myMap"></div>
                        </div>
                    </div>
                    <!--/ Striped Rows -->
                </div>
                @if ($rsc_garden)
                    <div class="col-12">
                        <div class="accordion-item card">
                            <h2 class="accordion-header text-body d-flex justify-content-between" id="accordionIconOne">
                                <button
                                    type="button"
                                    class="accordion-button collapsed fs-5"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#accordionIcon-1"
                                    aria-controls="accordionIcon-1"
                                >
                                    Data RSC Terbaru
                                </button>
                            </h2>

                            <div id="accordionIcon-1" class="accordion-collapse collapse" data-bs-parent="#accordionIcon">
                                <div class="accordion-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="float-start">
                                                <h5 class="card-title">Sampel data yang diambil di lahan {{ $rsc_garden->created_at }}</h5>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <h6>Rata-rata N: {{ number_format($rsc_garden->avg_n, 2) ?? '-' }} mg/kg |
                                                Rata-rata P: {{ number_format($rsc_garden->avg_p, 2) ?? '-' }} mg/kg |
                                                Rata-rata K: {{ number_format($rsc_garden->avg_k, 2) ?? '-' }} mg/kg</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive text-wrap">
                                    <table class="table table-striped" id="table-telemetri">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>N</th>
                                                <th>P</th>
                                                <th>K</th>
                                                <th>EC</th>
                                                <th>pH</th>
                                                <th>Suhu Sekitar</th>
                                                <th>Kelembapan Sekitar</th>
                                                <th>Suhu Tanah</th>
                                                <th>Kelembapan Tanah</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0">
                                            @forelse ($rsc_garden->rscGardenTelemetries as $rscGardenTelemetry)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $rscGardenTelemetry->samples->n }} mg/kg</td>
                                                    <td>{{ $rscGardenTelemetry->samples->p }} mg/kg</td>
                                                    <td>{{ $rscGardenTelemetry->samples->k }} mg/kg</td>
                                                    <td>{{ $rscGardenTelemetry->samples->ec ?? '-' }}&nbsp;uS/cm</td>
                                                    <td>{{ optional($rscGardenTelemetry->samples)->ph ?? '-' }}</td>
                                                    <td>{{ $rscGardenTelemetry->samples->ambient_temperature ?? '-' }}°C</td>
                                                    <td>{{ $rscGardenTelemetry->samples->ambient_humidity ?? '-' }}%</td>
                                                    <td>{{ $rscGardenTelemetry->samples->soil_temperature ?? '-' }}°C</td>
                                                    <td>{{ $rscGardenTelemetry->samples->soil_moisture ?? '-' }}%</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="9" class="text-center">Data tidak ada</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col-12">
                    <div class="row g-3">
                        <div class="col-12 col-md">
                            <a href="{{ route('bitanic.commodity.history', [
                                'farmer' => $farmer->id,
                                'land' => $land->id,
                                'garden' => $garden->id
                            ]) }}">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title m-0">
                                            Riwayat Komoditi
                                        </h5>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-12 col-md">
                            <a href="{{ route('bitanic.rsc-garden.history', [
                                'farmer' => $farmer->id,
                                'land' => $land->id,
                                'garden' => $garden->id
                            ]) }}">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title m-0">
                                            Riwayat RSC
                                        </h5>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                @if ($garden->device)
                    <div class="col-12 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex flex-column align-items-center gap-4">
                                    @if ($garden->device->picture)
                                    <img src="{{ asset($garden->device->picture) }}" alt="perangkat-foto" class="d-block" style="width: 100%;" id="uploadedAvatar" />
                                    @endif
                                    <h3>Perangkat {{ $garden->device->device_series }}</h3>
                                </div>
                                <hr />
                                <div class="d-flex flex-column align-items-start gap-2 mb-3">
                                    <div>
                                        <label for="" class="fw-bold">Versi</label>
                                        <p class="mb-0">{{ $garden->device->version }}</p>
                                    </div>
                                    <div>
                                        <label for="" class="fw-bold">Tipe</label>
                                        <p class="mb-0">{{ $garden->device->type }}</p>
                                    </div>
                                    @if ($garden->device->type == 3)
                                        <div>
                                            <label for="" class="fw-bold">Delay (Detik)</label>
                                            <p class="mb-0">{{ $garden->device->delay }}</p>
                                        </div>
                                        <div>
                                            <label for="" class="fw-bold">Debit (Liter)</label>
                                            <p class="mb-0">{{ $garden->device?->toren_pemupukan->debit ?? '-' }}</p>
                                        </div>
                                    @endif
                                </div>
                                @if ($garden->device->type == 3)
                                    <div class="mb-3">
                                        <a href="{{ route('bitanic.v3-device.telemetri-selenoids', $garden->device->id) }}" class="btn btn-sm btn-primary d-block">Detail Telemetri Status SV</a>
                                    </div>
                                    <div class="mt-3 d-flex flex-column" id="devices-box">
                                        @foreach ($garden->device->selenoids as $selenoid)
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex justify-content-between">
                                                    <span id="status-sv-{{ $selenoid->selenoid_id }}" @class([ 'me-1' , 'device-status' , 'bg-on-status'=> $selenoid->selenoid_status == 1,
                                                        ])
                                                        ></span>
                                                    Lahan {{ $selenoid->land->name }} (SV{{ $selenoid->selenoid_id }})
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center ms-3 mt-1">
                                                <div class="d-flex justify-content-between">
                                                    <span id="status-penyiraman-{{ $selenoid->selenoid_id }}" @class([ 'me-1' , 'device-status' , 'bg-on-status'=> $selenoid->selenoid_status == 1 && $garden->device->status_penyiraman == 1,
                                                        ])
                                                        ></span>
                                                    Penyiraman
                                                </div>
                                                <div>
                                                    <button type="button" data-motor="1" data-type="penyiraman" data-pe="{{ $selenoid->selenoid_id }}"
                                                        data-id="{{ $garden->device->id }}" data-status="on" class="btn btn-sm btn-pe-status btn-secondary">
                                                        ON
                                                    </button>
                                                    <button type="button" data-motor="1" data-type="penyiraman" data-pe="{{ $selenoid->selenoid_id }}"
                                                        data-id="{{ $garden->device->id }}" data-status="off" class="btn btn-sm btn-pe-status btn-secondary">
                                                        OFF
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center ms-3 mt-1 mb-3">
                                                <div class="d-flex justify-content-between">
                                                    <span id="status-pemupukan-{{ $selenoid->selenoid_id }}" @class([ 'me-1' , 'device-status' , 'bg-on-status'=> $selenoid->selenoid_status == 1 && $garden->device->status_pemupukan == 1,
                                                        ])
                                                        ></span>
                                                    Pemupukan
                                                </div>
                                                <div>
                                                    <button type="button" data-motor="1" data-type="pemupukan" data-pe="{{ $selenoid->selenoid_id }}"
                                                        data-id="{{ $garden->device->id }}" data-status="on" class="btn btn-sm btn-pe-status btn-secondary">
                                                        ON
                                                    </button>
                                                    <button type="button" data-motor="1" data-type="pemupukan" data-pe="{{ $selenoid->selenoid_id }}"
                                                        data-id="{{ $garden->device->id }}" data-status="off" class="btn btn-sm btn-pe-status btn-secondary">
                                                        OFF
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @include('bitanic.garden._modal-edit-status-garden')
    @include('bitanic.garden._modal-finish-harvest')

    @push('scripts')
        <script src="{{ asset('leaflet/leaflet.js') }}"></script>
        <script src="{{ asset('theme/js/ui-popover.js') }}"></script>
        <script src="{{ asset('js/extend.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <!-- <script src="{{ asset('js/extra.js') }}"></script> -->
        <script>
            const modalEditStatusGarden = new bootstrap.Modal(document.getElementById("modalEditStatusGarden"), {});
            const modalFinishHarvest = new bootstrap.Modal(document.getElementById("modalFinishHarvest"), {});

            let latlngs = [];
            let gardensMarker = [];
            let gardensPolygon = [];
            let marker, polygon;
            let locMarker, locPoly
            const days = ['minggu', 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];

            let stateData = {
                lat: -6.869080223722067,
                lng: 107.72491693496704,
                polygon: JSON.parse("{{ json_encode($garden->polygon) }}"),
                area: "{{ $garden->area }}",
                color: "#{{ $garden->color }}"
            };

            const randomNumber = (min, max) => {
                return Math.floor(Math.random() * (max - min) + min)
            }

            function getColor(d) {
                return d > 1000 ? '#800026' :
                    d > 500 ? '#BD0026' :
                    d > 200 ? '#E31A1C' :
                    d > 100 ? '#FC4E2A' :
                    d > 50 ? '#FD8D3C' :
                    d > 20 ? '#FEB24C' :
                    d > 10 ? '#FED976' :
                    '#FFEDA0';
            }

            // Layer MAP
            let googleStreets = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                maxZoom: 20,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
            });
            let googleStreetsSecond = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                maxZoom: 20,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
            });
            let googleStreetsThird = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                maxZoom: 20,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
            });
            // Layer MAP
            const map = L.map('myMap', {
                preferCanvas: true,
                layers: [googleStreets],
                zoomControl: true
            }).setView([-6.869080223722067, 107.72491693496704], 12);

            function errorMessage(error) {
                if ("messages" in error) {
                    let errorMessage = ''

                    let element = ``
                    for (const key in error.messages) {
                        if (Object.hasOwnProperty.call(error.messages, key)) {
                            error.messages[key].forEach(message => {
                                element += `<li>${message}</li>`;
                            });
                        }
                    }

                    errorMessage = `<ul>${element}</ul>`

                    Swal.fire({
                        html: errorMessage,
                        icon: "error",
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                }
            }

            function textViewLand({
                area,
                latitude,
                longitude,
                altitude,
                image,
                address
            }) {
                document.querySelector('#img-land').src = "{{ asset('') }}" + image
                document.querySelector('#text-view-area').textContent = area + " m²"
                document.querySelector('#text-view-latlng').textContent = `${latitude}, ${longitude}`
                document.querySelector('#text-view-altitude').textContent = altitude + " mdpl"
                document.querySelector('#text-view-address').textContent = address
            }

            const rscSamplesPosition = rcs_garden_telemetries => {
                rcs_garden_telemetries.forEach(rcs_garden_telemetry => {
                    L.circle(
                            [rcs_garden_telemetry.latitude, rcs_garden_telemetry.longitude],
                            {
                                radius: 1,
                                color: config.colors.danger
                            }
                        )
                        .bindPopup(
                            `<div class="d-flex gap-2 mb-2">
                                <span class="badge bg-info flex-fill">N: ${rcs_garden_telemetry.samples.n} mg/kg</span><span class="badge bg-warning flex-fill">P: ${rcs_garden_telemetry.samples.p} mg/kg</span><span class="badge bg-danger flex-fill">K: ${rcs_garden_telemetry.samples.k} mg/kg</span>
                            </div>`,
                            {
                                closeButton: true
                            }
                        )
                        .on('mouseover', function(e) {
                            this.openPopup();
                        })
                        .on('mouseout', function(e) {
                            this.closePopup();
                        })
                        .addTo(map)
                });
            }

            const getLand = async (id) => {
                if (!id) {
                    return 0
                }

                try {
                    const settings = {
                        method: 'GET',
                        headers: {
                            'x-csrf-token': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                    }

                    let url = "{{ route('bitanic.rsc-garden.get-rsc-garden', ['farmer' => $farmer->id, 'land' => $land->id, 'garden' => $garden->id, 'rscGarden' => 'ID']) }}"

                    const [data, error] = await yourRequest(url.replace('ID', id), settings)

                    if (error) {
                        errorMessage(error)

                        return false
                    }

                    // if (locPoly) {
                    //     locPoly.remove()
                    // }

                    // if (locMarker) {
                    //     locMarker.remove()
                    // }

                    const rscGarden = data.rsc_garden

                    const colors = [
                        config.colors.primary,
                        config.colors.info,
                        config.colors.warning,
                        config.colors.danger
                    ]

                    rscSamplesPosition(rscGarden.rsc_garden_telemetries)

                } catch (error) {
                    console.log(error);
                }
            }

            const editGardenStatus = (id, status) => {
                document.querySelector('#status-' + status).checked = true
                document.getElementById('btn-edit-status-garden').dataset['id'] = id
            }

            // btn edit status garden
            const btnEditGardenStatus = document.getElementById('btn-edit-status-garden')
            btnEditGardenStatus.addEventListener('click', async (e) => {
                e.preventDefault()

                showSpinner()

                // Simulate form submission. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                let url, formSubmited;
                const formData = new FormData();

                formData.append("status", document.querySelector('.data-input-status-kebun:checked').value)
                formData.append("_method", 'PUT')

                url = "{{ route('bitanic.garden.change-status', ['farmer' => $farmer, 'land' => $land->id, 'garden' => 'ID']) }}"
                    .replace('ID', e.target.dataset['id']);

                const settings = {
                    method: 'POST',
                    headers: {
                        'x-csrf-token': '{{ csrf_token() }}'
                    },
                    body: formData
                }

                const [data, error] = await yourRequest(url, settings)

                modalEditStatusGarden.toggle()

                if (error) {
                    deleteSpinner()

                    if ("messages" in error) {
                        let errorMessage = ''


                        let element = ``
                        for (const key in error.messages) {
                            if (Object.hasOwnProperty.call(error.messages, key)) {
                                error.messages[key].forEach(message => {
                                    element += `<li>${message}</li>`;
                                });
                            }
                        }

                        errorMessage = `<ul>${element}</ul>`

                        Swal.fire({
                            html: errorMessage,
                            icon: "error",
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    }

                    return false
                }

                Swal.fire({
                    text: "Kamu berhasil mengubah data " + data.name + "!.",
                    icon: "success"
                })

                window.location.reload();
            })

            const finishHarvesting = async (id) => {
                document.getElementById('btn-store-panen').dataset['garden'] = id
            }

            // btn store panen
            const btnStorePanen = document.getElementById('btn-store-panen')
            btnStorePanen.addEventListener('click', async (e) => {
                e.preventDefault()

                showSpinner()

                // Simulate form submission. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                let url, formSubmited;
                const formData = new FormData();

                formData.append("hasil_panen", document.querySelector('#data-input-hasil-panen').value)
                formData.append("satuan_panen", document.querySelector('#data-input-satuan-panen').value)
                formData.append("catatan", document.querySelector('#data-input-catatan').value)

                url = "{{ route('bitanic.harvest-produce.store', ['farmer' => $farmer, 'garden' => 'GD']) }}"
                    .replace('GD', e.target.dataset['garden']);

                const settings = {
                    method: 'POST',
                    headers: {
                        'x-csrf-token': '{{ csrf_token() }}'
                    },
                    body: formData
                }

                const [data, error] = await yourRequest(url, settings)

                modalFinishHarvest.toggle()

                if (error) {
                    deleteSpinner()

                    if ("messages" in error) {
                        let errorMessage = ''


                        let element = ``
                        for (const key in error.messages) {
                            if (Object.hasOwnProperty.call(error.messages, key)) {
                                error.messages[key].forEach(message => {
                                    element += `<li>${message}</li>`;
                                });
                            }
                        }

                        errorMessage = `<ul>${element}</ul>`

                        Swal.fire({
                            html: errorMessage,
                            icon: "error",
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    }

                    return false
                }

                Swal.fire({
                    text: "Kamu berhasil mengubah data " + data.name + "!.",
                    icon: "success"
                })

                window.location.reload();
            })

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

                getLand("{{ optional($rsc_garden)->id }}")

                const gardenPolygon = L.polygon(stateData.polygon, {
                    color: stateData.color,
                    dashArray: '10, 10',
                    dashOffset: '20',
                })
                .bindPopup(stateData.area + " m²")
                .addTo(map)

                map.fitBounds(gardenPolygon.getBounds());

                map.doubleClickZoom.disable();
            })
        </script>
    @endpush
</x-app-layout>
