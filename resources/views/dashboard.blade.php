<x-app-layout>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/MarkerCluster.css') }}">
        <link rel="stylesheet" href="{{ asset('css/MarkerCluster.Default.css') }}">
        <link rel="stylesheet" href="{{ asset('leaflet/leaflet.css') }}">
        <style>
            #gardensMap {
                height: 50vh;
            }

            .leaflet-legend {
                background-color: #f5f5f9;
                border-radius: 10px;
                padding: 10px;
                color: #3e8f55;
                box-shadow: 4px 3px 5px 5px #8d8989a8;
            }

            .clock {
                font-size: 18px;
                font-weight: bold;
                color: #3e8f55;
            }

            .bg-pro-devices {
                background-color: #ff9100
            }

            .outline-bitanic:hover {
                outline: 2.5px solid #3e8f55;
            }

            .bg-bitanic {
                background-color: #3e8f55;
            }

            .bg-fruit {
                background-color: #f6ff00
            }

            #modal-map {
                height: 50vh;
            }

            #img-land {
                height: 100%;
                object-fit: cover;
            }
        </style>
    @endpush

    <x-slot name="header">
    </x-slot>

    <!-- Content -->

    <div class="row gap-3">
        <div class="col-12">
            <div class="d-flex justify-content-start align-items-center gap-4 mb-3">
                <span>Tahun: </span>
                <select class="form-select w-25" id="filter-year-fertilizer" aria-label="Default select example">
                    @for ($i = now()->year; $i >= 2000; $i--)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="d-flex flex-wrap gap-3">
                {{-- <a href="{{ route('dashboard.kebutuhan-pupuk.province') }}" class="flex-fill"> --}}
                <div class="card h-100 text-info flex-fill">
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <div>
                            <span class="fw-semibold text-center d-block mb-1">Total Kebutuhan Pupuk N</span>
                            <h4 class="card-title text-center text-info mb-2" id="count-fertilizer-n">
                                <x-dashboard-spinner class="text-info"></x-dashboard-spinner>
                            </h4>
                        </div>
                    </div>
                </div>
                {{-- </a> --}}
                {{-- <a href="{{ route('dashboard.kebutuhan-pupuk.province') }}" class="flex-fill"> --}}
                <div class="card h-100 text-info flex-fill">
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <div>
                            <span class="fw-semibold text-center d-block mb-1">Total Kebutuhan Pupuk P</span>
                            <h4 class="card-title text-center text-info mb-2" id="count-fertilizer-p">
                                <x-dashboard-spinner class="text-info"></x-dashboard-spinner>
                            </h4>
                        </div>
                    </div>
                </div>
                {{-- </a> --}}
                {{-- <a href="{{ route('dashboard.kebutuhan-pupuk.province') }}" class="flex-fill"> --}}
                <div class="card h-100 text-info flex-fill">
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <div>
                            <span class="fw-semibold text-center d-block mb-1">Total Kebutuhan Pupuk K</span>
                            <h4 class="card-title text-center text-info mb-2" id="count-fertilizer-k">
                                <x-dashboard-spinner class="text-info"></x-dashboard-spinner>
                            </h4>
                        </div>
                    </div>
                </div>
                {{-- </a> --}}
            </div>
        </div>
        <div class="col-12">
            <div class="row gap-3">
                <div class="col-12">
                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('bitanic.farmer.index') }}" class="flex-fill">
                            <div class="card h-100 outline-bitanic text-dark">
                                <div class="card-body d-flex align-items-center justify-content-center">
                                    <div>
                                        <span
                                            class="fw-semibold text-center d-block mb-1">{{ auth()->user()->role == 'farmer' ? 'Total Kebun' : 'Total Akun Pengguna' }}</span>
                                        <h4 class="card-title text-center  mb-2"
                                            id="{{ auth()->user()->role == 'farmer' ? 'count-gardens' : 'count-farmers' }}">
                                            <x-dashboard-spinner class=""></x-dashboard-spinner>
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </a>
                        <a href="{{ route('bitanic.device.index') }}" class="flex-fill">
                            <div class="card h-100 outline-bitanic text-dark">
                                <div class="card-body d-flex align-items-center justify-content-center">
                                    <div>
                                        <span class="fw-semibold text-center d-block mb-1">Total Perangkat Pro</span>
                                        <h4 class="card-title text-center  mb-2" id="count-devices">
                                            <x-dashboard-spinner class=""></x-dashboard-spinner>
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </a>
                        @if (Auth::user()->role != 'admin')
                            <div class="card h-100 outline-bitanic text-dark flex-fill">
                                <div class="card-body d-flex align-items-center justify-content-center">
                                    <div>
                                        <span class="fw-semibold text-center d-block mb-1">Total Perangkat Lite</span>
                                        <h4 class="card-title text-center  mb-2" id="count-lite-devices">
                                            <x-dashboard-spinner class=""></x-dashboard-spinner>
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="card h-100 outline-bitanic text-dark flex-fill">
                                <div class="card-body d-flex align-items-center justify-content-center">
                                    <div>
                                        <span class="fw-semibold text-center d-block mb-1">Total Tanaman Sayur</span>
                                        <h4 class="card-title text-center  mb-2" id="count-vegies">
                                            <x-dashboard-spinner class=""></x-dashboard-spinner>
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="card h-100 outline-bitanic text-dark flex-fill">
                                <div class="card-body d-flex align-items-center justify-content-center">
                                    <div>
                                        <span class="fw-semibold text-center d-block mb-1">Total Tanaman Buah</span>
                                        <h4 class="card-title text-center mb-2" id="count-fruits">
                                            <x-dashboard-spinner class=""></x-dashboard-spinner>
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('bitanic.lite-device.index') }}" class="flex-fill">
                                <div class="card h-100 outline-bitanic text-dark">
                                    <div class="card-body d-flex align-items-center justify-content-center">
                                        <div>
                                            <span class="fw-semibold text-center d-block mb-1">Total Perangkat
                                                Lite</span>
                                            <h4 class="card-title text-center  mb-2" id="count-lite-devices">
                                                <x-dashboard-spinner class=""></x-dashboard-spinner>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            <a href="{{ route('bitanic.crop.index', ['jenis' => 'sayur']) }}" class="flex-fill">
                                <div class="card h-100 outline-bitanic text-dark">
                                    <div class="card-body d-flex align-items-center justify-content-center">
                                        <div>
                                            <span class="fw-semibold text-center d-block mb-1">Total Tanaman
                                                Sayur</span>
                                            <h4 class="card-title text-center  mb-2" id="count-vegies">
                                                <x-dashboard-spinner class=""></x-dashboard-spinner>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            <a href="{{ route('bitanic.crop.index', ['jenis' => 'buah']) }}" class="flex-fill">
                                <div class="card h-100 outline-bitanic text-dark">
                                    <div class="card-body d-flex align-items-center justify-content-center">
                                        <div>
                                            <span class="fw-semibold text-center d-block mb-1">Total Tanaman Buah</span>
                                            <h4 class="card-title text-center mb-2" id="count-fruits">
                                                <x-dashboard-spinner class=""></x-dashboard-spinner>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endif
                    </div>
                </div>

            </div>
        </div>
        <div class="col-12">
            <div class="row g-3">
                <div class="col-12">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="row gap-3">
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="float-start">
                                                <div class="badge bg-bitanic text-wrap lh-sm text-start text-white mb-2 p-2"
                                                    role="alert">Klik hasil pencarian lokasi di bawah untuk melihat
                                                    markernya!</div>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control shadow-none"
                                                        placeholder="Cari nama lahan..."
                                                        aria-label="Cari nama lahan..." id="map-search-markers"
                                                        name="search" />
                                                    <span class="input-group-text text-white"
                                                        style="cursor: pointer; background-color: #3e8f55;">
                                                        <i class="bx bx-search"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="float-end">
                                                <button class="btn btn-info" id="btn-get-location"
                                                    title="Klik untuk zoom ke lokasi anda saat ini.">Lihat lokasi
                                                    saya</button>
                                                <br>
                                                <span id="status"></span>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div id="hasil-search" class="d-flex flex-row flex-wrap gap-2"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div id="gardensMap"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- <div class="col-12 col-md-4">
                    <div class="row gap-3">
                        <div class="col-12">
                            <div class="card h-100 position-relative">
                                <div class="card-header d-flex align-items-center justify-content-between pb-0">
                                    <div class="card-title mb-0">
                                        <h5 class="card-title">Aktivitas Menanam</h5>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row gap-3">
                                        <div class="col-12">
                                            <div
                                                class="d-flex gap-3 flex-row align-items-center justify-content-between mb-3">
                                                <div class="d-flex flex-column align-items-center gap-1">
                                                    <h2 class="mb-2" id="total-aktivitas">
                                                        <x-dashboard-spinner></x-dashboard-spinner>
                                                    </h2>
                                                    <span>Total Aktivitas</span>
                                                </div>
                                                <div id="plantingActivityChart"></div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div>
                                                <ul class="p-0 m-0">
                                                    <li class="d-flex mb-4 pb-1">
                                                        <div
                                                            class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                            <div class="me-2">
                                                                <span class="rounded bg-label-primary p-2">Persiapan
                                                                    Penanaman</span>
                                                            </div>
                                                            <div class="user-progress">
                                                                <small class="fw-semibold" id="count-planting">
                                                                    <x-dashboard-spinner></x-dashboard-spinner>
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li class="d-flex mb-4 pb-1">
                                                        <div
                                                            class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                            <div class="me-2">
                                                                <span class="rounded bg-label-warning p-2">Penanaman
                                                                    dan Pemeliharaan</span>
                                                            </div>
                                                            <div class="user-progress">
                                                                <small class="fw-semibold"
                                                                    id="count-maintenance-period">
                                                                    <x-dashboard-spinner></x-dashboard-spinner>
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li class="d-flex mb-4 pb-1">
                                                        <div
                                                            class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                            <div class="me-2">
                                                                <span class="rounded bg-label-info p-2">Masa
                                                                    Panen</span>
                                                            </div>
                                                            <div class="user-progress">
                                                                <small class="fw-semibold" id="count-harvest-period">
                                                                    <x-dashboard-spinner></x-dashboard-spinner>
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>
        <div class="col-12">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title mb-0">
                                <h5 class="m-0 me-2">Rekapitulasi Transaksi Bitanic Tahun <span
                                        class="text-year">{{ today('Asia/Jakarta')->format('Y') }}</span>
                                </h5>
                            </div>
                        </div>
                        <div class="card-body">
                            {{-- <div id="barChartActiveGardens"></div> --}}
                            <div id="barChartTransactions"></div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <div class="card-title mb-0">
                                <h5 class="m-0 me-2">Rekapitulasi Tanaman yang Telah Ditanam Tahun <span
                                        class="text-year">{{ today('Asia/Jakarta')->format('Y') }}</span></h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="plantedCropChart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 d-none">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <div class="card-title mb-0">
                                <h5 class="m-0 me-2">Top 10 Petani</h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive text-wrap pb-2">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%;">No</th>
                                            <th>Name</th>
                                            <th style="width: 5%;">Jumlah Aktivitas</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0" id="list-farmers">
                                        <tr>
                                            <td colspan="3">Loading...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->

    <!-- Modal -->
    @include('_modal-detail-garden')
    <!-- End Modal -->

    @push('scripts')
        <script src="{{ asset('leaflet/leaflet.js') }}"></script>
        <script src="{{ asset('js/leaflet.markercluster-src.js') }}"></script>
        <script>
            // loading where page start
            showSpinner()

            function displayClock() {
                const clockDiv = document.querySelector("#clock");
                return setInterval(() => {
                    const date = new Date();
                    const tick = date.toLocaleTimeString();
                    clockDiv.textContent = tick;
                }, 1000);
            }

            window.onload = displayClock();

            const modalDetailGarden = new bootstrap.Modal(document.getElementById("modalDetailGarden"), {});
            let gardensMarker = []
            let markerGroupGarden = L.markerClusterGroup();

            // Map Start
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
            const modalMap = L.map('modal-map', {
                    preferCanvas: true,
                    layers: [googleStreetsSecond],
                    zoomControl: true
                })
                .setView([-6.869080223722067, 107.72491693496704], 12);

            // Initial MAP
            const gardensMap = L.map('gardensMap', {
                preferCanvas: true,
                layers: [googleStreetsThird],
                zoomControl: true
            }).setView([-0.45159435125092, 117.59765625000001], 5);

            gardensMap.doubleClickZoom.disable();

            // TODO: add legend in map
            var legend = L.control({
                position: 'bottomleft'
            });
            legend.onAdd = function(map) {

                var div = L.DomUtil.create('div', 'info legend leaflet-legend');

                labels = ['<strong>Legenda</strong>'];
                labels.push(
                    '<i class="bx bxs-map" style="color:#3e92cf;"></i> Marker Lahan');
                // labels.push(
                //     '<i class="bx bxs-circle" style="color:#8cd464;"></i> Marker Group');

                div.innerHTML = labels.join('<br>');
                return div;
            };
            legend.addTo(gardensMap);

            // TODO: add search in map
            // var search = L.control({
            //     position: 'topright'
            // });
            // search.onAdd = function(map) {

            //     var div = L.DomUtil.create('div');

            //     div.innerHTML =
            //         `
    // <div class="alert alert-primary" role="alert">Klik hasil pencarian lokasi di bawah untuk melihat markernya!</div>`;

            //     div.innerHTML += `
    //     <div class="input-group">
    //         <input type="text" class="form-control shadow-none"
    //             placeholder="Cari nama lokasi..." aria-label="Cari nama lokasi..." id="map-search-markers" name="search" />
    //         <span class="input-group-text text-white"
    //             style="cursor: pointer; background-color: #3e8f55;">
    //             <i class="bx bx-search"></i>
    //         </span>
    //     </div>`;
            //     return div;
            // };
            // search.addTo(gardensMap);

            // var hasilSearch = L.control({
            //     position: 'topright'
            // });
            // hasilSearch.onAdd = function(map) {

            //     var div = L.DomUtil.create('div');

            //     div.id = "hasil-search"

            //     return div;
            // };
            // hasilSearch.addTo(gardensMap);

            // Group marker
            var markerGroup = L.markerClusterGroup();

            // TODO: Get marker from api
            const getGardens = async () => {
                try {
                    const settings = {
                        method: 'GET',
                        headers: {
                            'x-csrf-token': '{{ csrf_token() }}'
                        },
                    }

                    const [data, error] = await yourRequest("{{ route('web.map.get') }}", settings)

                    if (error) {
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

                    const gardens = await data.gardens
                    console.log(gardens);

                    let locMarker
                    const colors = [
                        config.colors.primary,
                        config.colors.info,
                        config.colors.warning,
                        config.colors.danger
                    ]

                    gardens.forEach(garden => {
                        locMarker = L.marker([garden.latitude, garden.longitude])
                            .on('click', function(e) {
                                gardenShow(garden.id)
                            })
                            .bindPopup(garden.name)
                            .on('mouseover', function(e) {
                                this.openPopup();
                            })
                            .on('mouseout', function(e) {
                                this.closePopup();
                            })

                        gardensMarker.push({
                            id: garden.id,
                            marker: locMarker
                        })

                        markerGroup.addLayer(locMarker)
                    });

                    gardensMap.addLayer(markerGroup);


                } catch (error) {
                    console.log(error);
                }
            }

            function textViewLand({
                name,
                area,
                latitude,
                longitude,
                altitude,
                image,
                address
            }) {
                document.querySelector('#img-land').src = "{{ asset('') }}" + image
                document.querySelector('#text-view-land-name').textContent = name
                document.querySelector('#text-view-land-area').textContent = area + " m²"
                document.querySelector('#text-view-land-latlng').textContent = `${latitude}, ${longitude}`
                document.querySelector('#text-view-land-altitude').textContent = altitude + " mdpl"
                document.querySelector('#text-view-land-address').textContent = address
            }

            const gardenShow = async (id) => {
                try {
                    modalDetailGarden.show()

                    detailAddSpinner('detail-modal')

                    const settings = {
                        method: 'GET',
                        headers: {
                            'x-csrf-token': '{{ csrf_token() }}'
                        },
                    }

                    const [data, error] = await yourRequest("{{ route('web.map.show', 'ID') }}"
                        .replace('ID', id), settings)

                    if (error) {
                        if ("messages" in error) {
                            detailDeleteSpinner('detail-modal')
                            modalDetailGarden.toggle()

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

                    const land = await data.land

                    markerGroupGarden.clearLayers()

                    let landPolygon = L.polygon(land.polygon, {
                        color: "#" + land.color + "55"
                    })

                    markerGroupGarden.addLayer(landPolygon)

                    land.gardens.forEach(garden => {
                        if (garden.polygon) {
                            gardenPoly = L.polygon(garden.polygon, {
                                    color: "#" + garden.color,
                                    dashArray: '10, 10',
                                    dashOffset: '20',
                                })
                                .bindPopup(garden.name)

                            markerGroupGarden.addLayer(gardenPoly)
                        }
                    });

                    modalMap.addLayer(markerGroupGarden);

                    // modalMap.fitBounds(landPolygon.getBounds());

                    setTimeout(() => {
                        modalMap.invalidateSize();

                        modalMap.fitBounds(landPolygon.getBounds());
                    }, 1000);

                    document.getElementById('modalDetailGardenTitle').innerText = land.name

                    textViewLand(land)

                    let trGardens = ``

                    land.gardens.forEach((garden, index) => {
                        trGardens += `<tr>
                            <td class="text-center">${index + 1}</td>
                            <td>${garden.name ?? '-'}</td>
                            <td>${garden.current_commodity?.crop?.crop_name ?? '-'}</td>
                        </tr>`
                    })

                    if (land.gardens.length == 0) {
                        trGardens = `<tr>
                            <td class="text-center" colspan="3">Tidak ada data</td>
                        </tr>`
                    }

                    document.querySelector('tbody#gardens-list').innerHTML = trGardens

                    document.getElementById('text-view-user-name').innerHTML = land.farmer.full_name
                    // document.getElementById('modal-detail-type').innerHTML = garden.gardes_type
                    // document.getElementById('modal-detail-area').innerHTML = garden.land.area + " hektar"
                    // document.getElementById('modal-detail-lat').innerHTML = garden.land.latitude
                    // document.getElementById('modal-detail-lng').innerHTML = garden.land.longitude
                    // document.getElementById('modal-detail-alt').innerHTML = garden.land.altitude + " mdpl"
                    // document.getElementById('modal-detail-date-created').innerHTML = garden.date_created
                    // document.getElementById('modal-detail-crop').innerHTML = garden.crop.crop_name
                    // document.getElementById('modal-detail-device').innerHTML = garden.device.device_series
                    // document.getElementById('modal-detail-address').innerHTML = garden.land.address

                    // document.getElementById('modal-detail-temperature').innerHTML = (garden.temperature ?? '-') + " °C"
                    // document.getElementById('modal-detail-moisture').innerHTML = (garden.moisture ?? '-') + "%"
                    // document.getElementById('modal-detail-nitrogen').innerHTML = (garden.nitrogen ?? '-') + "kg"
                    // document.getElementById('modal-detail-phosphor').innerHTML = (garden.phosphor ?? '-') + "kg"
                    // document.getElementById('modal-detail-kalium').innerHTML = (garden.kalium ?? '-') + "kg"

                } catch (error) {
                    console.log(error);
                }
            }

            // executed after 700 milisecond
            function doneTyping() {
                let newData = {
                    'category': $('.kategori-button.active').data('id'),
                    'key': $('#searchInput').val()
                }
                loadClass(newData);
            }

            async function searchKebun(value = '') {
                try {
                    if (!value) {
                        document.querySelector('#hasil-search').innerHTML = ``
                        return false
                    }

                    let url = "{{ route('web.map.get') }}"

                    const settings = {
                        method: 'GET',
                        headers: {
                            'x-csrf-token': '{{ csrf_token() }}'
                        },
                    }

                    const [data, error] = await yourRequest(url + "?search=" + value, settings)

                    if (error) {
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

                    const gardens = await data.gardens

                    let list = ``

                    gardens.forEach(garden => {
                        list += `
                        <button type="button" data-lat="${garden.latitude}" data-lng="${garden.longitude}"
                            class="btn btn-sm btn-outline-primary" style="cursor:pointer" data-id="${garden.id}"
                            >${garden.name}</button
                        >`;
                    })

                    document.querySelector('#hasil-search').innerHTML = `${list}`

                } catch (error) {
                    console.log(error);
                }
            }

            // Map End

            let cardColor, headingColor, axisColor, shadeColor, borderColor;

            cardColor = config.colors.white;
            headingColor = config.colors.headingColor;
            axisColor = config.colors.axisColor;
            borderColor = config.colors.borderColor;

            const exPlantingActivityChart = async () => {
                try {
                    let activityData = []

                    const settings = {
                        method: 'GET',
                        headers: {
                            'x-csrf-token': '{{ csrf_token() }}'
                        },
                    }

                    const [data, error] = await yourRequest("{{ route('web.get-planting-activity') }}", settings)
                    console.log("Aktivitas Menanam: ", data)
                    if (error) {
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

                    activityData.push(data.count_planting)
                    activityData.push(data.count_maintenance_period)
                    activityData.push(data.count_harvest_period)

                    const totalActivity = activityData.reduce((p, c) => p + c, 0)

                    document.getElementById('total-aktivitas').innerHTML = totalActivity
                    document.getElementById('count-planting').innerHTML = data.count_planting
                    document.getElementById('count-maintenance-period').innerHTML = data.count_maintenance_period
                    document.getElementById('count-harvest-period').innerHTML = data.count_harvest_period

                    const chartPlantingActivity = document.querySelector(
                            "#plantingActivityChart"
                        ),
                        plantingChartConfig = {
                            chart: {
                                width: 230,
                                type: "pie",
                            },
                            labels: ["Sedang Menanam", "Masa Pemeliharaan", "Masa Panen"],
                            series: activityData,
                            colors: [
                                config.colors.primary,
                                config.colors.warning,
                                config.colors.info,
                            ],
                            stroke: {
                                width: 2,
                                colors: cardColor,
                            },
                            dataLabels: {
                                enabled: true,
                                // formatter: function (val, opt) {
                                //     return parseInt(val) + "%";
                                // },
                            },
                            legend: {
                                show: false,
                            },
                            grid: {
                                padding: {
                                    top: 0,
                                    bottom: 0,
                                    right: 15,
                                },
                            },
                            // plotOptions: {
                            //     pie: {
                            //         donut: {
                            //             size: "75%",
                            //             labels: {
                            //                 show: true,
                            //                 value: {
                            //                     fontSize: "1.5rem",
                            //                     fontFamily: "Public Sans",
                            //                     color: headingColor,
                            //                     offsetY: -15,
                            //                     formatter: function (val) {
                            //                         return parseInt(val) + "%";
                            //                     },
                            //                 },
                            //                 name: {
                            //                     offsetY: 20,
                            //                     fontFamily: "Public Sans",
                            //                 },
                            //                 total: {
                            //                     show: true,
                            //                     fontSize: "0.8125rem",
                            //                     color: axisColor,
                            //                     label: "Weekly",
                            //                     formatter: function (w) {
                            //                         return "38%";
                            //                     },
                            //                 },
                            //             },
                            //         },
                            //     },
                            // },
                        };

                    const activityChart = new ApexCharts(
                        chartPlantingActivity,
                        plantingChartConfig
                    );
                    activityChart.render();

                } catch (error) {
                    console.log(error);
                }
            }

            const getFertilization = async (year = null) => {
                try {
                    let activityData = []

                    const settings = {
                        method: 'GET',
                        headers: {
                            'x-csrf-token': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                    }

                    const [data, error] = await yourRequest("{{ route('web.get-fertilizer') }}?year=" + year, settings)

                    if (error) {
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

                    document.getElementById('count-fertilizer-n').innerHTML = parseFloat(data.fertilization.sum_n ?? 0)
                        .toFixed(2) + "kg"
                    document.getElementById('count-fertilizer-p').innerHTML = parseFloat(data.fertilization.sum_p ?? 0)
                        .toFixed(2) + "kg"
                    document.getElementById('count-fertilizer-k').innerHTML = parseFloat(data.fertilization.sum_k ?? 0)
                        .toFixed(2) + "kg"

                } catch (error) {
                    console.log(error);
                }
            }
            const getDashboardData = async () => {
                try {
                    let activityData = []

                    const settings = {
                        method: 'GET',
                        headers: {
                            'x-csrf-token': '{{ csrf_token() }}'
                        },
                    }

                    const [data, error] = await yourRequest("{{ route('web.get-dashboard-data') }}", settings)

                    if (error) {
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

                    if (document.getElementById('count-farmers')) {
                        document.getElementById('count-farmers').innerHTML = data.count_farmers
                    }
                    if (document.getElementById('count-gardens')) {
                        document.getElementById('count-gardens').innerHTML = data.count_gardens
                    }
                    document.getElementById('count-devices').innerHTML = data.count_devices
                    document.getElementById('count-lite-devices').innerHTML = data.count_lite_devices
                    document.getElementById('count-vegies').innerHTML = data.count_vegies
                    document.getElementById('count-fruits').innerHTML = data.count_fruits
                    document.getElementById('count-fertilizer-n').innerHTML = parseFloat(data.count_fertilizer.sum_n ??
                        0).toFixed(2) + "kg"
                    document.getElementById('count-fertilizer-p').innerHTML = parseFloat(data.count_fertilizer.sum_p ??
                        0).toFixed(2) + "kg"
                    document.getElementById('count-fertilizer-k').innerHTML = parseFloat(data.count_fertilizer.sum_k ??
                        0).toFixed(2) + "kg"

                } catch (error) {
                    console.log(error);
                }
            }

            const detailAddSpinner = (detailClass) => {
                const details = document.querySelectorAll('.' + detailClass)
                details.forEach(detail => {
                    detail.innerHTML = `<div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>`
                });
            }

            const detailDeleteSpinner = (detailClass) => {
                const details = document.querySelectorAll('.' + detailClass)
                details.forEach(detail => {
                    detail.innerHTML = `-`
                });
            }

            // Get user location
            function geoFindMe() {

                const status = document.querySelector('#status');
                // const mapLink = document.querySelector('#map-link');

                // mapLink.href = '';
                // mapLink.textContent = '';

                function success(position) {
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;

                    status.textContent = '';
                    gardensMap.setView([latitude, longitude], 12)
                    // mapLink.href = `https://www.openstreetmap.org/#map=18/${latitude}/${longitude}`;
                    // mapLink.textContent = `Latitude: ${latitude} °, Longitude: ${longitude} °`;
                }

                function error() {
                    status.textContent = 'Unable to retrieve your location';
                }

                if (!navigator.geolocation) {
                    status.textContent = 'Geolocation is not supported by your browser';
                } else {
                    status.textContent = 'Locating…';
                    navigator.geolocation.getCurrentPosition(success, error);
                }

            }

            // Generate random color
            function randomColor() {
                var randomColor = "#" + (Math.floor(Math.random() * 16777215).toString(16));
                return randomColor;
                // document.getElementById("box").style.backgroundColor = randomColor;
            };

            async function countPlantedChart() {
                try {
                    const settings = {
                        method: 'GET',
                        headers: {
                            'x-csrf-token': '{{ csrf_token() }}'
                        },
                    }

                    const [data, error] = await yourRequest("{{ route('web.count-planted.get') }}", settings)

                    if (error) {
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

                    const labels = data.crops.map((a, b) => a.crop_name)
                    const crops = data.crops.map((a, b) => a.count_planted)
                    const colors = data.crops.map((a, b) => randomColor())

                    const chartPlantedCrops = document.querySelector(
                            "#plantedCropChart"
                        ),
                        plantedChartConfig = {
                            chart: {
                                height: '400px',
                                width: '100%',
                                type: "pie",
                            },
                            labels: labels,
                            series: crops,
                            colors: colors,
                            stroke: {
                                width: 2,
                                colors: cardColor,
                            },
                            dataLabels: {
                                enabled: true,
                                // formatter: function (val, opt) {
                                //     return parseInt(val) + "%";
                                // },
                            },
                            legend: {
                                position: 'bottom'
                            }
                        };

                    const plantedCropsChart = new ApexCharts(
                        chartPlantedCrops,
                        plantedChartConfig
                    );
                    plantedCropsChart.render();

                } catch (error) {
                    console.log(error);
                }
            }

            async function topTenFarmers(url = null) {
                try {
                    document.getElementById('list-farmers').innerHTML = `
                    <tr>
                        <td colspan="3">Loading...</td>
                    </tr>`

                    const settings = {
                        method: 'GET',
                        headers: {
                            'x-csrf-token': '{{ csrf_token() }}'
                        },
                    }

                    const [data, error] = await yourRequest(url ?? "{{ route('web.top-ten-farmers.get') }}", settings)

                    if (error) {
                        if ("messages" in error) {

                            document.getElementById('list-farmers').innerHTML = `
                            <tr>
                                <td colspan="3">Error...</td>
                            </tr>`
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

                    const farmers = data.farmers

                    let list = ``

                    for (let i = 0; i < farmers.length; i++) {
                        const farmer = farmers[i];
                        list += `<tr>
                                <td>${i + 1}</td>
                                <td>
                                    <a href="javascript:;" type="button" class="avatar pull-up"
                                        style="display: inline-block;">
                                        <img src="{{ asset('') }}${farmer.picture ?? 'bitanic-landing/default-image.jpg'}" alt="Avatar" class="rounded-circle" />
                                    </a>
                                    ${farmer.full_name}
                                </td>
                                <td class="text-center">${farmer.count_activity}</td>
                            </tr>`
                    }

                    document.getElementById('list-farmers').innerHTML = list
                } catch (error) {
                    console.log(error);
                }
            }

            async function barChartActiveGardens() {
                try {
                    const settings = {
                        method: 'GET',
                        headers: {
                            'x-csrf-token': '{{ csrf_token() }}'
                        },
                    }

                    const [data, error] = await yourRequest("{{ route('web.active-gardens.index') }}", settings)

                    if (error) {
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

                    const months = await data.months

                    const chartActiveGardens = document.querySelector(
                            "#barChartActiveGardens"
                        ),
                        activeGardensConfig = {
                            chart: {
                                height: '400',
                                type: 'bar'
                            },
                            colors: ['#3e8f55'],
                            series: [{
                                data: months
                            }],
                            yaxis: {
                                forceNiceScale: true,
                                title: {
                                    text: "Jumlah Aktivitas"
                                }
                            },
                            xaxis: {
                                title: {
                                    text: "Bulan"
                                }
                            }
                        };

                    const activeGardensChart = new ApexCharts(
                        chartActiveGardens,
                        activeGardensConfig
                    );
                    activeGardensChart.render();

                } catch (error) {
                    console.log(error);
                }
            }

            async function barChartRekapitulasiTransaksi() {
                try {
                    const settings = {
                        method: 'GET',
                        headers: {
                            'x-csrf-token': '{{ csrf_token() }}'
                        },
                    }

                    const [data, error] = await yourRequest("{{ route('dashboard.rekapitulasi-transaksi') }}", settings)

                    if (error) {
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

                    const transactions = await data.transactions

                    const chartTransactions = document.querySelector(
                            "#barChartTransactions"
                        ),
                        transactionsConfig = {
                            chart: {
                                height: '400',
                                type: 'bar'
                            },
                            colors: ['#3e8f55'],
                            series: [{
                                name: "Jumlah Transaksi",
                                data: transactions
                            }],
                            yaxis: {
                                forceNiceScale: true,
                                title: {
                                    text: "Jumlah Transaksi"
                                }
                            },
                            xaxis: {
                                title: {
                                    text: "Bulan"
                                }
                            }
                        };

                    const transactionsChart = new ApexCharts(
                        chartTransactions,
                        transactionsConfig
                    );
                    transactionsChart.render();

                } catch (error) {
                    console.log(error);
                }
            }

            function debounce(func, timeout = 300) {
                let timer;
                return (...args) => {
                    clearTimeout(timer);
                    timer = setTimeout(() => {
                        func.apply(this, args);
                    }, timeout);
                };
            }

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

                //delete loading after page loaded
                deleteSpinner()

                getGardens()
                exPlantingActivityChart()
                countPlantedChart()
                getDashboardData()
                // topTenFarmers()
                // barChartActiveGardens()
                barChartRekapitulasiTransaksi()

                const searchProses = debounce((e) => searchKebun(e.target.value), 750)

                document.querySelector('#map-search-markers').addEventListener('keyup', searchProses)

                document.querySelector('#filter-year-fertilizer').addEventListener('change', e => {
                    getFertilization(e.target.value)
                })

                document.querySelector('#hasil-search').addEventListener('click', e => {
                    console.dir(e.target)
                    let latlng = [parseFloat(e.target.dataset['lat']), parseFloat(e.target.dataset['lng'])]

                    let findSelectedGarden = gardensMarker.find(garden => {
                        return garden.id === parseInt(e.target.dataset['id'])
                    })

                    console.log(findSelectedGarden.marker.openPopup());

                    gardensMap.setView(latlng, 18)
                })

                document.getElementById('btn-get-location').addEventListener('click', geoFindMe)
            });
        </script>
    @endpush
</x-app-layout>
