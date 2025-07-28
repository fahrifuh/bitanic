<x-app-layout>

    @push('styles')
        {{-- Cluster --}}
        <link rel="stylesheet" href="{{ asset('css/MarkerCluster.css') }}">
        <link rel="stylesheet" href="{{ asset('css/MarkerCluster.Default.css') }}">
        <link rel="stylesheet" href="{{ asset('leaflet/leaflet.css') }}">
        <link rel="stylesheet" href="{{ asset('css/extend.css') }}">
        <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
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
                width: calc(100% - 100px);
                /* Adjust the width as desired */
                object-fit: cover;
                aspect-ratio: 4/3;
                border: 1px solid #9f999975;
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
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">
                Master
                @if (Auth::user()->role == 'admin')
                    / <a href="{{ route('bitanic.farmer.index') }}">Data Pengguna Bitanic Pro</a>
                    / <a
                    href="{{ route('bitanic.farmer.show', $farmer->user_id) }}">{{ $farmer->full_name }}</a>
                @else
                    / {{ $farmer->full_name }}
                @endif
                / <a href="{{ route('bitanic.land.index', $farmer->id) }}">Data Lahan</a>
                / <a href="{{ route('bitanic.land.show', ['farmer' => $farmer->id, 'land' => $land->id]) }}">{{ $land->name }}</a>
                / <a
                    href="{{ route('bitanic.garden.index', ['farmer' => $farmer->id, 'land' => $land->id]) }}">Data Kebun</a>
                /
            </span>
            Tambah Kebun
        </h4>
    </x-slot>
    {{-- End Header --}}

    <div class="row">
        <div class="col-md-12">
            <!-- Striped Rows -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('bitanic.garden.store', ['farmer' => $farmer->id, 'land' => $land->id]) }}" method="POST"
                        id="form-product" enctype="multipart/form-data">
                        @csrf
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <div id="myMap"></div>
                                <input type="hidden" name="polygon" id="data-input-polygon" />
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
                                                        <label for="" class="fw-bold">Latitude,
                                                            Longitude</label>
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
                                            <img class="card-img card-img-right"
                                                src="{{ asset('theme/img/elements/17.jpg') }}" alt="Card image"
                                                id="img-land" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-12 col-md-2">
                                <label for="data-input-alamat" class="form-label">Warna Polygon</label>
                                <input class="form-control" id="data-input-color" type="color" name="color" value="#666EE8" />
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-12 col-md-6">
                              <label for="data-input-name" class="form-label">Nama Kebun</label>
                              <input type="text" id="data-input-name" class="form-control" name="name" value="{{ old('name') }}" />
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="data-input-area" class="form-label">Luas</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" class="form-control" id="data-input-area" name="area" placeholder="0" aria-label="0" aria-describedby="basic-addon13" />
                                    <span class="input-group-text" id="basic-addon13">m²</span>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col mb-0">
                                <label for="data-input-kategori" class="form-label">kategori</label>
                                <select class="form-select" id="data-input-kategori" name="category"
                                    aria-label="Default select example">
                                    <option value="urban">Urban</option>
                                    <option value="rural">Rural</option>
                                </select>
                            </div>
                            <div class="col mb-0">
                                <label for="data-input-tipe" class="form-label">Tipe</label>
                                <select class="form-select" id="data-input-tipe" name="gardes_type"
                                    aria-label="Default select example">
                                    <option value="tradisional">Tradisional</option>
                                    <option value="hidroponik">Hidroponik</option>
                                    <option value="aquaponik">Aquaponik</option>
                                    <option value="vertical">Vertical</option>
                                    <option value="green_house">Green House</option>
                                </select>
                            </div>
                        </div>
                        <div class="row g-2 mb-3 d-none" id="form-hydroponic">
                            <div class="col mb-0">
                                <label for="data-input-levels" class="form-label">Jumlah Pipa</label>
                                <input type="number" min="0" id="data-input-levels" class="form-control"
                                    name="pipa" value="{{ old('pipa') }}" />
                            </div>
                            <div class="col mb-0">
                                <label for="data-input-holes" class="form-label">Jumlah lubang per Pipa</label>
                                <input type="number" min="0" id="data-input-holes" class="form-control"
                                    name="lubang_pipa" value="{{ old('lubang_pipa') }}" />
                            </div>
                        </div>
                        <div class="row g-4 mb-3 d-none" id="form-aquaponic">
                            <div class="col mb-0">
                                <label for="data-input-length" class="form-label">Panjang Kolam</label>
                                <div class="input-group">
                                    <input type="number" min="0" step="0.01" id="data-input-length"
                                        class="form-control" name="length" value="{{ old('length') }}"
                                        placeholder="0" aria-label="0" aria-describedby="addon-length" />
                                    <span class="input-group-text" id="addon-length">m</span>
                                </div>
                            </div>
                            <div class="col mb-0">
                                <label for="data-input-width" class="form-label">Lebar Kolam</label>
                                <div class="input-group">
                                    <input type="number" min="0" step="0.01" id="data-input-width"
                                        class="form-control" name="width" value="{{ old('width') }}"
                                        placeholder="0" aria-label="0" aria-describedby="addon-width" />
                                    <span class="input-group-text" id="addon-width">m</span>
                                </div>
                            </div>
                            <div class="col mb-0">
                                <label for="data-input-height" class="form-label">Tinggi Kolam</label>
                                <div class="input-group">
                                    <input type="number" min="0" step="0.01" id="data-input-height"
                                        class="form-control" name="height" value="{{ old('height') }}"
                                        placeholder="0" aria-label="0" aria-describedby="addon-height" />
                                    <span class="input-group-text" id="addon-height">m</span>
                                </div>
                            </div>
                            <div class="col mb-0">
                                <label for="data-input-fish-type" class="form-label">Jenis Ikan dalam Kolam</label>
                                <input type="text" id="data-input-fish-type" class="form-control"
                                    name="fish_type" value="{{ old('fish_type') }}" />
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col">
                                <label for="data-input-perangkat-id" class="form-label">Perangkat</label>
                                <select class="form-select" id="data-input-perangkat-id" name="device_id"
                                    aria-label="Default select example">
                                    <option value="">-- Pilih Perangkat --</option>
                                    @forelse ($devices as $id => $series)
                                        <option value="{{ $id }}">{{ $series }}</option>
                                    @empty
                                        <option disabled>Tidak Perangkat</option>
                                    @endforelse
                                </select>
                            </div>
                            <div class="col">
                                <label for="data-input-tgl-dibuat" class="form-label">Tanggal Dibuat</label>
                                <input type="date" class="form-control" id="data-input-tgl-dibuat"
                                    name="date_created" value="{{ old('date_created') }}" />
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                          <div class="col-12 col-md-3">
                            <label for="" class="form-label">Foto</label>
                            <img src="{{ asset('bitanic-landing/default-image.jpg') }}" alt="preview-img" class="preview-image img-thumbnail">
                          </div>
                          <div class="col-12 col-md-9">
                            <label for="data-input-image" class="form-label">File Foto</label>
                            <input class="form-control" type="file" id="data-input-image" name="image" accept="image/png, image/jpg, image/jpeg"
                                aria-describedby="pictureHelp" />
                            <div id="pictureHelp" class="form-text">Format gambar JPG, JPEG, PNG. Maks.
                                2MB</div>
                          </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary float-end"
                                    id="submit-btn">Simpan</button>
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
        <script src="{{ asset('js/select2.min.js') }}"></script>
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
                lng: 107.72491693496704,
                polygon: [],
                layerPolygon: null
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

            var editableLayers = new L.FeatureGroup();
            map.addLayer(editableLayers);

            var optionDraw = {
                position: 'topright',
                draw: {
                    polyline: false,
                    polygon: {
                        allowIntersection: false, // Restricts shapes to simple polygons
                        drawError: {
                            message: '<strong>Oh snap!<strong> you can\'t draw that!' // Message that will show when intersect
                        },
                    },
                    circle: false, // Turns off this drawing tool
                    circlemarker: false, // Turns off this drawing tool
                    rectangle: false,
                    marker: false
                },
                edit: {
                    featureGroup: editableLayers, //REQUIRED!!
                    remove: true
                },
            };

            var drawControl = new L.Control.Draw(optionDraw);
            map.addControl(drawControl);

            var getPopupContent = function(layer) {
                // Marker - add lat/long
                if (layer instanceof L.Marker || layer instanceof L.CircleMarker) {
                    return strLatLng(layer.getLatLng());
                // Circle - lat/long, radius
                } else if (layer instanceof L.Circle) {
                    var center = layer.getLatLng(),
                        radius = layer.getRadius();
                    return "Center: "+strLatLng(center)+"<br />"
                            +"Radius: "+_round(radius, 2)+" m";
                // Rectangle/Polygon - area
                } else if (layer instanceof L.Polygon) {
                    var ll = layer._defaultShape ? layer._defaultShape() : layer.getLatLngs(),
                        area = L.GeometryUtil.geodesicArea(ll);
                    luasPolygon = area.toLocaleString();
                    document.querySelector('#data-input-area').value = area.toFixed(2)
                    return "Area: "+L.GeometryUtil.readableArea(area, true);
                // Polyline - distance
                } else if (layer instanceof L.Polyline) {
                    var ll = layer._defaultShape ? layer._defaultShape() : layer.getLatLngs(),
                        distance = 0;
                    if (ll.length < 2) {
                        return "Distance: N/A";
                    } else {
                        for (var i = 0; i < ll.length-1; i++) {
                            distance += ll[i].distanceTo(ll[i+1]);
                        }
                        return "Distance: "+_round(distance, 2)+" m";
                    }
                }
                return null;
            };

            map.on(L.Draw.Event.CREATED, function (e) {
                let type = e.layerType,
                    layer = e.layer;

                if (type === 'polygon') {
                    stateData.polygon = layer.getLatLngs()[0];
                    layer.setStyle({
                        dashArray: '10, 10',
                        dashOffset: '20',
                        color: document.querySelector('#data-input-color').value
                    })

                    fillPolygon(stateData.polygon.map((val, _) => [val.lat, val.lng]))
                    stateData.layerPolygon = layer
                }

                if(editableLayers && editableLayers.getLayers().length!==0){
                    editableLayers.clearLayers();
                }

                let content = getPopupContent(layer);
                if (content !== null) {
                    layer.bindPopup(content);
                }

                editableLayers.addLayer(layer);
            });

            map.on('draw:edited', function (e) {
                let layers = e.layers;
                layers.eachLayer(function (layer) {
                    stateData.polygon = JSON.stringify(layer.getLatLngs());
                    let content = getPopupContent(layer);
                    if (content !== null) {
                        layer.setPopupContent(content);
                        stateData.layerPolygon = layer
                    }
                });
            });

            map.on('draw:deleted', function (e) {
                stateData.polygon = ''
                stateData.layerPolygon = null
            });

            const btnSubmit = document.getElementById('submit-btn')
            btnSubmit.addEventListener('submit', e => {
                // Show loading indication
                btnSubmit.setAttribute('data-kt-indicator', 'on');

                // Disable button to avoid multiple click
                btnSubmit.disabled = true;

                // document.getElementById('form-product').submit()
            })

            function showHideHydroponic(hide = false) {
                const formHydroponic = document.getElementById('form-hydroponic')

                if (formHydroponic.classList.contains('d-none')) {
                    formHydroponic.classList.remove('d-none')
                }

                if (hide == true) {
                    formHydroponic.classList.add('d-none')
                }
            }

            function showHideAquaponic(hide = false) {
                const formAquaponic = document.getElementById('form-aquaponic')

                if (formAquaponic.classList.contains('d-none')) {
                    formAquaponic.classList.remove('d-none')
                }

                if (hide == true) {
                    formAquaponic.classList.add('d-none')
                }
            }

            function showHideRowPolygon(hide = false) {
                const rowPolygon = document.getElementById('row-polygon')

                if (rowPolygon.classList.contains('d-none')) {
                    rowPolygon.classList.remove('d-none')
                }

                if (hide == true) {
                    rowPolygon.classList.add('d-none')

                    document.getElementById('polygon-switch').checked = false

                    polygon.remove()
                    latlngs = []
                }
            }

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

                    let url = "{{ route('bitanic.land.get-land', ['farmer' => $farmer->id, 'land' => 'ID']) }}"

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
                    //     <span class="badge bg-info flex-fill">${land.name}</span>
                    //     <span class="badge bg-warning flex-fill">Luas: ${land.area} m²</span>
                    // </div>`, {
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
                            color: "#" + land.color + "55",
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

            function fillPolygon(polygon) {
                document.getElementById('data-input-polygon').value = JSON.stringify(polygon)
            }

            function eventFile(input) {
                // Validate
                if (input.files && input.files[0]) {
                    let fileSize = input.files[0].size / 1024 / 1024; //MB Format
                    let fileType = input.files[0].type;

                    // validate size
                    if(fileSize > 10){
                        showAlert('Ukuran File tidak boleh lebih dari 2mb !');
                        input.value = '';
                        return false;
                    }

                    // validate type
                    if(["image/jpeg", "image/jpg", "image/png"].indexOf(fileType) < 0){
                        showAlert('Format File tidak valid !');
                        input.value = '';
                        return false;
                    }

                    let reader = new FileReader();

                    reader.onload = function(e) {
                        document.querySelector('.preview-image').setAttribute('src', e.target.result)
                    }

                    reader.readAsDataURL(input.files[0]); // convert to base64 string
                }
            }


            const getGardensPolygon = async () => {
                try {
                    const settings = {
                        method: 'GET',
                        headers: {
                            'x-csrf-token': '{{ csrf_token() }}'
                        },
                    }

                    const [data, error] = await yourRequest(
                        "{{ route('web.gardens.get', ['farmer' => $farmer->id]) }}", settings)

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

                    const gardens = data.gardens

                    gardensMarker = gardens

                    let polygonGardens = []

                    gardens.forEach(garden => {
                        let land = garden.land

                        if (garden.polygon) {
                            polygonGardens.push(
                                L.polygon(garden.polygon, {
                                    color: "#" + garden.color,
                                    dashArray: '10, 10',
                                    dashOffset: '20',
                                })
                                .bindPopup(garden.name)
                                // .on('click', function(e) {
                                //     gardenShow(garden.id)
                                // })
                            )
                        }
                    });

                    L.layerGroup(polygonGardens)
                    .addTo(map)

                } catch (error) {
                    console.log(error);
                }
            }

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

                //   $('.select2-active').select2();
                $('#data-input-tanaman-id').select2({
                    placeholder: "Pilih Tanaman",
                });
                $('#data-input-perangkat-id').select2({
                    placeholder: "Pilih Perangkat",
                });

                document.getElementById('data-input-tipe').addEventListener('change', e => {

                    switch (e.target.value) {
                        case 'hidroponik':
                            showHideHydroponic()

                            showHideAquaponic(true)

                            showHideRowPolygon(true)
                            break;
                        case 'aquaponik':
                            showHideHydroponic()

                            showHideAquaponic()

                            showHideRowPolygon(true)
                            break;

                        default:
                            showHideHydroponic(true)
                            showHideAquaponic(true)

                            showHideRowPolygon()
                            break;
                    }

                })

                document.querySelector('#data-input-color').addEventListener('change', e => {
                    console.dir(e.target.value)
                    stateData.layerPolygon?.setStyle({
                        color: e.target.value
                    })
                })

                getLand(parseInt("{{ $land->id }}"))

                getGardensPolygon()

                map.doubleClickZoom.disable();

                // Fill form
                function fillForm() {
                    $(`#data-input-lat`).val(stateData.lat);
                    $(`#data-input-lng`).val(stateData.lng);
                }

                // Handle File upload
                document.querySelector('#data-input-image').addEventListener('change', e => {
                    if (e.target.files.length == 0) {
                        // $('.profile').attr('src', defaultImage);
                    } else {
                        eventFile(e.target);
                    }
                })
            })
        </script>
    @endpush
</x-app-layout>
