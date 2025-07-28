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
      height: 250px;
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
  </style>
  @endpush
  {{-- Header --}}
  <x-slot name="header">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master @if(Auth::user()->role == 'admin') / <a href="{{ route('bitanic.farmer.index') }}">Data Petani</a> @endif / {{ $farmer->full_name }} / <a href="{{ route('bitanic.land.index', $farmer->id) }}">Data Lahan</a> / </span> Detail Lahan {{ $land->name }}</h4>
  </x-slot>
  {{-- End Header --}}

    @if (session()->has('success'))
    <x-alert-message class="alert-success">{{ session()->get('success') }}</x-alert-message>
    @endif

  <div class="row g-3">
    <div class="col-md-12">
      <!-- Striped Rows -->
      <div class="card">
        <div class="card-body">
            <div class="row">
              <div class="col-12 col-md-6 mb-3">
                <div id="myMap"></div>
              </div>
              <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="row g-0">
                            <div class="col-md-8">
                                <div class="card-body">
                                    <div class="row g-2">
                                        <div class="col-12">
                                            <label for="" class="fw-bold">Luas</label>
                                            <p class="card-text" id="text-view-area"></p>
                                        </div>
                                        <div class="col-12">
                                            <label for="" class="fw-bold">Latitude, Longitude</label>
                                            <p class="card-text" id="text-view-latlng"></p>
                                        </div>
                                        <div class="col-12">
                                            <label for="" class="fw-bold">Altitude</label>
                                            <p class="card-text" id="text-view-altitude"></p>
                                        </div>
                                        <div class="col-12">
                                            <label for="" class="fw-bold">Alamat</label>
                                            <p class="card-text" id="text-view-address"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <img class="card-img card-img-right" src="{{ asset('theme/img/elements/17.jpg') }}" alt="Card image" id="img-land" />
                            </div>
                        </div>
                    </div>
              </div>
            </div>
        </div>
      </div>
      <!--/ Striped Rows -->
    </div>

    <div class="col-12">
        <a href="{{ route('bitanic.garden.index', ['farmer' => $farmer->id, 'land' => $land->id]) }}" title="Klik untuk menu data kebun">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title m-0"><i class="bx bx-box fs-2"></i>&nbsp;Kebun</h3>
                </div>
            </div>
        </a>
    </div>

    @if ($land->rsc_telemetries && count($land->rsc_telemetries) > 0)
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="float-start">
                                <h3 class="card-title">Sampel data yang diambil di lahan {{ $land->rsc_telemetries[0]->created_at }}</h3>
                            </div>
                            <div class="float-end">
                                <a class="btn btn-success" href="{{ route('bitanic.land.export-excel', ['farmer' => $farmer->id, 'land' => $land->id]) }}" target="_blank">Export Excel</a>
                            </div>
                        </div>
                        <div class="col-12">
                            <h5>Rata-rata N: {{ $avgN ?? '-' }} mg/kg | Rata-rata P: {{ $avgP ?? '-' }} mg/kg | Rata-rata K: {{ $avgK ?? '-' }} mg/kg</h5>
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
                                <th>Suhu</th>
                                <th>Kelembapan</th>
                                <th>Suhu Tanah</th>
                                <th>Kelembapan Tanah</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($land->rsc_telemetries as $rsc_telemetry)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $rsc_telemetry->samples->n }} mg/kg</td>
                                <td>{{ $rsc_telemetry->samples->p }} mg/kg</td>
                                <td>{{ $rsc_telemetry->samples->k }} mg/kg</td>
                                <td>{{ $rsc_telemetry->samples->ec ?? '-' }}</td>
                                <td>{{ $rsc_telemetry->samples->temperature ?? '-' }}°C</td>
                                <td>{{ $rsc_telemetry->samples->moisture ?? '-' }}%</td>
                                <td>{{ $rsc_telemetry->samples->soil_temperature ?? '-' }}°C</td>
                                <td>{{ $rsc_telemetry->samples->soil_moisture ?? '-' }}%</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7">Data tidak ada</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
  </div>

  @push('scripts')
  <script src="{{ asset('leaflet/leaflet.js') }}"></script>
  <script src="{{ asset('theme/js/ui-popover.js') }}"></script>
  <script src="{{ asset('js/extend.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <!-- <script src="{{ asset('js/extra.js') }}"></script> -->
  <script>
    let latlngs = [];
    let gardensMarker = [];
    let gardensPolygon = [];
    let marker, polygon;
    let locMarker, locPoly
    const days = ['minggu', 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];

    let stateData = {
            lat: -6.869080223722067,
            lng: 107.72491693496704
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

    function textViewLand({ area, latitude, longitude, altitude, image, address }) {
        document.querySelector('#img-land').src = "{{ asset('') }}" + image
        document.querySelector('#text-view-area').textContent = area + " m²"
        document.querySelector('#text-view-latlng').textContent = `${latitude}, ${longitude}`
        document.querySelector('#text-view-altitude').textContent = altitude + " mdpl"
        document.querySelector('#text-view-address').textContent = address
    }

    const rscSamplesPosition = rsc_telemetries => {
        rsc_telemetries.forEach(rsc_telemetry => {
            // L.marker([sample.latitude, sample.longitude])
            L.circle(
                    [rsc_telemetry.samples.latitude, rsc_telemetry.samples.longitude],
                    {
                        radius: 1,
                        color: config.colors.danger
                    }
                )
                .bindPopup(
                    `<div class="d-flex gap-2 mb-2">
                        <span class="badge bg-info flex-fill">N: ${rsc_telemetry.samples.n} mg/kg</span><span class="badge bg-warning flex-fill">P: ${rsc_telemetry.samples.p} mg/kg</span><span class="badge bg-danger flex-fill">K: ${rsc_telemetry.samples.k} mg/kg</span>
                    </div>`, {
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
        try {
            const settings = {
                method: 'GET',
                headers: {
                    'x-csrf-token': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
            }

            let url = "{{ route('bitanic.land.get-land-rsc', ['farmer' => $farmer->id, 'land' => 'ID']) }}"

            const [data, error] = await yourRequest(url.replace('ID', id), settings)

            if (error) {
                errorMessage(error)

                return false
            }

            if (locPoly) {
                locPoly.remove()
            }

            if (locMarker) {
                locMarker.remove()
            }

            const land = data

            const colors = [
                config.colors.primary,
                config.colors.info,
                config.colors.warning,
                config.colors.danger
            ]

            // locMarker = L.marker([land.latitude, land.longitude])
            //     .bindPopup(
            //         `<div class="d-flex flex-column gap-2">
            //             <span class="badge bg-info flex-fill">${land.name}</span>
            //             <span class="badge bg-warning flex-fill">Luas: ${land.area} m²</span>
            //         </div>`, {
            //             closeButton: true
            //         }
            //     )
            //     .on('mouseover', function(e) {
            //         this.openPopup();
            //     })
            //     .on('mouseout', function(e) {
            //         this.closePopup();
            //     }).addTo(map)

            locPoly = L.polygon(land.polygon, {
                    color: "#" + land.color
                })
                .on('mouseover', function(e) {
                    this.openPopup();
                })
                .on('mouseout', function(e) {
                    this.closePopup();
                }).addTo(map)

            if (land.rsc_telemetries != null) {
                rscSamplesPosition(land.rsc_telemetries)
            }

            map.fitBounds(locPoly.getBounds());

            textViewLand(land)

        } catch (error) {
            console.log(error);
        }
    }

    document.addEventListener("DOMContentLoaded", () => {
        console.log("Hello World!");

        getLand("{{ $land->id }}")

        map.doubleClickZoom.disable();
    })
  </script>
  @endpush
</x-app-layout>
