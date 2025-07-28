<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
        <style>
            .flex-nowrap{
                flex-wrap: nowrap !important;
            }
        </style>
    @endpush

    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master /</span> Data Kelompok Petani</h4>
    </x-slot>
    {{-- End Header --}}

    <div class="row">
        <div class="col-md-12">
            <!-- Striped Rows -->
            <div class="card">
                <div class="row">
                    <div class="col-md-8">
                        <!-- Search -->
                        <form action="{{ route('bitanic.farmer-group.index') }}" method="GET" id="form-search">
                            <div class="row p-3 g-2">
                                <div class="col-12 col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary text-white"
                                            style="cursor: pointer;"
                                            onclick="document.getElementById('form-search').submit()">
                                            <i class="bx bx-search"></i>
                                        </span>
                                        <input type="text" class="form-control shadow-none"
                                            placeholder="Cari nama..." aria-label="Cari nama..." name="search"
                                            value="{{ request()->query('search') }}" />
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="input-group flex-nowrap">
                                        <span class="input-group-text bg-primary text-white"><small>Provinsi</small></span>
                                        <select class="form-select" style="width: 100%;" id="select-province" name="province"
                                            aria-label="Default select example">
                                            <option value="all">Semua</option>
                                            @forelse ($provinces as $id => $prov_name)
                                                <option value="{{ $id }}"
                                                    @if (request()->query('province') == $id) selected @endif>{{ $prov_name }}
                                                </option>
                                            @empty
                                                <option value="" disabled>Tidak ada data</option>
                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <!-- /Search -->
                    </div>
                    <div class="col-md-4">
                        <div class="float-end m-2">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#modalForm" title="Tambah Data" data-input-group-name=""
                                data-input-address="" data-input-subdistrict="" data-input-province=""
                                data-input-city="" data-input-district="" data-input-id="add">
                                <i class="bx bx-plus"></i>
                                Tambah
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive text-wrap">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Grup</th>
                                <th>Jumlah Anggota</th>
                                <th>Wilayah</th>
                                <th>Alamat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($data as $group)
                                @php
                                    $subdistrict = $group->subdistrict;
                                    $district = optional($subdistrict)->district;
                                    $city = optional($district)->city;
                                    $province = optional($city)->province;
                                @endphp
                                <tr>
                                    <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                                    <td>
                                        <a href="javascript:;" type="button" class="avatar pull-up" data-bs-toggle="modal"
                                            data-bs-target="#modalFoto" data-foto="{{ asset($group->picture) }}"
                                            data-input-id="{{ $group->id }}" style="display: inline-block;">
                                            <img src="{{ asset($group->picture) }}" alt="Avatar" class="rounded-circle" />
                                        </a>
                                        {{ $group->name }}
                                    </td>
                                    <td>
                                        <button class="btn btn-icon btn-info" data-bs-toggle="modal"
                                            data-bs-target="#modalFarmers" title="Click untuk melihat list petani group ini."
                                            onclick="getFarmers({{ $group->id }}, {{ optional($subdistrict)->id ?? null }})">
                                            {{ $group->farmers_count }}
                                        </button>
                                    </td>
                                    <td>
                                        <ul>
                                            <li>Provinsi: {{ optional($province)->prov_name }}</li>
                                            <li>Kabupaten/Kota: {{ optional($city)->city_name }}</li>
                                            <li>Kecamatan: {{ optional($district)->dis_name }}</li>
                                            <li>Desa: {{ optional($subdistrict)->subdis_name }}</li>
                                        </ul>
                                    </td>
                                    <td>
                                        {{ Str::limit($group->address, 20, '...') }}
                                        <i class='bx bx-info-circle' style="cursor: pointer;" data-bs-toggle="popover"
                                            data-bs-offset="0,14" data-bs-placement="top" data-bs-html="true"
                                            data-bs-content="<p>{{ $group->address }}</p>" title="Alamat"></i>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-icon btn-warning my-1" data-bs-toggle="modal"
                                            data-bs-target="#modalForm" title="Edit data"
                                            data-input-group-name="{{ $group->name }}"
                                            data-input-subdistrict="{{ optional($subdistrict)->id }}"
                                            data-input-district="{{ optional($district)->id }}"
                                            data-input-city="{{ optional($city)->id }}"
                                            data-input-province="{{ optional($province)->id }}"
                                            data-input-address="{{ $group->address }}"
                                            data-input-id="{{ $group->id }}"><i
                                                class="bx bx-edit-alt"></i>
                                        </button>
                                        <button class="btn btn-sm btn-icon btn-danger my-1"
                                            data-id="{{ $group->id }}" data-name="{{ $group->city_name }}"
                                            href="javascript:void(0);" title="Hapus Data" onclick="destroyGroup({{ $group->id }}), '{{ $group->name }}'"><i class="bx bx-trash"></i>
                                        </button>
                                    </td>
                                </tr>

                            @empty

                                <tr>
                                    <td colspan="7" class="text-center">Data tidak ada</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="row">
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

    @include('bitanic.farmer-group._modal-form')
    @include('bitanic.farmer-group._modal-foto')
    @include('bitanic.farmer-group._modal-farmers')
    @include('bitanic.farmer-group._modal-add-farmer')
    @include('bitanic.farmer-group._modal-remove-farmer')

    @push('scripts')
        <script src="{{ asset('theme/js/ui-popover.js') }}"></script>
        <script src="{{ asset('js/select2.min.js') }}"></script>
        <script>
            const provincePlaceholderText = 'Pilih Provinsi'
            const cityPlaceholderText = 'Pilih Kabupaten/Kota'
            const districtPlaceholderText = 'Pilih Kecamatan'
            const subdistrictPlaceholderText = 'Pilih Desa'
            const listFarmerAdd = []
            const listFarmerRemove = []
            const modalAddFarmer = new bootstrap.Modal(document.getElementById("modaAddFarmer"), {});
            const modalDeleteFarmer = new bootstrap.Modal(document.getElementById("modalDeleteFarmer"), {});
            const modalFarmers = new bootstrap.Modal(document.getElementById("modalFarmers"), {});

            const myModal = new bootstrap.Modal(document.getElementById("modalForm"), {});
            const modal = document.getElementById('modalForm')
            modal.addEventListener('show.bs.modal', function(event) {
                // Button that triggered the modal
                const button = event.relatedTarget
                // Extract info from data-bs-* attributes
                // const recipient = button.getAttribute('data-bs-whatever')
                const modalTitle = modal.querySelector('.modal-title')

                for (let index = 0; index < button.attributes.length; index++) {
                    if (button.attributes[index].nodeName.includes('data-input')) {
                        document.getElementById(button.attributes[index].nodeName).value = button.attributes[index]
                            .nodeValue

                        if (button.attributes[index].nodeName == 'data-input-id') {
                            if (document.getElementById(button.attributes[index].nodeName).value != 'add') {
                                modalTitle.textContent = 'Edit'
                                $('.text-tidak-wajib').removeClass('d-none');

                                getProvinces(button.attributes['data-input-province'].nodeValue)
                                getCity(button.attributes['data-input-province'].nodeValue, button.attributes['data-input-city'].nodeValue)
                                getDistricts(button.attributes['data-input-city'].nodeValue, button.attributes['data-input-district'].nodeValue)
                                getSubdistricts(button.attributes['data-input-district'].nodeValue, button.attributes['data-input-subdistrict'].nodeValue)
                                // validator.validate()
                            } else {
                                getProvinces()
                                getCity()
                                getDistricts()
                                getSubdistricts()

                                modalTitle.textContent = 'Tambah'
                                $('.text-tidak-wajib').addClass('d-none');
                            }
                        }
                    }
                }

            })

            // Submit button handler
            const submitButton = document.getElementById('submit-btn');
            submitButton.addEventListener('click', async function(e) {
                showSpinner()
                // Prevent default button action
                e.preventDefault();

                // Show loading indication
                submitButton.setAttribute('data-kt-indicator', 'on');

                // Disable button to avoid multiple click
                submitButton.disabled = true;

                // Simulate form submission. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                let url, formSubmited;
                const editOrAdd = document.getElementById('data-input-id');
                const formData = new FormData();

                let myFoto = document.getElementById('data-input-foto').files[0];

                if (typeof myFoto !== 'undefined'){
                    formData.append("picture", document.getElementById(
                        'data-input-foto').files[0])
                }

                formData.append("name", document.getElementById('data-input-group-name').value)
                formData.append("subdis_id", document.getElementById('data-input-subdistrict').value)
                formData.append("address", document.getElementById('data-input-address').value)

                if (editOrAdd.value != 'add') {
                    url = "{{ route('bitanic.farmer-group.update', 'ID') }}".replace('ID', document.getElementById(
                        'data-input-id').value)
                    formData.append('_method', 'PUT')
                } else {
                    url = "{{ route('bitanic.farmer-group.store') }}"
                }

                const settings = {
                    method: 'POST',
                    headers: {
                        'x-csrf-token': '{{ csrf_token() }}'
                    },
                    body: formData
                }

                const [data, error] = await yourRequest(url, settings)

                myModal.toggle()

                // Remove loading indication
                submitButton.removeAttribute('data-kt-indicator');

                // Enable button
                submitButton.disabled = false;

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
                    text: "Kamu berhasil menyimpan data!.",
                    icon: "success",
                    showConfirmButton: false,
                    allowOutsideClick: false,
                })

                window.location.reload();
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
                modalTitle.textContent = 'Foto'

                for (let index = 0; index < button.attributes.length; index++) {
                    if (button.attributes[index].nodeName.includes('data-foto')) {
                        document.getElementById('iframe').src = button.attributes[index].nodeValue
                    }
                }

            })

            const getProvinces = async (selectedValue = null) => {
                const [data, error] = await yourRequest("{{ route('web.wilayah.provinces') }}", {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'x-csrf-token': '{{ csrf_token() }}'
                    }
                })

                if (error) {
                    console.log(error);
                    return 0;
                }

                if (data.provinces.length > 0) {
                    //destroy select2
                    $('#data-input-province').select2("destroy");

                    //remove options physically from the HTML
                    $('#data-input-province').find("option").remove();

                    const provinces = data.provinces.map((e) => {
                        return {
                            id: e.id,
                            text: e.prov_name,
                            selected: (selectedValue && selectedValue == e.id) ? true : false
                        }
                    })

                    if (!selectedValue) {
                        provinces.unshift({id: '', text: ''})
                    }


                    $('#data-input-province').select2({
                        dropdownParent: $('#modalForm'),
                        data: provinces,
                        placeholder: provincePlaceholderText,
                        allowClear: true
                    });
                }
            }

            const getCity = async (province = null, selectedValue = null) => {
                if (!province) {
                    //remove options physically from the HTML
                    $('#data-input-city').find("option").remove();
                    $('#data-input-city').attr('disabled', 'disabled');
                    return 0;
                }

                $('#data-input-city').removeAttr('disabled');

                const [data, error] = await yourRequest("{{ route('web.wilayah.cities', ['province' => 'ID']) }}".replace('ID', province), {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'x-csrf-token': '{{ csrf_token() }}'
                    }
                })

                if (error) {
                    console.log(error);
                    return 0;
                }

                if (data.cities.length > 0) {
                    //destroy select2
                    $('#data-input-city').select2("destroy");

                    //remove options physically from the HTML
                    $('#data-input-city').find("option").remove();

                    const cities = data.cities.map((e) => {
                        return {
                            id: e.id,
                            text: e.city_name,
                            selected: (selectedValue && selectedValue == e.id) ? true : false
                        }
                    })

                    if (!selectedValue) {
                        cities.unshift({id: '', text: ''})
                    }

                    $('#data-input-city').select2({
                        dropdownParent: $('#modalForm'),
                        data: cities,
                        placeholder: cityPlaceholderText,
                        allowClear: true
                    });
                }

            }

            const getDistricts = async (city = null, selectedValue = null) => {
                if (!city) {
                    //remove options physically from the HTML
                    $('#data-input-district').find("option").remove();
                    $('#data-input-district').attr('disabled', 'disabled');
                    return 0;
                }
                $('#data-input-district').removeAttr('disabled');

                const [data, error] = await yourRequest("{{ route('web.wilayah.districts', ['city' => 'ID']) }}".replace('ID', city), {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'x-csrf-token': '{{ csrf_token() }}'
                    }
                })

                if (error) {
                    console.log(error);
                    return 0;
                }

                if (data.districts.length > 0) {
                    //destroy select2
                    $('#data-input-district').select2("destroy");

                    //remove options physically from the HTML
                    $('#data-input-district').find("option").remove();

                    const districts = data.districts.map((e) => {
                        return {
                            id: e.id,
                            text: e.dis_name,
                            selected: (selectedValue && selectedValue == e.id) ? true : false
                        }
                    })

                    if (!selectedValue) {
                        districts.unshift({id: '', text: ''})
                    }

                    $('#data-input-district').select2({
                        dropdownParent: $('#modalForm'),
                        data: districts,
                        placeholder: districtPlaceholderText,
                        allowClear: true
                    });
                }
            }

            const getSubdistricts = async (district = null, selectedValue = null) => {
                if (!district) {
                    //remove options physically from the HTML
                    $('#data-input-subdistrict').find("option").remove();
                    $('#data-input-subdistrict').attr('disabled', 'disabled');
                    return 0;
                }
                $('#data-input-subdistrict').removeAttr('disabled');

                const [data, error] = await yourRequest("{{ route('web.wilayah.subdistricts', ['district' => 'ID']) }}"
                    .replace('ID', district), {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'x-csrf-token': '{{ csrf_token() }}'
                    }
                })

                if (error) {
                    console.log(error);
                    return 0;
                }

                if (data.subdistricts.length > 0) {
                    //destroy select2
                    $('#data-input-subdistrict').select2("destroy");

                    //remove options physically from the HTML
                    $('#data-input-subdistrict').find("option").remove();

                    const subdistricts = data.subdistricts.map((e) => {
                        return {
                            id: e.id,
                            text: e.subdis_name,
                            selected: (selectedValue && selectedValue == e.id) ? true : false
                        }
                    })

                    if (!selectedValue) {
                        subdistricts.unshift({id: '', text: ''})
                    }

                    $('#data-input-subdistrict').select2({
                        dropdownParent: $('#modalForm'),
                        data: subdistricts,
                        placeholder: subdistrictPlaceholderText,
                        allowClear: true
                    });
                }
            }

            const getFarmers = async (group, subdis = null) => {
                if (subdis) {
                    $('#btn-add-farmer').attr('data-subdis', subdis);
                    $('#btn-delete-farmer').attr('data-subdis', subdis);
                    $('#btn-delete-farmer').attr('data-group', group);
                    $('#btn-submit-farmers').attr('data-group', group);
                    $('#btn-submit-delete-farmers').attr('data-group', group);
                }

                document.getElementById('view-spesifik').innerHTML = `<tr>
                                <td colspan="4" class="text-center">Loading...</td>
                            </tr>`

                const [data, error] = await yourRequest("{{ route('web.farmer-group-farmers', ['group' => 'ID']) }}"
                    .replace('ID', group), {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'x-csrf-token': '{{ csrf_token() }}'
                    }
                })

                if (error) {
                    console.log(error);
                    return 0;
                }

                let element = data.farmers.length > 0 ? `` : `<tr>
                                <td colspan="4" class="text-center">Tidak ada data</td>
                            </tr>`

                for (let i = 0; i < data.farmers.length; i++) {
                    const user = data.farmers[i];

                    element += `<tr>
                                <td class="text-center">${i+1}</td>
                                <td class="text-center">${user.name}</td>
                                <td class="text-center">${user.farmer.nik}</td>
                                <td class="text-center">${user.phone_number}</td>
                            </tr>`
                }

                document.getElementById('view-spesifik').innerHTML = element
            }

            const destroyGroup = (id, name) => {
                handleDeleteRows("{{ route('bitanic.farmer-group.destroy', 'ID') }}".replace('ID', id), "{{ csrf_token() }}", name)
            }

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

                $('#data-input-province').select2({
                    dropdownParent: $('#modalForm'),
                    placeholder: provincePlaceholderText,
                    allowClear: true
                });

                getProvinces()

                $('#data-input-city').select2({
                    dropdownParent: $('#modalForm'),
                    placeholder: cityPlaceholderText,
                    allowClear: true
                });

                $('#data-input-district').select2({
                    dropdownParent: $('#modalForm'),
                    placeholder: districtPlaceholderText,
                    allowClear: true
                });

                $('#data-input-subdistrict').select2({
                    dropdownParent: $('#modalForm'),
                    placeholder: subdistrictPlaceholderText,
                    allowClear: true
                });

                $('#data-input-province').on('select2:select', function (e) {
                    // Do submit
                    getCity(this.value)
                    $('#data-input-city').find("option").remove();
                    $('#data-input-district').find("option").remove();
                    $('#data-input-subdistrict').find("option").remove();
                });
                $('#data-input-city').on('select2:select', function (e) {
                    // Do submit
                    getDistricts(this.value)
                    $('#data-input-district').find("option").remove();
                    $('#data-input-subdistrict').find("option").remove();
                });
                $('#data-input-district').on('select2:select', function (e) {
                    // Do submit
                    getSubdistricts(this.value)
                    $('#data-input-subdistrict').find("option").remove();
                });

                $('#select-province').select2({
                    placeholder: 'Semua Province'
                });

                const selectSearch = document.querySelectorAll('.select-search')
                selectSearch.forEach(eSelect => {
                    eSelect.addEventListener('change', e => {
                        document.getElementById('form-search').submit()
                    })
                });

                $('#select-province').on('select2:select', function (e) {
                    // Do submit
                    document.getElementById('form-search').submit()
                });

                document.getElementById('btn-add-farmer').addEventListener('click', async (event) => {

                    document.getElementById('list-petani').innerHTML = `<tr>
                                    <td colspan="4" class="text-center">Loading...</td>
                                </tr>`

                    const [data, error] = await yourRequest("{{ route('web.get-farmers-from-subdis', ['subdis' => 'ID']) }}"
                        .replace('ID', event.target.dataset['subdis']), {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'x-csrf-token': '{{ csrf_token() }}'
                        }
                    })

                    if (error) {
                        console.log(error);
                        return 0;
                    }

                    let element = data.farmers.length > 0 ? `` : `<tr>
                                    <td colspan="4" class="text-center">Tidak ada data</td>
                                </tr>`

                    for (let i = 0; i < data.farmers.length; i++) {
                        const user = data.farmers[i];

                        element += `<tr data-id="${user.farmer.id}">
                                    <td class="text-center">${i+1}</td>
                                    <td class="text-center">${user.name}</td>
                                    <td class="text-center">${user.farmer.nik}</td>
                                    <td class="text-center">${user.phone_number}</td>
                                </tr>`
                    }

                    document.getElementById('list-petani').innerHTML = element
                })

                document.getElementById('btn-delete-farmer').addEventListener('click', async (event) => {
                    const subdis = event.target.dataset['subdis']
                    const group = event.target.dataset['group']

                    document.getElementById('list-petani-for-delete').innerHTML = `<tr>
                                    <td colspan="4" class="text-center">Loading...</td>
                                </tr>`

                    const [data, error] = await yourRequest("{{ route('web.farmer-group-farmers', ['group' => 'ID']) }}"
                        .replace('ID', group), {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'x-csrf-token': '{{ csrf_token() }}'
                        }
                    })

                    if (error) {
                        console.log(error);
                        return 0;
                    }

                    let element = data.farmers.length > 0 ? `` : `<tr>
                                    <td colspan="4" class="text-center">Tidak ada data</td>
                                </tr>`

                    for (let i = 0; i < data.farmers.length; i++) {
                        const user = data.farmers[i];

                        element += `<tr data-id="${user.farmer.id}">
                                    <td class="text-center">${i+1}</td>
                                    <td class="text-center">${user.name}</td>
                                    <td class="text-center">${user.farmer.nik}</td>
                                    <td class="text-center">${user.phone_number}</td>
                                </tr>`
                    }

                    document.getElementById('list-petani-for-delete').innerHTML = element
                })

                document.getElementById('list-petani').addEventListener('click', event => {
                    let userID = event.target.parentElement.dataset['id']

                    Array.prototype.forEach.call(event.target.parentElement.children, (e) => {
                        e.classList.toggle('bg-secondary')
                        e.classList.toggle('bg-opacity-25')
                        e.classList.toggle('text-white')
                    })

                    if (listFarmerAdd.includes(userID)) {
                        const index = listFarmerAdd.indexOf(userID)

                        if (index > -1) {
                            listFarmerAdd.splice(index, 1)
                        }

                        return 0
                    }

                    listFarmerAdd.push(event.target.parentElement.dataset['id'])
                })

                document.getElementById('list-petani-for-delete').addEventListener('click', event => {
                    let userID = event.target.parentElement.dataset['id']

                    Array.prototype.forEach.call(event.target.parentElement.children, (e) => {
                        e.classList.toggle('bg-secondary')
                        e.classList.toggle('bg-opacity-25')
                        e.classList.toggle('text-white')
                    })

                    if (listFarmerRemove.includes(userID)) {
                        const index = listFarmerRemove.indexOf(userID)

                        if (index > -1) {
                            listFarmerRemove.splice(index, 1)
                        }

                        return 0
                    }

                    listFarmerRemove.push(event.target.parentElement.dataset['id'])

                })

                document.getElementById('btn-submit-farmers').addEventListener('click', async event => {
                    showSpinner()

                    let url, formSubmited;
                    const formData = new FormData();

                    formData.append('farmers', JSON.stringify(listFarmerAdd))

                    url = "{{ route('bitanic.farmer-group.add-farmers', 'ID') }}".replace('ID', event.target.dataset['group'])
                    formData.append('_method', 'PUT')

                    const settings = {
                        method: 'POST',
                        headers: {
                            'x-csrf-token': '{{ csrf_token() }}'
                        },
                        body: formData
                    }

                    const [data, error] = await yourRequest(url, settings)

                    modalAddFarmer.toggle()

                    listFarmerAdd.splice(0, listFarmerAdd.length)

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
                        text: "Kamu berhasil menyimpan data!.",
                        icon: "success",
                        showConfirmButton: false,
                        allowOutsideClick: false,
                    })

                    window.location.reload();
                })

                document.getElementById('btn-submit-delete-farmers').addEventListener('click', async event => {
                    showSpinner()

                    let url, formSubmited;
                    const formData = new FormData();

                    formData.append('farmers', JSON.stringify(listFarmerRemove))

                    url = "{{ route('bitanic.farmer-group.remove-farmers', 'ID') }}".replace('ID', event.target.dataset['group'])
                    formData.append('_method', 'PUT')

                    const settings = {
                        method: 'POST',
                        headers: {
                            'x-csrf-token': '{{ csrf_token() }}'
                        },
                        body: formData
                    }

                    const [data, error] = await yourRequest(url, settings)

                    modalDeleteFarmer.toggle()

                    listFarmerRemove.splice(0, listFarmerRemove.length)

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
                        text: "Kamu berhasil menyimpan data!.",
                        icon: "success",
                        showConfirmButton: false,
                        allowOutsideClick: false,
                    })

                    window.location.reload();
                })

                document.getElementById('btn-close-add-farmer').addEventListener('click', e => {
                    modalAddFarmer.hide()
                    listFarmerAdd.splice(0, listFarmerAdd.length)
                })
                document.getElementById('btn-close-delete-farmer').addEventListener('click', e => {
                    modalDeleteFarmer.hide()
                    listFarmerRemove.splice(0, listFarmerRemove.length)
                })
                document.getElementById('btn-back-farmer').addEventListener('click', e => {
                    modalAddFarmer.hide()
                    modalFarmers.show()
                    listFarmerAdd.splice(0, listFarmerAdd.length)
                })
                document.getElementById('btn-back-delete-farmer').addEventListener('click', e => {
                    modalDeleteFarmer.hide()
                    modalFarmers.show()
                    listFarmerRemove.splice(0, listFarmerRemove.length)
                })
            });
        </script>
    @endpush
</x-app-layout>
