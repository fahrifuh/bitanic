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

            .rsc-telemetry-table {
                height: 500px;
                overflow-y: scroll;
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
                @endif
                / <a href="{{ route('bitanic.land.index', $farmer->id) }}">Data Lahan</a>
                / <a
                    href="{{ route('bitanic.land.show', ['farmer' => $farmer->id, 'land' => $land->id]) }}">{{ $land->name }}</a>
                / <a href="{{ route('bitanic.garden.index', ['farmer' => $farmer->id, 'land' => $land->id]) }}">Data
                    Kebun</a>
                / <a
                    href="{{ route('bitanic.garden.show', ['farmer' => $farmer->id, 'land' => $land->id, 'garden' => $garden->id]) }}">
                    {{ $garden->name }}</a>
                /
            </span>
            Riwayat RSC
        </h4>
    </x-slot>
    {{-- End Header --}}

    @if (session()->has('success'))
        <x-alert-message class="alert-success">{{ session()->get('success') }}</x-alert-message>
    @endif
    @if (session()->has('failed'))
        <x-alert-message class="alert-danger">{{ session()->get('failed') }}</x-alert-message>
    @endif

    <div class="row g-3 justify-content-center">
        <div class="col-12">
            <!-- Striped Rows -->
            <div class="card">
                <div class="card-body">
                    <div id="myMap"></div>
                </div>
            </div>
            <!--/ Striped Rows -->
        </div>
        <div class="col-12 col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Data Riwayat RSC</h5>
                </div>
                <div class="table-responsive text-wrap ">
                    <table class="table table-striped" id="table-telemetri">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Timestamp</th>
                                <th>Perangkat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($rscGardens as $rscGarden)
                                <tr>
                                    <td>{{ ($rscGardens->currentPage() - 1) * $rscGardens->perPage() + $loop->iteration }}</td>
                                    <td>{{ $rscGarden->created_at }}</td>
                                    <td>{{ $rscGarden->device->device_series }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('bitanic.rsc-garden.export-excel', [
                                                'farmer' => $farmer->id,
                                                'garden' => $garden->id,
                                                'land' => $land->id,
                                                'rscGarden' => $rscGarden->id,
                                            ]) }}" target="_blank" class="btn btn-icon btn-sm btn-success" title="Export Excel">
                                                <i class='bx bxs-file-export'></i>
                                            </a>
                                            <a href="{{ route('bitanic.rsc-garden.export-pdf', [
                                                'farmer' => $farmer->id,
                                                'garden' => $garden->id,
                                                'land' => $land->id,
                                                'rscGarden' => $rscGarden->id,
                                            ]) }}" target="_blank" class="btn btn-icon btn-sm btn-warning" title="Export PDF">
                                                <i class='bx bxs-file-export'></i>
                                            </a>
                                            <button type="button"
                                                class="btn btn-sm btn-icon btn-info"
                                                onclick="getLand({{ $rscGarden->id }}, '{{ $rscGarden->created_at }}')"
                                                title="Telemetri Data">
                                                <i class="bx bx-link-external"></i>
                                            </button>
                                            <button type="button"
                                                class="btn btn-sm btn-icon btn-danger" href="javascript:void(0);"
                                                title="Hapus Data"
                                                onclick="destroyHarvestProduce({{ $rscGarden->id }}), '{{ $rscGarden->id }}'">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Data tidak ada</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <nav aria-label="Page navigation" class="mt-3">
                    <ul class="pagination justify-content-center">
                        {{ $rscGardens->links() }}
                    </ul>
                </nav>
            </div>
        </div>
        <div class="col-12 col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-12">
                            <div class="float-start">
                                <h5 class="card-title">Sampel data yang diambil di lahan <span id="text-created-at"></span></h5>
                            </div>
                        </div>
                        <div class="col-12">
                            <h6>Rata-rata N: <span id="text-avg-n"></span> mg/kg |
                                Rata-rata P: <span id="text-avg-p"></span> mg/kg |
                                Rata-rata K: <span id="text-avg-k"></span> mg/kg</h6>
                        </div>
                    </div>
                </div>
                <div class="table-responsive text-wrap rsc-telemetry-table">
                    <table class="table table-striped" id="table-rsc-telemetries">
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
                            <tr>
                                <td colspan="10" class="text-center">Data tidak ada</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('bitanic.garden._modal-edit-status-garden')

    @push('scripts')
        <script src="{{ asset('leaflet/leaflet.js') }}"></script>
        <script src="{{ asset('theme/js/ui-popover.js') }}"></script>
        <script src="{{ asset('js/extend.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <!-- <script src="{{ asset('js/extra.js') }}"></script> -->
        <script>
            const modalEditStatusGarden = new bootstrap.Modal(document.getElementById("modalEditStatusGarden"), {});

            const destroyHarvestProduce = (id, name) => {
                handleDeleteRows(
                    "{{ route('bitanic.rsc-garden.destroy', ['farmer' => $farmer->id, 'land' => $land->id, 'garden' => $garden->id, 'rscGarden' => 'ID']) }}"
                    .replace('ID', id), "{{ csrf_token() }}",
                    name
                )
            }

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

            let currentLayerGroup = null;

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

            const rscSamplesPosition = (rcs_garden_telemetries, layerGroup) => {
                if (layerGroup) {
                    layerGroup.clearLayers()
                }

                let rscGardenTelemetriesCircles = [],
                    tdRscTelemetries = ``

                rcs_garden_telemetries.forEach((rcs_garden_telemetry, index) => {
                    rscGardenTelemetriesCircles.push(
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
                    )

                    tdRscTelemetries += `<tr>
                        <td>${index+1}</td>
                        <td>${rcs_garden_telemetry.samples.n} mg/kg</td>
                        <td>${rcs_garden_telemetry.samples.p} mg/kg</td>
                        <td>${rcs_garden_telemetry.samples.k} mg/kg</td>
                        <td>${rcs_garden_telemetry.samples.ec} uS/cm</td>
                        <td>${rcs_garden_telemetry.samples?.ph ?? '-'}</td>
                        <td>${rcs_garden_telemetry.samples.ambient_temperature}°C</td>
                        <td>${rcs_garden_telemetry.samples.ambient_humidity}%</td>
                        <td>${rcs_garden_telemetry.samples.soil_temperature}°C</td>
                        <td>${rcs_garden_telemetry.samples.soil_moisture}%</td>
                    </tr>`
                });

                document.querySelector('#table-rsc-telemetries tbody').innerHTML = tdRscTelemetries

                return L.layerGroup(rscGardenTelemetriesCircles)
                    .addTo(map)
            }

            const getLand = async (id, timestamp) => {
                document.querySelector('#text-created-at').textContent = timestamp

                document.querySelector('#table-rsc-telemetries tbody').innerHTML = `<tr>
                                <td colspan="10" class="text-center">Loading</td>
                            </tr>`

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

                    document.querySelector('#text-avg-n').textContent = parseFloat(rscGarden.avg_n).toFixed(2)
                    document.querySelector('#text-avg-p').textContent = parseFloat(rscGarden.avg_p).toFixed(2)
                    document.querySelector('#text-avg-k').textContent = parseFloat(rscGarden.avg_k).toFixed(2)

                    currentLayerGroup = rscSamplesPosition(rscGarden.rsc_garden_telemetries, currentLayerGroup)

                } catch (error) {
                    console.log(error);
                    document.querySelector('#table-rsc-telemetries tbody').innerHTML = `<tr>
                                <td colspan="10" class="text-center">Data tidak ada</td>
                            </tr>`
                }
            }

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

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
