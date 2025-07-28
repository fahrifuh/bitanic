<x-app-layout>
    @push('styles')
        {{-- Cluster --}}
        <link rel="stylesheet" href="{{ asset('css/MarkerCluster.css') }}">
        <link rel="stylesheet" href="{{ asset('css/MarkerCluster.Default.css') }}">
        <link rel="stylesheet" href="{{ asset('leaflet/leaflet.css') }}">
        <style>
            #myMap {
                height: 250px;
            }

            #modal-map {
                height: 450px;
            }

            #gardensMap {
                height: 500px;
            }

            .leaflet-legend {
                background-color: #f5f5f9;
                border-radius: 10%;
                padding: 10px;
                color: #3e8f55;
                box-shadow: 4px 3px 5px 5px #8d8989a8;
            }

            .event-none {
                pointer-events: none;
            }
        </style>
    @endpush

    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">
                Master
                @if (Auth::user()->role == 'admin')
                    / <a href="{{ route('bitanic.farmer.index') }}">Data Petani</a>
                    / <a
                        href="{{ route('bitanic.farmer.show', $farmer->user_id) }}">{{ $farmer->full_name }}</a>
                @else
                    / {{ $farmer->full_name }}
                @endif
                /
                </span>
            Data Lahan
        </h4>
    </x-slot>
    {{-- End Header --}}

    <div class="row">
        <div class="col-12 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="rounded" id="gardensMap"></div>
                    <div class="bg-white border p-2 mt-2 rounded">
                        <h5>Legenda</h5>
                        <span><i class='bx bxs-map text-info'></i>&nbsp;Lahan</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <!-- Striped Rows -->
            <div class="card">
                <div class="row">
                    <div class="col-md-12">
                        <!-- Search -->
                        <!-- /Search -->
                        <div class="float-end m-3">
                            <a href="{{ route('bitanic.land.create', $farmer->id) }}" class="btn btn-primary"
                                title="Tambah Data">
                                <i class="bx bx-plus"></i>&nbsp;Tambah
                            </a>
                        </div>
                    </div>
                </div>
                <div class="table-responsive text-wrap">
                    <table class="table table-striped" id="table-telemetri">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Lahan</th>
                                <th>Luas</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($lands as $land)
                                <tr>
                                    <td>{{ ($lands->currentPage() - 1) * $lands->perPage() + $loop->iteration }}
                                    </td>
                                    <td>{{ $land->name }}</td>
                                    <td>{{ $land->area }}&nbsp;m²</td>
                                    <td class="d-flex gap-1">
                                        <a href="{{ route('bitanic.land.show', ['farmer' => $farmer->id, 'land' => $land->id]) }}"
                                            class="btn btn-info btn-icon btn-sm" title="Detail Lahan">
                                            <i class="bx bx-list-ul"></i>
                                        </a>
                                        <a href="{{ route('bitanic.land.edit', ['farmer' => $farmer->id, 'land' => $land->id]) }}"
                                            class="btn btn-warning btn-icon btn-sm" title="Edit Lahan">
                                            <i class="bx bx-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-icon btn-sm btn-delete"
                                            data-id="{{ $land->id }}" title="Hapus Lahan">
                                            <i class="bx bx-trash event-none"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                {{ $lands->links() }}
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
            <!--/ Striped Rows -->
        </div>
    </div>

    @include('bitanic.stick-telemetri._modal-map')

    @push('scripts')
        <script src="{{ asset('leaflet/leaflet.js') }}"></script>
        <script src="{{ asset('js/leaflet.markercluster-src.js') }}"></script>
        <script>
            const detailModal = new bootstrap.Modal(document.getElementById("modalMap"), {});

            let latlngs = [];
            let gardensMarker = [];
            let gardensPolygon = [];
            let marker, polygon;
            const days = ['minggu', 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];

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
            const modalMap = L.map('modal-map', {
                preferCanvas: true,
                layers: [googleStreetsSecond],
                zoomControl: true
            }).setView([-6.869080223722067, 107.72491693496704], 12);

            // Layer MAP
            const gardensMap = L.map('gardensMap', {
                preferCanvas: true,
                layers: [googleStreetsThird],
                zoomControl: true
            }).setView([-6.869080223722067, 107.72491693496704], 10);

            const markerGroup = L.markerClusterGroup();

            async function handleDeleteRows(data) {
                const result = await Swal.fire({
                    text: "Menghapus data tidak dapat dibatalkan, dan semua data yang berhubungan akan hilang",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Yes, delete!",
                    cancelButtonText: "No, cancel",
                    customClass: {
                        confirmButton: "btn fw-bold btn-danger",
                        cancelButton: "btn fw-bold btn-active-light-primary"
                    }
                })

                if (result.value) {
                    showSpinner()
                    // Simulate delete request -- for demo purpose only
                    const url = "#".replace(
                        'ID', data.id)
                    const settings = {
                        method: 'DELETE',
                        headers: {
                            'x-csrf-token': '{{ csrf_token() }}'
                        },
                    }

                    const [res, error] = await yourRequest(url, settings)

                    deleteSpinner()

                    if (error) {
                        // Remove loading indication
                        submitButton.removeAttribute('data-kt-indicator');

                        // Enable button
                        submitButton.disabled = false;

                        if ("messages" in error) {
                            let errorMessage = ''

                            myModal.toggle()

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

                    // Remove loading indication
                    submitButton.removeAttribute('data-kt-indicator');

                    // Enable button
                    submitButton.disabled = false;

                    Swal.fire({
                        text: "Kamu berhasil menghapus data " + data.name + "!",
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "Ok",
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        }
                    }).then(function() {
                        // delete row data from server and re-draw datatable
                        window.location.reload();
                    });
                }

            }

            const getAllTelemetries = async () => {
                try {
                    const settings = {
                        method: 'GET',
                        headers: {
                            'x-csrf-token': '{{ csrf_token() }}'
                        },
                    }

                    const [data, error] = await yourRequest(
                        "{{ route('bitanic.land.get-lands', ['farmer' => $farmer->id]) }}", settings)

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

                    const lands = data

                    // gardensMarker = gardens

                    let locMarker, locPoly
                    const colors = [
                        config.colors.primary,
                        config.colors.info,
                        config.colors.warning,
                        config.colors.danger
                    ]

                    lands.forEach(land => {
                        locMarker = L.marker([land.latitude, land.longitude])
                            .bindPopup(
                                `<div class="d-flex flex-column gap-2">
                                <span class="badge bg-info flex-fill">${land.name}</span>
                                <span class="badge bg-warning flex-fill">Luas: ${land.area} m²</span>
                            </div>
                            <div class="mt-2">
                                Klik marker untuk melihat detail
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
                            .on('click', function(e) {
                                viewGardenMap(land.id)
                                // deleteAction(land.id)
                            })
                        markerGroup.addLayer(locMarker)

                        locPoly = L.polygon(land.polygon, {
                                color: '#' + land.color
                            })
                            .on('mouseover', function(e) {
                                this.openPopup();
                            })
                            .on('mouseout', function(e) {
                                this.closePopup();
                            })

                        markerGroup.addLayer(locPoly)
                    });

                    gardensMap.addLayer(markerGroup);


                } catch (error) {
                    console.log(error);
                }
            }

            async function deleteAction(id) {
                const {
                    isConfirmed,
                    value
                } = await Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: `Data yang dihapus tidak dapat dikembalikan! Jika anda yakin Ketik 'HAPUS' untuk konfirmasi`,
                    input: 'text',
                    inputPlaceholder: 'Ketik disini',
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Konfirmasi',
                    showLoaderOnConfirm: true,
                    allowOutsideClick: () => !Swal.isLoading()
                })

                if (isConfirmed) {
                    const match = 'HAPUS';

                    if (value !== match) {
                        Swal.fire(
                            'Konfirmasi tidak valid'
                        )

                        return
                    }

                    const settings = {
                        method: 'DELETE',
                        headers: {
                            'x-csrf-token': '{{ csrf_token() }}'
                        },
                    }

                    let url = "{{ route('bitanic.land.destroy', ['farmer' => $farmer->id, 'land' => 'ID']) }}"

                    const [data, error] = await yourRequest(url.replace('ID', id), settings)

                    if (error) {
                        errorMessage(error)

                        return false
                    }

                    Swal.fire(
                        data?.message
                    )

                    window.location.reload()
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

            let stateData = {
                lat: -6.869080223722067,
                lng: 107.72491693496704
            };
            let defaultCoordinate = [-6.869080223722067, 107.72491693496704];
            // create a red polygon from an array of LatLng points

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

            const viewGardenMap = async (id) => {
                try {
                    const settings = {
                        method: 'GET',
                        headers: {
                            'x-csrf-token': '{{ csrf_token() }}'
                        },
                    }

                    detailModal.show()

                    detailAddSpinner('detail-modal')

                    const [data, error] = await yourRequest(
                        "{{ route('bitanic.stick-telemetri.get-telemetri', ['farmer' => $farmer->id, 'stik_telemetri' => 'ID']) }}"
                        .replace('ID', id), settings)

                    if (error) {
                        if ("messages" in error) {
                            detailDeleteSpinner('detail-modal')

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

                    if (polygon) {
                        polygon.remove()
                    }

                    detailDeleteSpinner('detail-modal')

                    let currentLatLong
                    // marker.remove()
                    let telemetri = data.data

                    if (data.type == 'npk') {
                        currentLatLong = [data.data.latitude, data.data.longitude]

                        marker.setLatLng(currentLatLong).setOpacity(1)

                        document.querySelector('#telemetri-n').textContent = telemetri.n
                        document.querySelector('#telemetri-p').textContent = telemetri.p
                        document.querySelector('#telemetri-k').textContent = telemetri.k
                        document.querySelector('#telemetri-latitude').textContent = telemetri.latitude
                        document.querySelector('#telemetri-longitude').textContent = telemetri.longitude
                        document.querySelector('#telemetri-id-perangkat').textContent = telemetri.id_perangkat
                        document.querySelector('#telemetri-id-pengukuran').textContent = telemetri.id_pengukuran
                        document.querySelector('#telemetri-area').textContent = telemetri.area
                        document.querySelector('#telemetri-temperature').textContent = telemetri.temperature + "°C"
                        document.querySelector('#telemetri-moisture').textContent = telemetri.moisture + "%"

                        document.querySelector('#btn-delete-data').setAttribute('data-id', telemetri.id)

                        document.querySelector('#table-luas').classList.add('d-none')
                        document.querySelector('#table-npk').classList.remove('d-none')
                    } else {
                        latlngs = data.polygon

                        polygon = L.polygon(latlngs, {
                            color: 'blue'
                        }).addTo(modalMap)

                        marker.setOpacity(0)

                        document.querySelector('#telemetri-area').textContent = telemetri.area

                        currentLatLong = latlngs[0]

                        document.querySelector('#table-npk').classList.add('d-none')
                        document.querySelector('#table-luas').classList.remove('d-none')
                    }

                    modalMap.setView(currentLatLong, 16)

                    // marker = L.marker([data.data.lat, data.data.lng]).addTo(modalMap);
                    // polygon.setLatLngs(latlngs)
                    // modalMap.fitBounds(polygon.getBounds())

                    setTimeout(() => {
                        modalMap.invalidateSize();
                    }, 1000);
                } catch (error) {
                    console.log(error);
                }
            }

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

                modalMap.doubleClickZoom.disable();

                // markerGroup.on('clusterclick', function (a) {
                //     console.log(a.layer.getBounds());
                // })

                modalMap.invalidateSize()

                getAllTelemetries()
                // getGardensPolygon()

                marker = L.marker(defaultCoordinate, {
                    draggable: false
                }).on('drag', function() {
                    stateData.lat = this.getLatLng().lat;
                    stateData.lng = this.getLatLng().lng;
                }).addTo(modalMap)

                polygon = L.polygon(latlngs, {
                    color: 'blue'
                })

                document.querySelector('#btn-delete-data').addEventListener('click', e => {
                    e.preventDefault()

                    if (e.target.dataset?.id) {
                        detailModal.hide()
                        deleteAction(e.target.dataset.id)
                    }
                })

                document.querySelector('#table-telemetri').addEventListener('click', (e) => {
                    if (e.target.classList.contains("btn-delete") && e.target.dataset?.id) {
                        e.preventDefault()
                        deleteAction(e.target.dataset.id)
                    }
                })

                // map.on('dblclick', function(e) {
                //     if (document.getElementById('polygon-switch').checked) {
                //         polygon.remove()
                //         latlngs.push([e.latlng.lat, e.latlng.lng])
                //         polygon = L.polygon(latlngs, {
                //             color: 'red'
                //         }).addTo(map);
                //     } else {
                //         marker.remove()
                //         stateData.lat = e.latlng.lat;
                //         stateData.lng = e.latlng.lng;

                //         marker = L.marker([stateData.lat, stateData.lng]).addTo(map).openPopup();
                //         fillForm();
                //     }
                // });

                // map.invalidateSize()
                // map.zoomControl.remove()

                // var searchLayer = L.geoJson().addTo(map);

                let zoomControl = null;


            });
        </script>
    @endpush
</x-app-layout>
