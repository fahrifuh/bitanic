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
        </style>
    @endpush
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master @if (Auth::user()->role == 'admin')
                    / <a href="{{ route('bitanic.farmer.index') }}">Data Pengguna Bitanic Pro</a>
                    / <a
                    href="{{ route('bitanic.farmer.show', $farmer->user_id) }}">{{ $farmer->full_name }}</a>
                    @else
                    / {{ $farmer->full_name }}
                @endif
                / <a href="{{ route('bitanic.land.index', $farmer->id) }}">Data Lahan</a>
                / <a href="{{ route('bitanic.land.show', ['farmer' => $farmer->id, 'land' => $land->id]) }}">{{ $land->name }}</a>
                /
            </span>
            Data Kebun</h4>
    </x-slot>
    {{-- End Header --}}

    @if (session()->has('success'))
        <x-alert-message class="alert-success">{{ session()->get('success') }}</x-alert-message>
    @endif
    @if (session()->has('failed'))
        <x-alert-message class="alert-alert">{{ session()->get('failed') }}</x-alert-message>
    @endif

    {{-- Map --}}

    <div class="row">
        <div class="col-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <div id="gardensMap"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3 d-none">
            <div class="card">
                <div class="card-body">
                    <div class="bg-info text-white p-3 rounded mb-3" role="alert">Klik marker untuk melihat detail
                        kebun!</div>
                    <div class="table-responsive text-wrap">
                        <table class="table table-bordered">
                            <tbody class="table-border-bottom-0">
                                <tr>
                                    <td class="text-start bg-info text-white" style="width: 25%;">Lahan</td>
                                    <td class="text-start detail-val" id="marker-detail-name"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">Tipe</td>
                                    <td class="text-start detail-val" id="marker-detail-type"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">Luas</td>
                                    <td class="text-start detail-val" id="marker-detail-area"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">Latitude</td>
                                    <td class="text-start detail-val text-break" id="marker-detail-lat"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">longitude</td>
                                    <td class="text-start detail-val text-break" id="marker-detail-lng"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">Altitude</td>
                                    <td class="text-start detail-val text-break" id="marker-detail-alt"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">Tanggal Dibuat</td>
                                    <td class="text-start detail-val" id="marker-detail-date-created"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">Tanaman</td>
                                    <td class="text-start detail-val" id="marker-detail-crop"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">Perangkat</td>
                                    <td class="text-start detail-val" id="marker-detail-device"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">Alamat</td>
                                    <td class="text-start detail-val" id="marker-detail-address"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- End Map --}}

    <div class="row">
        <div class="col-md-12">
            <!-- Striped Rows -->
            <div class="card">
                <div class="row">
                    <div class="col-md-12">
                        <div class="float-start">
                            <h5 class="card-header">Petani {{ $farmer->full_name }}</h5>
                        </div>
                        <div class="float-end m-3">
                            <a class="btn btn-primary" title="Tambah kebun"
                                href="{{ route('bitanic.garden.create', ['farmer' => $farmer->id, 'land' => $land->id]) }}">
                                <i class="bx bx-plus"></i>&nbsp;Tambah
                            </a>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="table-responsive text-wrap" style="height: 80vh;">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nama Lahan</th>
                                        <th>Luas Lahan</th>
                                        <th>Nama Kebun</th>
                                        <th>Luas Kebun</th>
                                        <th>Tanaman</th>
                                        <th>Status Kebun</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @forelse ($data as $garden)
                                        <tr>
                                            <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}
                                            </td>
                                            <td>{{ $garden->land->name }}</td>
                                            <td>{{ $garden->land->area }}&nbsp;m²</td>
                                            <td>{{ $garden->name ?? '-' }}</td>
                                            <td>{{ $garden->area }}&nbsp;m²</td>
                                            <td>{{ $garden->currentCommodity?->crop?->crop_name ?? '-' }}</td>
                                            <td class="text-center">
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
                                                    title="Klik untuk update status kebun"
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
                                            </td>
                                            <td>
                                                <div class="d-flex flex-row flex-wrap gap-2 align-items-center">
                                                    <a class="btn btn-sm btn-icon btn-warning"
                                                        href="{{ route('bitanic.garden.edit', ['farmer' => $farmer->id, 'land' => $land->id, 'garden' => $garden->id]) }}"
                                                        title="Edit Kebun">
                                                        <i class="bx bx-edit-alt"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-icon btn-danger"
                                                        onclick="handleDeleteRows({{ $garden }})" title="Hapus Kebun">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                    <div class="dropdown">
                                                        <button type="button" title="Aksi Lainnya"
                                                            class="btn btn-sm btn-icon btn-info dropdown-toggle hide-arrow"
                                                            data-bs-toggle="dropdown">
                                                            <i class="bx bx-dots-vertical-rounded"></i>
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            <a class="dropdown-item" href="{{ route('bitanic.garden.show', [
                                                                'farmer' => $farmer->id,
                                                                'land' => $land->id,
                                                                'garden' => $garden->id
                                                            ]) }}"><i
                                                                    class="bx bx-list-ul me-1"></i> Detail</a>
                                                            <a class="dropdown-item" href="javascript:void(0);"
                                                                data-bs-toggle="modal" data-bs-target="#modalFoto"
                                                                data-foto="{{ asset($garden->picture) }}"
                                                                data-input-id="{{ $garden->id }}"><i
                                                                    class="bx bx-image-alt me-1"></i> Foto</a>
                                                            <a class="dropdown-item" href="javascript:void(0);"
                                                                data-bs-toggle="modal" data-bs-target="#modalSpesifikasi"
                                                                onclick="viewSpesifikasi({{ $garden->device }})"><i
                                                                    class="bx bx-chip me-1"></i> Perangkat</a>
                                                            <a class="dropdown-item d-none"
                                                                href="{{ route('bitanic.telemetri.index', ['farmer' => $farmer->id, 'garden' => $garden->id]) }}"><i
                                                                    class="bx bx-chip me-1"></i> Telemetri</a>
                                                            <a class="dropdown-item" href="javascript:void(0);"
                                                                data-bs-toggle="modal" data-bs-target="#modalSchedules"
                                                                onclick="viewSchedules({{ $garden->id }}, '{{ $garden->name }}')"><i
                                                                    class="bx bx-edit-alt me-1"></i> Jadwal Pemupukan</a>
                                                            <a class="dropdown-item" href="javascript:void(0);"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#modalFertilizationList"
                                                                onclick="getFertilizationList({{ $garden->id }}, '{{ $garden->name }}')"><i
                                                                    class="bx bx-edit-alt me-1"></i> Riwayat Pemupukan</a>
                                                            <a class="dropdown-item d-none"
                                                                href="{{ route('bitanic.harvest-produce.index', [
                                                                    'farmer' => $farmer,
                                                                    'garden' => $garden->id,
                                                                ]) }}">
                                                                <i class="bx bxs-component me-1"></i> Hasil Panen
                                                            </a>
                                                            <a class="dropdown-item d-none" href="javascript:void(0);"
                                                                data-bs-toggle="modal" data-bs-target="#modalMap"
                                                                onclick="viewGardenMap({{ $garden->id }})"><i
                                                                    class="bx bxs-map me-1"></i> Peta</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">Tidak ada data</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <nav aria-label="Page navigation">
                                <ul class="pagination justify-content-center">
                                    {{ $data->links() }}
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
                <!--/ Striped Rows -->
            </div>
        </div>

        @include('bitanic.garden._modal-form')
        @include('bitanic.garden._modal-foto')
        @include('bitanic.garden._modal-peta')
        @include('bitanic.garden._modal-device')
        @include('bitanic.garden._modal-detail')
        @include('bitanic.garden._modal-fertilization')
        @include('bitanic.garden._modal-schedule')
        @include('bitanic.garden._modal-add-fertilization')
        @include('bitanic.garden._modal-edit-status-garden')
        @include('bitanic.garden._modal-finish-harvest')

        @push('scripts')
            <script src="{{ asset('leaflet/leaflet.js') }}"></script>
            <script src="{{ asset('js/leaflet.markercluster-src.js') }}"></script>
            <script>
                const myModal = new bootstrap.Modal(document.getElementById("modalForm"), {});
                const modalAddFertilization = new bootstrap.Modal(document.getElementById("modalAddFertilization"), {});
                const modalEditStatusGarden = new bootstrap.Modal(document.getElementById("modalEditStatusGarden"), {});
                const modalFinishHarvest = new bootstrap.Modal(document.getElementById("modalFinishHarvest"), {});
                const modalSpesifikasi = new bootstrap.Modal(document.getElementById("modalSpesifikasi"), {});
                const modal = document.getElementById('modalForm')
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
                const map = L.map('myMap', {
                    preferCanvas: true,
                    layers: [googleStreets],
                    zoomControl: true
                }).setView([-6.869080223722067, 107.72491693496704], 12);

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
                }).setView([-6.869080223722067, 107.72491693496704], 12);

                var legend = L.control({
                    position: 'bottomleft'
                });
                legend.onAdd = function(map) {

                    var div = L.DomUtil.create('div', 'info legend');

                    div.classList.add('leaflet-legend')

                    labels = ['<strong>Categories</strong>'];
                    labels.push(
                        '<i class="bx bxs-map" style="color:#3e92cf;"></i> Marker Kebun');
                    // labels.push(
                    //     '<i class="bx bxs-circle" style="color:#8cd464;"></i> Marker Group');

                    div.innerHTML = labels.join('<br>');
                    return div;
                };
                legend.addTo(gardensMap);


                var markerGroupGarden = L.markerClusterGroup();
                var markerGroupLand = L.markerClusterGroup();

                const editGardenStatus = (id, status) => {
                    document.querySelector('#status-' + status).checked = true
                    document.getElementById('btn-edit-status-garden').dataset['id'] = id
                }

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

                const getDevice = async (id = null) => {
                    try {
                        const settings = {
                            method: 'GET',
                            headers: {
                                'x-csrf-token': '{{ csrf_token() }}'
                            },
                        }

                        const [data, error] = await yourRequest(
                            "{{ route('web.get-device', ['farmer' => $farmer, 'garden' => 'ID']) }}".replace('ID', id),
                            settings)

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

                        const devices = data.data;
                        let element = `<option value="" selected disabled>-- Tidak ada perangkat --</option>`

                        if (devices.length > 0) {
                            element = `<option value="">-- Pilih Perangkat --</option>`
                            devices.forEach(device => {
                                element +=
                                    `<option value="${device.id}" ${id && device.garden_id == id ? 'selected' : ''}>${device.device_series} | ${device.device_name}</option>`
                            });
                        }

                        document.getElementById('data-input-perangkat-id').innerHTML = element
                    } catch (error) {
                        console.log(error);
                    }

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

                // Submit button handler
                const submitButton = document.getElementById('submit-btn');
                submitButton.addEventListener('click', async function(e) {
                    // Prevent default button action
                    e.preventDefault();

                    // Show loading indication
                    submitButton.setAttribute('data-kt-indicator', 'on');

                    // Disable button to avoid multiple click
                    submitButton.disabled = true;

                    showSpinner()

                    // Simulate form submission. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                    let url, formSubmited;
                    const editOrAdd = document.getElementById('data-input-id');
                    const formData = new FormData();
                    let myFoto = document.getElementById('data-input-foto').files[0];

                    if (typeof myFoto !== 'undefined') {
                        formData.append("picture", document.getElementById(
                            'data-input-foto').files[0])
                    }

                    formData.append("name", document.getElementById('data-input-name').value)
                    formData.append("owner", document.getElementById('data-input-pemilik').value)
                    formData.append("gardes_type", document.getElementById('data-input-tipe').value)
                    formData.append("category", document.getElementById('data-input-kategori').value)
                    formData.append("area", document.getElementById('data-input-luas').value)
                    formData.append("unit", document.getElementById('data-input-satuan').value)
                    formData.append("lat", document.getElementById('data-input-lat').value)
                    formData.append("lng", document.getElementById('data-input-lng').value)
                    formData.append("alt", document.getElementById('data-input-alt').value)
                    formData.append("date_created", document.getElementById('data-input-tgl-dibuat').value)
                    formData.append("estimated_harvest", document.getElementById('data-input-estimasi-panen').value)
                    formData.append("crop_id", document.getElementById('data-input-tanaman-id').value)
                    formData.append("device_id", document.getElementById('data-input-perangkat-id').value)
                    formData.append("address", document.getElementById('data-input-alamat').value)
                    formData.append("polygon", JSON.stringify(latlngs))

                    formData.append("temperature", document.getElementById('data-input-temperature').value)
                    formData.append("moisture", document.getElementById('data-input-moisture').value)
                    formData.append("nitrogen", document.getElementById('data-input-nitrogen').value)
                    formData.append("phosphor", document.getElementById('data-input-phosphor').value)
                    formData.append("kalium", document.getElementById('data-input-kalium').value)

                    if (editOrAdd.value != 'add') {
                        url = "{{ route('bitanic.garden.update', ['farmer' => $farmer, 'land' => $land->id, 'garden' => 'ID']) }}"
                            .replace('ID', document.getElementById('data-input-id').value);
                        formData.append("_method", 'PUT')
                    } else {
                        url = "{{ route('bitanic.garden.store', ['farmer' => $farmer, 'land' => $land->id]) }}"
                    }

                    const settings = {
                        method: 'POST',
                        headers: {
                            'x-csrf-token': '{{ csrf_token() }}'
                        },
                        body: formData
                    }

                    const [data, error] = await yourRequest(url, settings)

                    if (error) {
                        deleteSpinner()

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

                    window.location.reload();

                    // Remove loading indication
                    submitButton.removeAttribute('data-kt-indicator');

                    // Enable button
                    submitButton.disabled = false;
                });

                const btnAddFertilization = document.getElementById('btn-add-fertilization')
                btnAddFertilization.addEventListener('click', async function(e) {
                    // Prevent default button action
                    e.preventDefault();

                    let listDays = [];

                    document.querySelectorAll('.data-input-set-hari:checked').forEach(days => {
                        listDays.push(days.value)
                    });

                    // Show loading indication
                    btnAddFertilization.setAttribute('data-kt-indicator', 'on');

                    // Disable button to avoid multiple click
                    btnAddFertilization.disabled = true;

                    showSpinner()

                    // Simulate form submission. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                    let url, formSubmited;
                    const formData = new FormData();

                    formData.append("crop_name", document.getElementById('data-input-nama-tanaman').value)
                    formData.append("weeks", document.getElementById('data-input-set-minggu').value)
                    formData.append("set_time", document.getElementById('data-input-set-waktu').value)
                    formData.append("set_minute", document.getElementById('data-input-set-menit').value)
                    formData.append("days", listDays)

                    url = "{{ route('bitanic.fertilization.store', ['farmer' => $farmer, 'garden' => 'ID']) }}"
                        .replace('ID', document.getElementById('data-input-garden-id').value);

                    const settings = {
                        method: 'POST',
                        headers: {
                            'x-csrf-token': '{{ csrf_token() }}'
                        },
                        body: formData
                    }

                    const [data, error] = await yourRequest(url, settings)

                    modalAddFertilization.toggle()

                    if (error) {
                        deleteSpinner()

                        // Remove loading indication
                        btnAddFertilization.removeAttribute('data-kt-indicator');

                        // Enable button
                        btnAddFertilization.disabled = false;

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

                    // Remove loading indication
                    btnAddFertilization.removeAttribute('data-kt-indicator');

                    // Enable button
                    btnAddFertilization.disabled = false;
                });

                // btn picture
                const myModalPrev = new bootstrap.Modal(document.getElementById("modalFoto"), {});
                const modalFoto = document.getElementById('modalFoto')
                modalFoto.addEventListener('show.bs.modal', function(event) {
                    // Button that triggered the modal
                    const button = event.relatedTarget
                    // Extract info from data-bs-* attributes
                    // const recipient = button.getAttribute('data-bs-whatever')
                    const modalTitle = modalFoto.querySelector('.modal-title')
                    modalTitle.textContent = 'Foto Kebun'

                    for (let index = 0; index < button.attributes.length; index++) {
                        if (button.attributes[index].nodeName.includes('data-foto')) {
                            document.getElementById('iframe').src = button.attributes[index].nodeValue
                        }
                    }

                })

                async function changeHarvestStatus(data) {
                    const result = await Swal.fire({
                        text: "Mengubah status panen tidak dapat dibatalkan.",
                        icon: "info",
                        showCancelButton: true,
                        buttonsStyling: false,
                        confirmButtonText: "Ya, ubah!",
                        cancelButtonText: "Tidak, batalkan",
                        customClass: {
                            confirmButton: "btn fw-bold btn-success",
                            cancelButton: "btn fw-bold btn-active-light-primary"
                        }
                    })


                    if (result.value) {
                        showSpinner()

                        // Simulate delete request -- for demo purpose only
                        const url = "{{ route('bitanic.garden.change-status', ['farmer' => $farmer, 'land' => $land->id, 'garden' => 'ID']) }}".replace(
                            'ID', data.id)

                        const settings = {
                            method: 'POST',
                            headers: {
                                'x-csrf-token': '{{ csrf_token() }}'
                            },
                            body: {}
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
                            text: "Kamu berhasil mengubah data " + data.name + "!.",
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

                async function handleDeleteRows(data) {
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

                    if (result.value) {
                        showSpinner()
                        // Simulate delete request -- for demo purpose only
                        const url = "{{ route('bitanic.garden.destroy', ['farmer' => $farmer, 'land' => $land->id, 'garden' => 'ID']) }}".replace(
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

                const viewSpesifikasi = (device) => {
                    document.getElementById('btn-reset-device').dataset['id'] = device.id
                    document.getElementById('device-seri-perangkat').innerHTML = device.device_series
                    document.getElementById('device-versi').innerHTML = device.version
                    document.getElementById('device-tgl-produksi').innerHTML = device.production_date
                    document.getElementById('device-tgl-pembelian').innerHTML = device.purchase_date
                    document.getElementById('device-tgl-diaktifkan').innerHTML = device.activate_date
                    document.getElementById('device-status').innerHTML = device.status == 1 ? 'Aktif' : 'Belum Aktif'
                    let element = device.specification.length > 0 ? `` : `<tr>
                                <td colspan="2" class="text-center">Tidak ada data</td>
                            </tr>`

                    for (let i = 0; i < device.specification.length; i++) {
                        const specification = device.specification[i];

                        element += `<tr>
                                <td class="text-center">${specification.name}</td>
                                <td class="text-center">${specification.value}</td>
                            </tr>`
                    }

                    document.getElementById('view-spesifik').innerHTML = element
                }

                const viewGardenMap = async (id) => {
                    try {
                        const settings = {
                            method: 'GET',
                            headers: {
                                'x-csrf-token': '{{ csrf_token() }}'
                            },
                        }

                        const [data, error] = await yourRequest("{{ route('web.get-garden-polygon', ['id' => 'ID']) }}"
                            .replace('ID', id), settings)

                        if (error) {
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

                        polygon.remove()
                        marker.remove()

                        latlngs = data.data.polygon

                        marker = L.marker([data.data.lat, data.data.lng]).addTo(modalMap);
                        polygon = L.polygon(latlngs, {
                            color: 'red'
                        }).addTo(modalMap)
                        modalMap.setView([data.data.lat, data.data.lng], 12)

                        setTimeout(() => {
                            modalMap.invalidateSize();
                        }, 1000);
                    } catch (error) {
                        console.log(error);
                    }
                }

                const getGardenPolygon = async (id) => {
                    try {
                        const settings = {
                            method: 'GET',
                            headers: {
                                'x-csrf-token': '{{ csrf_token() }}'
                            },
                        }

                        const [data, error] = await yourRequest("{{ route('web.get-garden-polygon', ['id' => 'ID']) }}"
                            .replace('ID', id), settings)

                        if (error) {
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

                        latlngs = data.data.polygon

                        marker = L.marker([data.data.lat, data.data.lng]).addTo(map);
                        polygon = L.polygon(latlngs, {
                            color: 'red'
                        }).addTo(map)
                        map.setView([data.data.lat, data.data.lng], 12)

                        setTimeout(() => {
                            map.invalidateSize();
                        }, 1000);
                    } catch (error) {
                        console.log(error);
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

                        let locMarker, locPoly, gardenPoly

                        let isFirstLoop = true

                        gardens.forEach(garden => {
                            let land = garden.land
                            // locMarker = L.marker([land.latitude, land.longitude]).bindPopup(land.name)
                                // .on('click',
                            //     function(e) {
                            //         gardenShow(garden.id)
                            //     })

                            // locPoly = L.polygon(land.polygon, {
                            //     color: "#" + land.color + "55"
                            // })
                            if (garden.polygon) {
                                gardenPoly = L.polygon(garden.polygon, {
                                    color: "#" + garden.color,
                                    dashArray: '10, 10',
                                    dashOffset: '20',
                                })
                                .bindPopup(garden.name)
                                // .on('click', function(e) {
                                //     gardenShow(garden.id)
                                // })
                                markerGroupGarden.addLayer(gardenPoly)
                                if (isFirstLoop) {
                                    // gardensMap.fitBounds(gardenPoly.getBounds());
                                    isFirstLoop = false
                                }
                            }

                            // markerGroupLand.addLayer(locMarker)
                            // markerGroupLand.addLayer(locPoly)
                        });

                        // gardensMap.addLayer(markerGroupLand);
                        gardensMap.addLayer(markerGroupGarden);

                    } catch (error) {
                        console.log(error);
                    }
                }

                const gardenShow = async (id) => {

                    return 0
                    try {

                        detailAddSpinner('detail-val')

                        const settings = {
                            method: 'GET',
                            headers: {
                                'x-csrf-token': '{{ csrf_token() }}'
                            },
                        }

                        const [data, error] = await yourRequest(
                            "{{ route('bitanic.garden.show', ['farmer' => $farmer->id, 'land' => $land->id, 'garden' => 'ID']) }}"
                            .replace('ID', id), settings)

                        if (error) {
                            if ("messages" in error) {
                                detailDeleteSpinner('detail-val')

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

                        const land = garden.land

                        document.getElementById('marker-detail-name').innerHTML = land.name
                        document.getElementById('marker-detail-type').innerHTML = garden.gardes_type
                        document.getElementById('marker-detail-area').innerHTML = land.area + " m²"
                        document.getElementById('marker-detail-lat').innerHTML = land.latitude
                        document.getElementById('marker-detail-lng').innerHTML = land.longitude
                        document.getElementById('marker-detail-alt').innerHTML = land.altitude + " mdpl"
                        document.getElementById('marker-detail-date-created').innerHTML = garden.date_created
                        document.getElementById('marker-detail-crop').innerHTML = garden.crop.crop_name
                        document.getElementById('marker-detail-device').innerHTML = garden.device?.device_series ??
                            'Tidak ada perangkat'
                        document.getElementById('marker-detail-address').innerHTML = land.address

                    } catch (error) {
                        console.log(error);
                    }
                }

                const detailModal = new bootstrap.Modal(document.getElementById("modalDetail"), {});
                const gardenModal = async (id) => {
                    try {

                        detailAddSpinner('detail-modal')

                        const settings = {
                            method: 'GET',
                            headers: {
                                'x-csrf-token': '{{ csrf_token() }}'
                            },
                        }

                        const [data, error] = await yourRequest(
                            "{{ route('bitanic.garden.show', ['farmer' => $farmer->id, 'land' => $land->id, 'garden' => 'ID']) }}"
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

                        const garden = await data.garden

                        const land = garden.land

                        document.getElementById('modal-detail-name').innerHTML = land.name
                        document.getElementById('modal-detail-type').innerHTML = garden.gardes_type
                        document.getElementById('modal-detail-area').innerHTML = land.area + " m²"
                        document.getElementById('modal-detail-lat').innerHTML = land.latitude
                        document.getElementById('modal-detail-lng').innerHTML = land.longitude
                        document.getElementById('modal-detail-alt').innerHTML = land.altitude + " mdpl"
                        document.getElementById('modal-detail-date-created').innerHTML = garden.date_created
                        document.getElementById('modal-detail-crop').innerHTML = garden.crop.crop_name
                        document.getElementById('modal-detail-device').innerHTML = garden.device.device_series
                        document.getElementById('modal-detail-address').innerHTML = land.address

                        const bodyHydroponic = document.getElementById('body-hydroponic')
                        const bodyAquaponic = document.getElementById('body-aquaponic')

                        if (bodyHydroponic.classList.contains('d-none')) {
                            bodyHydroponic.classList.remove('d-none')
                        }

                        if (bodyAquaponic.classList.contains('d-none')) {
                            bodyAquaponic.classList.remove('d-none')
                        }

                        if (garden.gardes_type == 'hidroponik' || garden.gardes_type == 'aquaponik') {
                            // hydroponic && aquaponic
                            document.getElementById('modal-detail-levels').innerHTML = garden.levels
                            document.getElementById('modal-detail-holes').innerHTML = garden.holes
                            document.getElementById('modal-detail-total-pod').innerHTML = garden.levels * garden.holes

                            if (garden.gardes_type == 'aquaponik') {
                                // Aquaponic
                                document.getElementById('modal-detail-length').innerHTML = garden.length + " m"
                                document.getElementById('modal-detail-width').innerHTML = garden.width + " m"
                                document.getElementById('modal-detail-height').innerHTML = garden.height + " m"
                                document.getElementById('modal-detail-volume').innerHTML = (garden.length * garden.width *
                                    garden.height) + " m3"
                                document.getElementById('modal-detail-fish-type').innerHTML = garden.fish_type
                            } else {
                                bodyAquaponic.classList.add('d-none')
                            }
                        } else {
                            bodyHydroponic.classList.add('d-none')
                            bodyAquaponic.classList.add('d-none')
                        }

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

                let stateData = {
                    lat: -6.869080223722067,
                    lng: 107.72491693496704
                };
                let defaultCoordinate = [-6.869080223722067, 107.72491693496704];
                // create a red polygon from an array of LatLng points

                // fertilization
                const getFertilizationList = async (garden, gardenName) => {
                    try {
                        document.getElementById('fertilization-list').innerHTML = `<tr>
                                <td colspan="6" class="text-center"><div class="spinner-border text-info" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div></td>
                            </tr>`
                        let modalTitle = document.querySelector('#modalFertilizationListTitle')

                        modalTitle.textContent = "List Pemupukan Kebun " + gardenName

                        const settings = {
                            method: 'GET',
                            headers: {
                                'x-csrf-token': '{{ csrf_token() }}'
                            },
                        }

                        const url = "{{ route('web.fertilization-list', ['garden' => 'ID']) }}".replace('ID', garden)

                        const [data, error] = await yourRequest(url, settings)

                        if (error) {
                            if ("messages" in error) {
                                document.getElementById('fertilization-list').innerHTML = `<tr>
                                <td colspan="5" class="text-center">Error</td>
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

                        document.getElementById('data-input-garden-id').value = garden

                        let fertilizations = data.fertilizations

                        let element = fertilizations.length > 0 ? `` : `<tr>
                                <td colspan="6" class="text-center">Tidak ada data</td>
                            </tr>`
                        for (let i = 0; i < fertilizations.length; i++) {
                            let fertilization = fertilizations[i];
                            let listDay = []
                            const setDay = fertilization.set_day.split(",")

                            setDay.forEach((day, index) => {
                                if (day == 1) {
                                    listDay.push(days[index])
                                }
                            });

                            const status = fertilization.is_finished == 1 ?
                                `<span class="badge bg-label-success">Selesai</span>` :
                                `<span class="badge bg-label-warning">Sedang Berlangsung</span>`

                            element += `<tr style="cursor:pointer;">
                                <td class="text-center">${(i + 1)}</td>
                                <td class="text-center">${fertilization.crop_name}</td>
                                <td class="text-center">${fertilization.weeks}</td>
                                <td class="text-center">${listDay.join(', ')}</td>
                                <td class="text-center">${fertilization.created_at}</td>
                                <td class="text-center">${status}</td>
                            </tr>`
                        }

                        document.getElementById('fertilization-list').innerHTML = element

                        // eventRiwayat()
                    } catch (error) {
                        console.log(error);
                    }
                }

                const viewSchedules = async (gardenID, gardenName) => {
                    try {
                        let modalTitle = document.querySelector('#modalSchedulesTitle')
                        const elementLoading = `<div class="spinner-border text-info" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>`

                        modalTitle.textContent = "Kebun " + gardenName + " Telemetri"

                        document.getElementById('pemupukan-pengiriman-jadwal').innerHTML = elementLoading
                        document.getElementById('pemupukan-jenis-tanaman').innerHTML = elementLoading
                        document.getElementById('pemupukan-minggu').innerHTML = elementLoading
                        document.getElementById('pemupukan-set-hari').innerHTML = elementLoading
                        document.getElementById('pemupukan-set-waktu').innerHTML = elementLoading
                        document.getElementById('pemupukan-status-pompo-1').innerHTML = elementLoading
                        document.getElementById('pemupukan-status-pompo-2').innerHTML = elementLoading
                        document.getElementById('btn-status-pompa-1').classList.remove('d-none')
                        document.getElementById('btn-status-pompa-2').classList.remove('d-none')
                        document.getElementById('btn-status-pompa-1').dataset['garden'] = ''
                        document.getElementById('btn-status-pompa-1').dataset['status'] = ''
                        document.getElementById('btn-status-pompa-2').dataset['garden'] = ''
                        document.getElementById('btn-status-pompa-2').dataset['status'] = ''
                        document.getElementById('btn-kirim-setting').dataset['garden'] = ''
                        document.getElementById('btn-reset-perangkat').dataset['garden'] = ''
                        document.getElementById('btn-pemupukan-berhenti').dataset['garden'] = ''
                        document.getElementById('btn-kirim-setting').dataset['id'] = ''
                        document.getElementById('btn-reset-perangkat').dataset['id'] = ''
                        document.getElementById('btn-pemupukan-berhenti').dataset['id'] = ''
                        document.getElementById('view-schedules').innerHTML = `<tr>
                                <td colspan="4" class="text-center">${elementLoading}</td>
                            </tr>`

                        document.getElementById('btn-status-pompa-1').classList.remove('btn-secondary')
                        document.getElementById('btn-status-pompa-2').classList.remove('btn-secondary')
                        document.getElementById('btn-status-pompa-1').classList.remove('btn-success')
                        document.getElementById('btn-status-pompa-2').classList.remove('btn-success')
                        document.getElementById('btn-status-pompa-1').classList.remove('btn-danger')
                        document.getElementById('btn-status-pompa-2').classList.remove('btn-danger')

                        const settings = {
                            method: 'GET',
                            headers: {
                                'x-csrf-token': '{{ csrf_token() }}'
                            },
                        }

                        const url = "{{ route('web.fertilization', ['garden' => 'ID']) }}".replace('ID', gardenID)

                        const [data, error] = await yourRequest(url, settings)

                        document.getElementById('pemupukan-pengiriman-jadwal').innerHTML = `-`
                        document.getElementById('pemupukan-jenis-tanaman').innerHTML = `-`
                        document.getElementById('pemupukan-minggu').innerHTML = `-`
                        document.getElementById('pemupukan-set-hari').innerHTML = `-`
                        document.getElementById('pemupukan-set-waktu').innerHTML = `-`

                        document.getElementById('pemupukan-status-pompo-1').innerHTML = `-`
                        document.getElementById('pemupukan-status-pompo-2').innerHTML = `-`

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

                        let fertilization = data.fertilization
                        let device = data.device

                        if (device && device.status == 1) {
                            document.getElementById('btn-status-pompa-1').dataset['garden'] = gardenID
                            document.getElementById('btn-status-pompa-2').dataset['garden'] = gardenID

                            switch (device.status_motor_1) {
                                case 0:
                                    document.getElementById('btn-status-pompa-1').innerText = 'Aktifkan'
                                    document.getElementById('btn-status-pompa-1').classList.add('btn-success')
                                    document.getElementById('btn-status-pompa-1').dataset['status'] = 1
                                    document.getElementById('pemupukan-status-pompo-1').innerHTML =
                                        `<span class="text-danger">Tidak Aktif</span>`
                                    break;

                                case 1:
                                    document.getElementById('btn-status-pompa-1').innerText = 'Matikan'
                                    document.getElementById('btn-status-pompa-1').classList.add('btn-danger')
                                    document.getElementById('btn-status-pompa-1').dataset['status'] = 0
                                    document.getElementById('pemupukan-status-pompo-1').innerHTML =
                                        `<span class="text-info">Aktif</span>`
                                    break;

                                default:
                                    document.getElementById('btn-status-pompa-1').innerText = 'Menuggu response alat'
                                    document.getElementById('btn-status-pompa-1').classList.add('btn-secondary')
                                    document.getElementById('btn-status-pompa-1').dataset['status'] = ''
                                    document.getElementById('pemupukan-status-pompo-1').innerHTML =
                                        `<span class="text-warning">-</span>`
                                    break;
                            }
                            switch (device.status_motor_2) {
                                case 0:
                                    document.getElementById('btn-status-pompa-2').innerText = 'Aktifkan'
                                    document.getElementById('btn-status-pompa-2').classList.add('btn-success')
                                    document.getElementById('btn-status-pompa-2').dataset['status'] = 1
                                    document.getElementById('pemupukan-status-pompo-2').innerHTML =
                                        `<span class="text-danger">Tidak Aktif</span>`
                                    break;

                                case 1:
                                    document.getElementById('btn-status-pompa-2').innerText = 'Matikan'
                                    document.getElementById('btn-status-pompa-2').classList.add('btn-danger')
                                    document.getElementById('btn-status-pompa-2').dataset['status'] = 0
                                    document.getElementById('pemupukan-status-pompo-2').innerHTML =
                                        `<span class="text-info">Aktif</span>`
                                    break;

                                default:
                                    document.getElementById('btn-status-pompa-2').innerText = 'Menuggu response alat'
                                    document.getElementById('btn-status-pompa-2').classList.add('btn-secondary')
                                    document.getElementById('btn-status-pompa-2').dataset['status'] = ''
                                    document.getElementById('pemupukan-status-pompo-2').innerHTML =
                                        `<span class="text-warning">-</span>`
                                    break;
                            }
                        } else if (device && device.status == 0) {
                            document.getElementById('btn-status-pompa-1').innerText = "Perangkat Tidak aktif."
                            document.getElementById('btn-status-pompa-2').innerText = "Perangkat Tidak aktif."
                        }

                        $('#btn-kirim-ulang').addClass('d-none');
                        $('#col-reset-ulang').addClass('d-none');
                        $('#col-pemupukan-berhenti').addClass('d-none');
                        document.getElementById('col-alert-luar-jadwal').classList.remove('d-none')

                        if (fertilization) {
                            $('#btn-kirim-ulang').removeClass('d-none');
                            $('#col-reset-ulang').removeClass('d-none');
                            $('#col-pemupukan-berhenti').removeClass('d-none');
                            document.getElementById('col-alert-luar-jadwal').classList.add('d-none')
                            let listDay = []
                            const setDays = fertilization.set_day.split(",")

                            setDays.forEach((hari, index) => {
                                if (hari == 1) {
                                    listDay.push(days[index])
                                }
                            });

                            // document.getElementById('btn-status-pompa-1').classList.remove('d-none')
                            // document.getElementById('btn-status-pompa-2').classList.remove('d-none')

                            document.getElementById('btn-pemupukan-berhenti').dataset['id'] = fertilization.id
                            document.getElementById('btn-kirim-setting').dataset['id'] = fertilization.id
                            document.getElementById('btn-reset-perangkat').dataset['id'] = fertilization.id
                            document.getElementById('btn-pemupukan-berhenti').dataset['garden'] = gardenID
                            document.getElementById('btn-kirim-setting').dataset['garden'] = gardenID
                            document.getElementById('btn-reset-perangkat').dataset['garden'] = gardenID
                            document.getElementById('pemupukan-pengiriman-jadwal').innerHTML = fertilization.created_at
                            document.getElementById('pemupukan-jenis-tanaman').innerHTML = fertilization.crop_name
                            document.getElementById('pemupukan-minggu').innerHTML = fertilization.weeks
                            document.getElementById('pemupukan-set-hari').innerHTML = listDay.join(', ')
                            // document.getElementById('pemupukan-set-waktu').innerHTML = fertilization.set_time + ' | ' + fertilization.set_minute + ' Menit'

                            let setTimes = ``
                            fertilization.settimes.forEach(time => {
                                setTimes +=
                                    `<div>${time.time} | ${time.minute} menit | Selenoid: ${time.selenoid.join(',')}</div>`
                            });

                            document.getElementById('pemupukan-set-waktu').innerHTML = setTimes
                        }

                        const urlTelemetri = "{{ route('web.schedules', ['garden' => 'ID']) }}".replace('ID', gardenID)

                        const [dataSchedule, errorSchedule] = await yourRequest(urlTelemetri, settings)

                        if (errorSchedule) {
                            if ("messages" in errorSchedule) {
                                let errorMessage = ''

                                let element = ``
                                for (const key in errorSchedule.messages) {
                                    if (Object.hasOwnProperty.call(errorSchedule.messages, key)) {
                                        errorSchedule.messages[key].forEach(message => {
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

                        let schedules = dataSchedule.schedules

                        let element = schedules.length > 0 ? `` : ``

                        for (const key in schedules) {
                            if (Object.hasOwnProperty.call(schedules, key)) {
                                const group = schedules[key];
                                let td = ``
                                let week = 0;

                                for (let j = 0; j < group.length; j++) {
                                    const schedule = group[j];
                                    week = schedule.week == 0 ? "Luar penjadwalan" : "Pekan ke-" + schedule.week

                                    if (j == 0) {
                                        element += `<tr>
                                    <td class="text-center" rowspan="${group.length}">${week}</td>
                                    <td class="text-center">${days[schedule.day]} | ${schedule.date}</td>
                                    <td class="text-center">${schedule.time}</td>
                                    <td class="text-center">${schedule.type.replace('motor', 'pompa')}</td>
                                </tr>`
                                    } else {
                                        element += `<tr>
                                    <td class="text-center">${days[schedule.day]} | ${schedule.date}</td>
                                    <td class="text-center">${schedule.time}</td>
                                    <td class="text-center">${schedule.type.replace('motor', 'pompa')}</td>
                                </tr>`
                                    }

                                }
                            }
                        }

                        document.getElementById('view-schedules').innerHTML = element
                    } catch (error) {
                        console.log(error);
                    }
                }

                const modalSchedule = new bootstrap.Modal(document.getElementById("modalSchedules"), {});
                const btnStatusPompa1 = document.getElementById('btn-status-pompa-1');
                btnStatusPompa1.addEventListener('click', async function(e) {
                    try {
                        modalSchedule.toggle()
                        // Prevent default button action
                        e.preventDefault();

                        const result = await Swal.fire({
                            text: "Apakah anda yakin mengubah status pompa?",
                            icon: "info",
                            showCancelButton: true,
                            buttonsStyling: false,
                            confirmButtonText: "Iya, saya yakin!",
                            cancelButtonText: "Tidak, batalkan",
                            customClass: {
                                confirmButton: "btn fw-bold btn-success",
                                cancelButton: "btn fw-bold btn-active-light-primary"
                            }
                        })

                        if (result.value) {
                            // Show loading indication
                            btnStatusPompa1.setAttribute('data-kt-indicator', 'on');

                            // Disable button to avoid multiple click
                            btnStatusPompa1.disabled = true;

                            showSpinner()

                            // Simulate form submission. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                            let url = "{{ route('web.motor-status.update', ['garden' => 'ID']) }}".replace('ID',
                                btnStatusPompa1.dataset['garden'])
                            const formData = new FormData();

                            formData.append("motor", 1)
                            formData.append("status", btnStatusPompa1.dataset['status'])
                            formData.append("_method", 'PUT')

                            const settings = {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'x-csrf-token': '{{ csrf_token() }}'
                                },
                                body: formData
                            }

                            const [data, error] = await yourRequest(url, settings)

                            deleteSpinner()

                            // Remove loading indication
                            btnStatusPompa1.removeAttribute('data-kt-indicator');

                            // Enable button
                            btnStatusPompa1.disabled = false;

                            if (error) {
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

                            const message = data.message


                            Swal.fire({
                                text: message,
                                icon: "success",
                            });
                        }
                    } catch (error) {
                        console.error(error);

                        // Remove loading indication
                        btnStatusPompa1.removeAttribute('data-kt-indicator');

                        // Enable button
                        btnStatusPompa1.disabled = false;
                    }
                });

                const btnStatusPompa2 = document.getElementById('btn-status-pompa-2');
                btnStatusPompa2.addEventListener('click', async function(e) {
                    try {
                        modalSchedule.toggle()
                        // Prevent default button action
                        e.preventDefault();

                        const result = await Swal.fire({
                            text: "Apakah anda yakin mengubah status pompa?",
                            icon: "info",
                            showCancelButton: true,
                            buttonsStyling: false,
                            confirmButtonText: "Iya, saya yakin!",
                            cancelButtonText: "Tidak, batalkan",
                            customClass: {
                                confirmButton: "btn fw-bold btn-success",
                                cancelButton: "btn fw-bold btn-active-light-primary"
                            }
                        })

                        if (result.value) {
                            // Show loading indication
                            btnStatusPompa1.setAttribute('data-kt-indicator', 'on');

                            // Disable button to avoid multiple click
                            btnStatusPompa1.disabled = true;

                            showSpinner()

                            // Simulate form submission. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                            let url = "{{ route('web.motor-status.update', ['garden' => 'ID']) }}".replace('ID',
                                btnStatusPompa2.dataset['garden'])
                            const formData = new FormData();

                            formData.append("motor", 2)
                            formData.append("status", btnStatusPompa2.dataset['status'])
                            formData.append("_method", 'PUT')

                            const settings = {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'x-csrf-token': '{{ csrf_token() }}'
                                },
                                body: formData
                            }

                            const [data, error] = await yourRequest(url, settings)

                            deleteSpinner()

                            // Remove loading indication
                            btnStatusPompa2.removeAttribute('data-kt-indicator');

                            // Enable button
                            btnStatusPompa2.disabled = false;

                            if (error) {
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

                            const message = data.message


                            Swal.fire({
                                text: message,
                                icon: "success",
                            });
                        }
                    } catch (error) {
                        console.error(error);

                        // Remove loading indication
                        btnStatusPompa2.removeAttribute('data-kt-indicator');

                        // Enable button
                        btnStatusPompa2.disabled = false;
                    }
                });

                const btnResendSetting = document.getElementById('btn-kirim-setting');
                btnResendSetting.addEventListener('click', async function(e) {
                    try {
                        // Prevent default button action
                        e.preventDefault();

                        // Show loading indication
                        btnResendSetting.setAttribute('data-kt-indicator', 'on');

                        // Disable button to avoid multiple click
                        btnResendSetting.disabled = true;

                        showSpinner()

                        // Simulate form submission. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                        let url =
                            "{{ route('bitanic.fertilization.resend-setting', ['farmer' => $farmer->id, 'garden' => 'GD', 'id' => 'ID']) }}"
                            .replace('GD', btnResendSetting.dataset['garden'])
                            .replace('ID', btnResendSetting.dataset['id'])
                        const formData = new FormData();

                        const settings = {
                            method: 'POST',
                            headers: {
                                'x-csrf-token': '{{ csrf_token() }}'
                            },
                            body: formData
                        }

                        const [data, error] = await yourRequest(url, settings)

                        deleteSpinner()

                        // Remove loading indication
                        btnResendSetting.removeAttribute('data-kt-indicator');

                        // Enable button
                        btnResendSetting.disabled = false;

                        if (error) {
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

                        const message = data.message

                        modalSchedule.toggle()

                        Swal.fire({
                            text: message,
                            icon: "success"
                        });
                    } catch (error) {
                        console.error(error);

                        // Remove loading indication
                        btnResendSetting.removeAttribute('data-kt-indicator');

                        // Enable button
                        btnResendSetting.disabled = false;
                    }
                });

                const btnSaveAndReset = document.getElementById('btn-pemupukan-berhenti');
                btnSaveAndReset.addEventListener('click', async function(e) {
                    try {
                        // Prevent default button action
                        e.preventDefault();

                        // Show loading indication
                        btnSaveAndReset.setAttribute('data-kt-indicator', 'on');

                        // Disable button to avoid multiple click
                        btnSaveAndReset.disabled = true;

                        showSpinner()

                        // Simulate form submission. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                        let url =
                            "{{ route('bitanic.fertilization.save-fertilization', ['farmer' => $farmer->id, 'garden' => 'GD', 'id' => 'ID']) }}"
                            .replace('GD', btnSaveAndReset.dataset['garden'])
                            .replace('ID', btnSaveAndReset.dataset['id'])
                        const formData = new FormData();
                        formData.append("_method", 'PUT')

                        const settings = {
                            method: 'POST',
                            headers: {
                                'x-csrf-token': '{{ csrf_token() }}'
                            },
                            body: formData
                        }

                        const [data, error] = await yourRequest(url, settings)

                        deleteSpinner()

                        // Remove loading indication
                        btnSaveAndReset.removeAttribute('data-kt-indicator');

                        // Enable button
                        btnSaveAndReset.disabled = false;

                        if (error) {
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

                        const message = data.message

                        modalSchedule.toggle()

                        Swal.fire({
                            text: message,
                            icon: "success",
                            showConfirmButton: false
                        });
                    } catch (error) {
                        console.error(error);

                        // Remove loading indication
                        btnSaveAndReset.removeAttribute('data-kt-indicator');

                        // Enable button
                        btnSaveAndReset.disabled = false;
                    }
                });

                const btnDestroyAndResetSetting = document.getElementById('btn-reset-perangkat');
                btnDestroyAndResetSetting.addEventListener('click', async function(e) {
                    try {
                        // Prevent default button action
                        e.preventDefault();

                        // Show loading indication
                        btnDestroyAndResetSetting.setAttribute('data-kt-indicator', 'on');

                        // Disable button to avoid multiple click
                        btnDestroyAndResetSetting.disabled = true;

                        showSpinner()

                        // Simulate form submission. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                        let url =
                            "{{ route('bitanic.fertilization.destroy-fertilization', ['farmer' => $farmer->id, 'garden' => 'GD', 'id' => 'ID']) }}"
                            .replace('GD', btnDestroyAndResetSetting.dataset['garden'])
                            .replace('ID', btnDestroyAndResetSetting.dataset['id'])
                        const formData = new FormData();

                        const settings = {
                            method: 'DELETE',
                            headers: {
                                'x-csrf-token': '{{ csrf_token() }}'
                            },
                            body: formData
                        }

                        const [data, error] = await yourRequest(url, settings)

                        deleteSpinner()

                        // Remove loading indication
                        btnDestroyAndResetSetting.removeAttribute('data-kt-indicator');

                        // Enable button
                        btnDestroyAndResetSetting.disabled = false;

                        if (error) {
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

                        const message = data.message

                        modalSchedule.toggle()

                        Swal.fire({
                            text: message,
                            icon: "success"
                        });
                    } catch (error) {
                        console.error(error);

                        // Remove loading indication
                        btnDestroyAndResetSetting.removeAttribute('data-kt-indicator');

                        // Enable button
                        btnDestroyAndResetSetting.disabled = false;
                    }
                });

                async function changePompaStatus(pompa, status) {
                    const result = await Swal.fire({
                        text: "Apakah anda yakin?",
                        icon: "info",
                        showCancelButton: true,
                        buttonsStyling: false,
                        confirmButtonText: "Iya, saya yakin!",
                        cancelButtonText: "Tidak, batalkan",
                        customClass: {
                            confirmButton: "btn fw-bold btn-success",
                            cancelButton: "btn fw-bold btn-active-light-primary"
                        }
                    })


                    if (result.value) {
                        showSpinner()

                        // Simulate delete request -- for demo purpose only
                        const url = "{{ route('bitanic.garden.change-status', ['farmer' => $farmer, 'land' => $land->id, 'garden' => 'ID']) }}".replace(
                            'ID', data.id)

                        const settings = {
                            method: 'POST',
                            headers: {
                                'x-csrf-token': '{{ csrf_token() }}'
                            },
                            body: {}
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
                            text: "Kamu berhasil mengubah data " + data.name + "!.",
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

                document.addEventListener("DOMContentLoaded", () => {
                    console.log("Hello World!");

                    map.doubleClickZoom.disable();

                    // markerGroup.on('clusterclick', function (a) {
                    //     console.log(a.layer.getBounds());
                    // })

                    gardensMap.invalidateSize()

                    const landPolygon = L.polygon(JSON.parse("{{ json_encode($land->polygon) }}"), {
                        color: "#{{ $land->color }}55",
                        // dashArray: '10, 10',
                        // dashOffset: '20',
                    }).addTo(gardensMap)

                    gardensMap.fitBounds(landPolygon.getBounds());

                    getGardensPolygon()

                    marker = L.marker(defaultCoordinate, {
                        draggable: false
                    }).on('drag', function() {
                        stateData.lat = this.getLatLng().lat;
                        stateData.lng = this.getLatLng().lng;
                    })

                    polygon = L.polygon(latlngs, {
                        color: 'red'
                    })

                    map.on('dblclick', function(e) {
                        if (document.getElementById('polygon-switch').checked) {
                            polygon.remove()
                            latlngs.push([e.latlng.lat, e.latlng.lng])
                            polygon = L.polygon(latlngs, {
                                color: 'red'
                            }).addTo(map);
                        } else {
                            marker.remove()
                            stateData.lat = e.latlng.lat;
                            stateData.lng = e.latlng.lng;


                            marker = L.marker([stateData.lat, stateData.lng]).addTo(map).openPopup();
                            fillForm();
                        }
                    });

                    // Event input latitude
                    $('body').on('input', '#data-input-lat', function() {
                        stateData.lat = $('#data-input-lat').val();
                        var newLatLng = new L.LatLng(stateData.lat, stateData.lng);
                        marker.setLatLng(newLatLng);

                        defaultCoordinate = new L.LatLng(stateData.lat, stateData.lng);
                        console.log(defaultCoordinate);
                        map.panTo(defaultCoordinate);
                    });

                    // Event input longitude
                    $('body').on('input', '#data-input-lng', function() {
                        stateData.lng = $('#data-input-lng').val();
                        var newLatLng = new L.LatLng(stateData.lat, stateData.lng);
                        marker.setLatLng(newLatLng);

                        defaultCoordinate = new L.LatLng(stateData.lat, stateData.lng);
                        map.panTo(defaultCoordinate);
                    });

                    // Fill form
                    function fillForm() {
                        $(`#data-input-lat`).val(stateData.lat);
                        $(`#data-input-lng`).val(stateData.lng);
                    }

                    // map.invalidateSize()
                    // map.zoomControl.remove()

                    // var searchLayer = L.geoJson().addTo(map);

                    let zoomControl = null;

                    modal.addEventListener('show.bs.modal', function(event) {
                        // Button that triggered the modal
                        const button = event.relatedTarget
                        // Extract info from data-bs-* attributes
                        // const recipient = button.getAttribute('data-bs-whatever')
                        const modalTitle = modal.querySelector('.modal-title')

                        for (let index = 0; index < button.attributes.length; index++) {
                            if (button.attributes[index].nodeName.includes('data-input')) {
                                document.getElementById(button.attributes[index].nodeName).value = button
                                    .attributes[index].nodeValue

                                setTimeout(() => {
                                    map.invalidateSize()
                                }, 1000);

                                if (button.attributes[index].nodeName == 'data-input-id') {
                                    if (document.getElementById(button.attributes[index].nodeName).value != 'add') {
                                        modalTitle.textContent = 'Edit'
                                        getDevice(document.getElementById(button.attributes[index].nodeName).value)
                                        getGardenPolygon(document.getElementById(button.attributes[index].nodeName)
                                            .value)
                                        // validator.validate()
                                    } else {
                                        modalTitle.textContent = 'Tambah'

                                        getDevice();
                                    }
                                }
                            }
                        }

                    })

                    const btnDeletePolygon = document.getElementById('btn-delete-polygon')
                    btnDeletePolygon.addEventListener('click', e => {
                        console.log(gardensMarker);
                        polygon.remove()
                        latlngs = []
                    })

                    const btnReversePolygon = document.getElementById('btn-reverse-polygon')
                    btnReversePolygon.addEventListener('click', e => {
                        polygon.remove()
                        latlngs.pop()

                        polygon = L.polygon(latlngs, {
                            color: 'red'
                        }).addTo(map);
                    })

                    // modal map

                    const btnResetDevice = document.getElementById('btn-reset-device')
                    btnResetDevice.addEventListener('click', async e => {
                        e.preventDefault()

                        console.dir(e.target);

                        showSpinner()

                        let url = "{{ route('bitanic.device.reset', ['device' => 'ID']) }}".replace('ID', e
                            .target.dataset['id'])

                        const settings = {
                            method: 'POST',
                            headers: {
                                'x-csrf-token': '{{ csrf_token() }}'
                            }
                        }

                        const [data, error] = await yourRequest(url, settings)

                        deleteSpinner()
                        modalSpesifikasi.toggle()

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

                        Swal.fire({
                            text: "Command berhasil dikirim ke mqtt.",
                            icon: "success",
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    })


                });
            </script>
        @endpush
    </x-app-layout>
