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
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master / <a href="{{ route('bitanic.device.index') }}">Data Perangkat</a> / <a href="{{ route('bitanic.v3-device.show', $device->id) }}">{{ $device->device_series }}</a> </span>/ Tambah Selenoid </h4>
  </x-slot>
  {{-- End Header --}}

  <div class="row">
    <div class="col-md-12">
      <!-- Striped Rows -->
      <div class="card">
        <div class="card-body">
          <form action="{{ route('bitanic.v3-device.selenoid.store', $device->id) }}" method="POST">
            @csrf
            <!-- Validation Errors -->
            <x-auth-validation-errors class="mb-4" :errors="$errors" />

            <div class="row">
                <div class="col mb-3">
                    <label for="data-input-land-id" class="form-label">Lahan</label>
                    <br>
                    <select class="form-select select2-active" id="data-input-land-id" name="land_id" aria-label="Default select example">
                        <option value="">-- Pilih Lahan --</option>
                        @forelse ($lands as $id => $land)
                            <option value="{{ $id }}">{{ $land }}</option>
                        @empty
                            <option disabled>Tidak ada Lahan</option>
                        @endforelse
                    </select>
                </div>
            </div>
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
            <div class="row mb-3 g-2">
                <div class="col-12 mt-1">
                    <label for="data-input-morning-time" class="form-label">Durasi Penyiraman Sebelum Pemupukan </label>
                </div>
                <div class="col-12 col-md-6">
                    <div class="input-group">
                        <input type="number" min="0" max="60" class="form-control" name="water_before[minutes]" />
                        <span class="input-group-text" id="basic-addon13">Menit</span>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="input-group">
                        <input type="number" min="0" max="60" class="form-control" name="water_before[seconds]" />
                        <span class="input-group-text" id="basic-addon13">Detik</span>
                    </div>
                </div>
                <div class="col-12 mt-1">
                    <label for="data-input-morning-time" class="form-label">Durasi Pendorongan Setelah Pemupukan </label>
                </div>
                <div class="col-12 col-md-6">
                    <div class="input-group">
                        <input type="number" min="0" max="60" class="form-control" name="water_after[minutes]" />
                        <span class="input-group-text" id="basic-addon13">Menit</span>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="input-group">
                        <input type="number" min="0" max="60" class="form-control" name="water_after[seconds]" />
                        <span class="input-group-text" id="basic-addon13">Detik</span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary w-100" id="submit-btn">Save</button>
                </div>
            </div>
          </form>
        </div>
      </div>
      <!--/ Striped Rows -->
    </div>
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

    const btnSubmit = document.getElementById('submit-btn')
    btnSubmit.addEventListener('submit', e => {
      // Show loading indication
      btnSubmit.setAttribute('data-kt-indicator', 'on');

      // Disable button to avoid multiple click
      btnSubmit.disabled = true;

      // document.getElementById('form-product').submit()
    })

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
        document.querySelector('#text-view-area').textContent = area + "m²"
        document.querySelector('#text-view-latlng').textContent = `${latitude}, ${longitude}`
        document.querySelector('#text-view-altitude').textContent = altitude + " mdpl"
        document.querySelector('#text-view-address').textContent = address
    }

    const getLand = async (id) => {
        try {
            const settings = {
                method: 'GET',
                headers: {
                    'x-csrf-token': '{{ csrf_token() }}'
                },
            }

            let url = "{{ route('bitanic.land.get-land', ['farmer' => $device->farmer_id, 'land' => 'ID']) }}"

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

            locMarker = L.marker([land.latitude, land.longitude])
                .bindPopup(
                    `<div class="d-flex flex-column gap-2">
                        <span class="badge bg-info flex-fill">${land.name}</span>
                        <span class="badge bg-warning flex-fill">Luas: ${land.area} m²</span>
                    </div>`, {
                        closeButton: true
                    }
                )
                .on('mouseover', function(e) {
                    this.openPopup();
                })
                .on('mouseout', function(e) {
                    this.closePopup();
                }).addTo(map)

            locPoly = L.polygon(land.polygon, {
                    color: colors[randomNumber(0, 3)]
                })
                .on('mouseover', function(e) {
                    this.openPopup();
                })
                .on('mouseout', function(e) {
                    this.closePopup();
                }).addTo(map)

            map.fitBounds(locPoly.getBounds());

            textViewLand(land)

        } catch (error) {
            console.log(error);
        }
    }

    document.addEventListener("DOMContentLoaded", () => {
        console.log("Hello World!");

        $('.select2-active').select2();

        $('#data-input-land-id').on('select2:select', function (e) {
            var data = e.params.data;
            console.log(data);
            getLand(data.id)
        });

        map.doubleClickZoom.disable();
    })
  </script>
  @endpush
</x-app-layout>
