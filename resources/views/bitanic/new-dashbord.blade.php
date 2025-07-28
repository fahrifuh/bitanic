<x-app-layout>

    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/MarkerCluster.css') }}">
    <link rel="stylesheet" href="{{ asset('css/MarkerCluster.Default.css') }}">
    <link rel="stylesheet" href="{{ asset('leaflet/leaflet.css') }}">
    <style>
        #gardensMap {
            height: 65vh;
        }
        .leaflet-legend {
            background-color: #f5f5f9;
            border-radius: 10px;
            padding: 10px;
            color: #242424;
            /* box-shadow: 4px 3px 5px 5px #8d8989a8; */
        }
        .leaflet-detail-land {
            position: absolute;
            top: 50%;
            left: -100%;
            transform: translateY(-50%);
            background-color: #ffffff;
            border-radius: 10px;
            padding: 20px;
            color: #292929;
            z-index: 999;
            transition: .5s ease left;
            /* font-family: Arial, Helvetica, sans-serif; */
            /* box-shadow: 4px 3px 5px 5px #8d8989a8; */
        }
        #clock{
            font-size: 24px;
            font-weight: bold;
            color: #3e8f55;
        }
        .bg-bitanic {
            --bs-bg-opacity: 1;
            background-color: #4FD1C5 !important;
        }
    </style>
    @endpush

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <!-- Content -->

    <div class="flex-grow-1">
        <div class="row">
            <div class="col-12 mb-3 order-0">
                <div class="row">
                    <div class="col-12 col-md-3">
                        <a href="{{ route('dashboard.kebutuhan-pupuk.province') }}">
                            <div class="card h-100 bg-white text-dark">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <span class="text-muted">Kebutuhan Pupuk</span>
                                        <br/>
                                        <span class="fw-semibold">N:10 P:12 K:20</span>
                                    </div>
                                    <div>
                                        <span class="badge bg-bitanic p-2"><i class='fs-3 bx bxs-wallet'></i></span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="card h-100 bg-white text-dark">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted">Total RSC</span>
                                    <br/>
                                    <span class="fw-semibold">100</span>
                                </div>
                                <div>
                                    <span class="badge bg-bitanic p-2"><i class='fs-3 bx bxs-wallet'></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="card h-100 bg-white text-dark">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted">Total Bitanic Pro</span>
                                    <br/>
                                    <span class="fw-semibold">73</span>
                                </div>
                                <div>
                                    <span class="badge bg-bitanic p-2"><i class='fs-3 bx bxs-wallet'></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="card h-100 bg-white text-dark">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted">Total Bitanic Lite</span>
                                    <br/>
                                    <span class="fw-semibold">38</span>
                                </div>
                                <div>
                                    <span class="badge bg-bitanic p-2"><i class='fs-3 bx bxs-wallet'></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 mb-3">
                <div class="position-relative overflow-hidden">
                    <div class="leaflet-detail-land">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5><b>Informasi Detail</b></h5>
                            </div>
                            <div>
                                <button
                                    type="button"
                                    class="btn-close"
                                    aria-label="Close"
                                    id="btn-close-detail-land"
                                ></button>
                            </div>
                        </div>
                        <div class="d-flex flex-column align-items-start gap-2 mb-3">
                            <span><span class="fw-bold">Luas Lahan</span>: <span class="fw-lighter">1,286 m2</span></span>
                            <span><span class="fw-bold">Kondisi Tanah</span>: <span class="fw-lighter">pH:6 N:6 P:6 K:6</span></span>
                            <span><span class="fw-bold">Fase</span>: <span class="fw-lighter">Masa Pemeliharaan</span></span>
                            <span><span class="fw-bold">Lokasi</span>: <span class="fw-lighter">Bandung, Indonesia</span></span>
                            <span><span class="fw-bold">Kontak</span>: <span class="fw-lighter">628976281676</span></span>
                        </div>
                    </div>
                    <div class="rounded-3" id="gardensMap"></div>
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
                const tick = date.toLocaleDateString() + " " + date.toLocaleTimeString();
                clockDiv.textContent = tick;
            }, 1000);
        }

        // window.onload = displayClock();

        const modalDetailGarden = new bootstrap.Modal(document.getElementById("modalDetailGarden"), {});
        let gardensMarker = []

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

        // Initial MAP
        const gardensMap = L.map('gardensMap', {
            preferCanvas: true,
            layers: [googleStreetsThird],
            zoomControl: false
        })
        .setView([-0.45159435125092, 117.59765625000001], 5);

        // L.control.zoom({
        //     position: 'bottomright'
        // }).addTo(gardensMap);

        gardensMap.doubleClickZoom.disable();

        // TODO: add legend in map
        var legend = L.control({position: 'bottomright'});
        legend.onAdd = function (map) {

            var div = L.DomUtil.create('div', 'info legend leaflet-legend');

            let labels = `<strong>Legenda</strong>
            <div class="d-flex gap-2">
                <div class="d-flex"><div style="width:15px;height:15px;border:1px solid #FFD93F;background-color: #ffd93f54;"></div>&nbsp;<span>Sedang Menanam</span></div>
                <div class="d-flex"><div style="width:15px;height:15px;border:1px solid #3792E2;background-color: #3792E254;"></div>&nbsp;<span>Masa Pemeliharaan</span></div>
            </div>`
            // labels.push(
            //     '<i class="bx bxs-circle" style="color:#8cd464;"></i> Marker Group');

            div.innerHTML = labels;
            return div;
        };
        legend.addTo(gardensMap);

        // TODO: add search in map
        var search = L.control({position: 'topleft'});
        search.onAdd = function (map) {

            var div = L.DomUtil.create('div');

            div.innerHTML = ``;

            div.innerHTML += `
                <div class="input-group">
                    <span class="input-group-text text-dark"
                        style="cursor: pointer;">
                        <i class="bx bx-search"></i>
                    </span>
                    <input type="text" class="form-control shadow-none"
                        placeholder="Cari nama lokasi..." aria-label="Cari nama lokasi..." id="map-search-markers" name="search" />
                </div>`;
            return div;
        };
        search.addTo(gardensMap);

        // TODO: add detail land
        // let land = L.control({position: 'topleft'});
        // land.onAdd = function (map) {

        //     let div = L.DomUtil.create('div', 'info land leaflet-detail-land');

        //     let labels = `<h5><b>Informasi Detail</b></h5>
        //             <div class="d-flex flex-column align-items-start gap-2 mb-3">
        //                 <span><span class="fw-bold">Luas Lahan</span>: <span class="fw-lighter">1,286 m2</span></span>
        //                 <span><span class="fw-bold">Kondisi Tanah</span>: <span class="fw-lighter">pH:6 N:6 P:6 K:6</span></span>
        //                 <span><span class="fw-bold">Fase</span>: <span class="fw-lighter">Masa Pemeliharaan</span></span>
        //                 <span><span class="fw-bold">Lokasi</span>: <span class="fw-lighter">Bandung, Indonesia</span></span>
        //                 <span><span class="fw-bold">Kontak</span>: <span class="fw-lighter">628976281676</span></span>
        //             </div>`

        //     div.innerHTML = labels;
        //     return div;
        // };
        // land.addTo(gardensMap);

        var hasilSearch = L.control({position: 'topleft'});
        hasilSearch.onAdd = function (map) {

            var div = L.DomUtil.create('div');

            div.id = "hasil-search"

            return div;
        };
        hasilSearch.addTo(gardensMap);

        // Group marker
        var markerGroup = L.markerClusterGroup();

        // TODO: Get marker from api
        const getGardens = async () => {
            try {
                const settings = {
                    method: 'GET',
                    headers: {
                        'x-csrf-token': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                }

                const [data, error] = await yourRequest("{{ route('bitanic.dashboard-admin.get-lands') }}", settings)

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

                console.log(data);

                let color = '#57D38C'

                data.forEach(land => {
                    switch (land.use_garden.harvest_status) {
                        case "1":
                            color = "#FFD93F"
                            break;
                        case "2":
                            color = "#3792E2"
                            break;
                    }
                    L.polygon(land.polygon, {color: color})
                        .on('click', function(e) {
                            // gardenShow(garden.use_garden.id)
                            console.log(e);
                            showDetailLand()
                        })
                        .addTo(gardensMap);
                });

                // const gardens = await data.gardens

                // let locMarker
                // const colors = [
                //     config.colors.primary,
                //     config.colors.info,
                //     config.colors.warning,
                //     config.colors.danger
                // ]

                // gardens.forEach(garden => {
                //     locMarker = L.marker([garden.latitude, garden.longitude])
                    // .on('click', function(e) {
                    //     gardenShow(garden.use_garden.id)
                    // })
                //     .bindPopup(garden.name)
                //     .on('mouseover', function(e) {
                //         this.openPopup();
                //     })
                //     .on('mouseout', function(e) {
                //         this.closePopup();
                //     })

                //     gardensMarker.push({
                //         id: garden.id,
                //         marker: locMarker
                //     })

                //     markerGroup.addLayer(locMarker)
                // });

                // gardensMap.addLayer(markerGroup);


            } catch (error) {
                console.log(error);
            }
        }

        const showDetailLand = () => {
            let eDetailLand = document.querySelector('.leaflet-detail-land')

            eDetailLand.style.left = "10px";
        }

        const closeDetailLand = () => {
            let eDetailLand = document.querySelector('.leaflet-detail-land')

            eDetailLand.style.left = "-100%";
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

                const garden = await data.garden

                document.getElementById('modalDetailGardenTitle').innerText = garden.name

                document.getElementById('modal-detail-name').innerHTML = garden.land.name
                document.getElementById('modal-detail-type').innerHTML = garden.gardes_type
                document.getElementById('modal-detail-area').innerHTML = garden.land.area + " hektar"
                document.getElementById('modal-detail-lat').innerHTML = garden.land.latitude
                document.getElementById('modal-detail-lng').innerHTML = garden.land.longitude
                document.getElementById('modal-detail-alt').innerHTML = garden.land.altitude + " mdpl"
                document.getElementById('modal-detail-date-created').innerHTML = garden.date_created
                document.getElementById('modal-detail-crop').innerHTML = garden.crop.crop_name
                document.getElementById('modal-detail-device').innerHTML = garden.device.device_series
                document.getElementById('modal-detail-address').innerHTML = garden.land.address

                document.getElementById('modal-detail-temperature').innerHTML = (garden.temperature ?? '-') + " °C"
                document.getElementById('modal-detail-moisture').innerHTML = (garden.moisture ?? '-') + "%"
                document.getElementById('modal-detail-nitrogen').innerHTML = (garden.nitrogen ?? '-') + "kg"
                document.getElementById('modal-detail-phosphor').innerHTML = (garden.phosphor ?? '-') + "kg"
                document.getElementById('modal-detail-kalium').innerHTML = (garden.kalium ?? '-') + "kg"

            } catch (error) {
                console.log(error);
            }
        }

        // executed after 700 milisecond
        function doneTyping () {
            let newData = {
                'category' : $('.kategori-button.active').data('id'),
                'key' : $('#searchInput').val()
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

                const [data, error] = await yourRequest(url+"?search="+value, settings)

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
                        <a href="javascript:void(0);" data-lat="${garden.lat}" data-lng="${garden.lng}"
                            class="bg-white list-group-item list-group-item-action" data-id="${garden.id}"
                            >${garden.name}</a
                        >`;
                })

                document.querySelector('#hasil-search').innerHTML = `
                    <div class="list-group">
                        ${list}
                    </div>`

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
        function randomColor(){
            var randomColor = "#" + (Math.floor(Math.random()*16777215).toString(16));
            return randomColor;
            // document.getElementById("box").style.backgroundColor = randomColor;
        };

        function debounce(func, timeout = 300){
            let timer;
            return (...args) => {
                clearTimeout(timer);
                timer = setTimeout(() => { func.apply(this, args); }, timeout);
            };
        }

        document.addEventListener("DOMContentLoaded", () => {
            console.log("Hello World!");

            //delete loading after page loaded
            deleteSpinner()

            getGardens()

            document.querySelector('#btn-close-detail-land').addEventListener('click', e => {
                closeDetailLand()
            })

            const searchProses = debounce((e) => searchKebun(e.target.value), 750)

            document.querySelector('#map-search-markers').addEventListener('keyup', searchProses)

            document.querySelector('#hasil-search').addEventListener('click', e => {
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
