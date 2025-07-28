<x-app-layout>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
        <style>
            .flex-nowrap {
                flex-wrap: nowrap !important;
            }
        </style>
    @endpush

    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master /</span> Data Pengguna</h4>
    </x-slot>
    {{-- End Header --}}

    <div class="row">
        <div class="col-md-12">
            <!-- Striped Rows -->
            <div class="card">
                <div class="row">
                    <div class="col-md-10">
                        <div class="m-3">
                            <!-- Search -->
                            <form action="{{ route('bitanic.farmer.index') }}" method="GET" id="form-search">
                                <!-- Validation Errors -->
                                <x-auth-validation-errors class="mb-4" :errors="$errors" />
                                <div class="row g-2">
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="input-group">
                                                    <span class="input-group-text bg-primary text-white"
                                                        style="cursor: pointer;"
                                                        onclick="document.getElementById('form-search').submit()">
                                                        <i class="bx bx-search"></i>
                                                    </span>
                                                    <input type="text" class="form-control shadow-none"
                                                        placeholder="Cari Nama, No HP" aria-label="Cari Nama, No HP"
                                                        name="search" value="{{ request()->query('search') }}" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <div class="input-group flex-nowrap">
                                                    <span
                                                        class="input-group-text bg-primary text-white"><small>Provinsi</small></span>
                                                    <select class="form-select select-wilayah"
                                                        id="search-select-province" name="province"
                                                        aria-label="Default select example">
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group flex-nowrap">
                                                    <span
                                                        class="input-group-text bg-primary text-white"><small>Kabupaten/Kota</small></span>
                                                    <select class="form-select select-wilayah" id="search-select-city"
                                                        name="city" aria-label="Default select example" disabled>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group flex-nowrap">
                                                    <span
                                                        class="input-group-text bg-primary text-white"><small>Kecamatan</small></span>
                                                    <select class="form-select select-wilayah"
                                                        id="search-select-district" name="district"
                                                        aria-label="Default select example" disabled>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group flex-nowrap">
                                                    <span
                                                        class="input-group-text bg-primary text-white"><small>Desa</small></span>
                                                    <select class="form-select select-wilayah"
                                                        id="search-select-subdistrict" name="subdistrict"
                                                        aria-label="Default select example" disabled>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <!-- /Search -->
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="float-end m-3">
                            <div class="d-flex flex-row-reverse gap-2">
                                <button type="button" class="btn btn-primary btn-icon" title="Tambah Petani"
                                    data-bs-toggle="modal" data-bs-target="#modalForm" data-input-type="2"
                                    data-input-category="1" data-input-nama="" data-input-phone-number=""
                                    data-input-nik="" data-input-gender="l" data-input-birth-date=""
                                    data-input-address="" data-input-subdistrict="" data-input-farmer-group="0"
                                    data-input-id="add">
                                    <i class="bx bx-plus"></i>
                                </button>
                                <a href="{{ route('bitanic.farmer.export-excel', [
                                    'province' => request()->query('province'),
                                    'city' => request()->query('city'),
                                    'district' => request()->query('district'),
                                    'subdistrict' => request()->query('subdistrict'),
                                ]) }}"
                                    class="btn btn-success btn-icon">
                                    <i class="bx bx-export"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Status KTP</th>
                                <th>Kategori</th>
                                <th>Alamat</th>
                                <th>Jenis Kelamin</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($data as $user)
                                @php
                                    $subdistrict = $user->subdistrict;
                                    $district = optional($subdistrict)->district;
                                    $city = optional($district)->city;
                                    $province = optional($city)->province;
                                @endphp
                                <tr>
                                    <td>
                                        <a href="javascript:;" type="button" class="avatar pull-up"
                                            data-bs-toggle="modal" data-bs-target="#modalFoto"
                                            data-foto="{{ $user->farmer?->picture ? asset($user->farmer->picture) : asset('bitanic-landing/default-profile.png') }}"
                                            style="display: inline-block;">
                                            <img src="{{ $user->farmer?->picture ? asset($user->farmer->picture) : asset('bitanic-landing/default-profile.png') }}"
                                                alt="Avatar" class="rounded-circle" />
                                        </a>
                                        {{ $user->name }}
                                    </td>
                                    <td>
                                        @if ($user->farmer->is_ktp_uploaded)
                                            @if ($user->farmer->is_ktp_validated === null)
                                                <span class="badge bg-warning">KTP Belum Diverifikasi</span>
                                            @elseif ($user->farmer->is_ktp_validated === 0)
                                                <span class="badge bg-danger">KTP Ditolak</span>
                                            @elseif ($user->farmer->is_ktp_validated === 1)
                                                <span class="badge bg-success">KTP Diterima</span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">Belum Upload KTP</span>
                                        @endif
                                    </td>
                                    <td>{{ farmerCategory($user->farmer->category) }}</td>
                                    <td>
                                        {{ Str::limit($user->farmer->address, 20, '...') }}
                                        <span data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="right"
                                            data-bs-html="true"
                                            title="<i class='bx bxs-hand-up bx-xs' ></i> <span>Klik untuk lihat alamat</span>">
                                            <i class='bx bx-info-circle' style="cursor: pointer;"
                                                data-bs-toggle="popover" data-bs-offset="0,14"
                                                data-bs-placement="top" data-bs-html="true"
                                                data-bs-content="<p>{{ $user->farmer->address }}</p>"
                                                title="Alamat"></i>
                                        </span>
                                    </td>
                                    <td>{{ $user->farmer->gender == 'l' ? 'Laki - laki' : 'Perempuan' }}</td>
                                    <td>
                                        <a href="{{ route('bitanic.farmer.show', $user->id) }}"
                                            class="btn btn-info btn-sm btn-icon"
                                            title="Klik untuk buka detail pengguna">
                                            <i class="bx bx-list-ul"></i>
                                        </a>
                                        <button type="button" class="btn btn-warning btn-icon btn-sm"
                                            data-bs-toggle="modal" data-bs-target="#modalForm"
                                            data-input-type="{{ $user->farmer->type }}"
                                            data-input-category="{{ $user->farmer->category }}"
                                            data-input-nama="{{ $user->name }}"
                                            data-input-phone-number="{{ substr($user->phone_number, 2) }}"
                                            data-input-nik="{{ $user->farmer->nik }}"
                                            data-input-gender="{{ $user->farmer->gender }}"
                                            data-input-birth-date="{{ $user->farmer->birth_date }}"
                                            data-input-address="{{ $user->farmer->address }}"
                                            data-input-subdistrict="{{ optional($subdistrict)->id }}"
                                            data-input-district="{{ optional($district)->id }}"
                                            data-input-city="{{ optional($city)->id }}"
                                            data-input-province="{{ optional($province)->id }}"
                                            data-input-farmer-group="{{ $user->farmer->group_id }}"
                                            data-input-product='@json($user->farmer->bitanicProducts->pluck('id'))' data-input-password=""
                                            data-input-password-confirm="" data-input-id="{{ $user->id }}"
                                            title="Edit Pengguna">
                                            <i class="bx bx-edit-alt"></i>
                                        </button>
                                        <button type="button" onclick="handleDeleteRows({{ $user }})"
                                            class="btn btn-danger btn-icon btn-sm" title="Hapus Pengguna">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada pengguna</td>
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

    @include('bitanic.farmer._modal-form')
    @include('bitanic.farmer._modal-foto')

    @push('scripts')
        <script src="{{ asset('js/select2.min.js') }}"></script>
        <script src="{{ asset('theme/js/ui-popover.js') }}"></script>
        <script>
            showSpinner()

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
                                $('#alert').removeClass('d-none');

                                getProvinces(button.attributes['data-input-province'].nodeValue, false,
                                    'data-input-province', $('#modalForm'))
                                getCity('data-input-city', $('#modalForm'), false, button.attributes[
                                        'data-input-province'].nodeValue, button.attributes['data-input-city']
                                    .nodeValue)
                                getDistricts('data-input-district', $('#modalForm'), false, button.attributes[
                                        'data-input-city'].nodeValue, button.attributes['data-input-district']
                                    .nodeValue)
                                getSubdistricts('data-input-subdistrict', $('#modalForm'), false, button.attributes[
                                    'data-input-district'].nodeValue, button.attributes[
                                    'data-input-subdistrict'].nodeValue)
                                getFarmerGroups(button.attributes['data-input-subdistrict'].nodeValue, button
                                    .attributes['data-input-farmer-group'].nodeValue)
                                // validator.validate()
                                if (button.hasAttribute('data-input-product')) {
                                    let selectedProducts = JSON.parse(button.attributes['data-input-product']
                                        .nodeValue);
                                    $('#data-input-product').val(selectedProducts).trigger('change');
                                }
                            } else {
                                getProvinces(null, false, 'data-input-province', $('#modalForm'))
                                getCity('data-input-city', $('#modalForm'), false)
                                getDistricts('data-input-district', $('#modalForm'), false)
                                getSubdistricts('data-input-subdistrict', $('#modalForm'), false)
                                getFarmerGroups()
                                modalTitle.textContent = 'Tambah'
                                $('#alert').addClass('d-none');
                                $('#data-input-product').val(null).trigger('change');

                            }
                        }
                    }
                }

            })

            // Submit button handler
            const submitButton = document.getElementById('submit-btn');
            submitButton.addEventListener('click', async function(e) {
                try {
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
                    let myFoto = document.getElementById('data-input-foto').files[0];
                    let fileFoto = null
                    let setMethod = 'POST'
                    const formData = new FormData();
                    const selectedProducts = $('#data-input-product').val();

                    selectedProducts.forEach(p => formData.append("products[]", p));

                    if (typeof myFoto !== 'undefined') {
                        formData.append("picture", document.getElementById(
                            'data-input-foto').files[0])
                    }

                    formData.append("type", document.getElementById('data-input-type').value)
                    formData.append("category", document.getElementById('data-input-category').value)
                    formData.append("name", document.getElementById('data-input-nama').value)
                    formData.append("password", document.getElementById('data-input-password').value)
                    formData.append("password_confirmation", document.getElementById('data-input-password-confirm')
                        .value)
                    formData.append("phone_number", document.getElementById('data-input-phone-number').value)
                    formData.append("nik", document.getElementById('data-input-nik').value)
                    formData.append("gender", document.getElementById('data-input-gender').value)
                    formData.append("birth_date", document.getElementById('data-input-birth-date').value)
                    formData.append("address", document.getElementById('data-input-address').value)
                    formData.append("subdistrict", document.getElementById('data-input-subdistrict').value)
                    formData.append("farmer_group", document.getElementById('data-input-farmer-group').value)


                    if (editOrAdd.value != 'add') {
                        url = "{{ route('bitanic.farmer.update', 'ID') }}".replace('ID', document.getElementById(
                            'data-input-id').value)
                        setMethod = 'POST'

                        formData.append("_method", 'PUT')
                    } else {
                        url = "{{ route('bitanic.farmer.store') }}"
                    }

                    let settings = {
                        method: setMethod,
                        headers: {
                            'x-csrf-token': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: formData
                    }

                    let response = await fetch(url, settings)

                    if (response.ok) {
                        window.location.reload();
                        // deleteSpinner()

                        // Remove loading indication
                        submitButton.removeAttribute('data-kt-indicator');

                        // Enable button
                        submitButton.disabled = false;
                    } else if (response.status >= 400 && response.status <= 500) {
                        let json = await response.json()
                        deleteSpinner()
                        let errorMessage = json.messages

                        myModal.toggle()

                        let element = ``
                        for (const key in errorMessage) {
                            if (Object.hasOwnProperty.call(errorMessage, key)) {
                                errorMessage[key].forEach(message => {
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

                        // Remove loading indication
                        submitButton.removeAttribute('data-kt-indicator');

                        // Enable button
                        submitButton.disabled = false;
                    } else {
                        console.log(response);
                    }
                } catch (error) {
                    console.log(error);
                }
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
                modalTitle.textContent = 'Foto Pengguna'

                for (let index = 0; index < button.attributes.length; index++) {
                    if (button.attributes[index].nodeName.includes('data-foto')) {
                        document.getElementById('iframe').src = button.attributes[index].nodeValue
                    }
                }

            })

            async function handleDeleteRows(data) {
                let result = await Swal.fire({
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
                    Swal.fire({
                        html: '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span class=""> Loading...</span>',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                    });
                    // Simulate delete request -- for demo purpose only
                    const url = "{{ route('bitanic.farmer.destroy', 'ID') }}"
                    let newUrl = url.replace('ID', data.id)
                    let settings = {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json;charset=utf-8',
                            'x-csrf-token': '{{ csrf_token() }}'
                        },
                    }

                    try {
                        let response = await fetch(newUrl, settings)

                        Swal.fire({
                            text: "Kamu berhasil menghapus data " + data.name + "!.",
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
                    } catch (error) {
                        let errorMessage = error

                        if (error.hasOwnProperty('response')) {
                            if (error.response.status == 422) {
                                errorMessage = 'Data yang dikirim tidak sesuai'
                            }
                        }

                        Swal.fire({
                            text: errorMessage,
                            icon: "error",
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    }
                }

            }

            const getProvinces = async (selectedValue = null, setAll = false, selectId, isModal = $(document.body)) => {
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
                    $('#' + selectId).select2("destroy");

                    //remove options physically from the HTML
                    $('#' + selectId).find("option").remove();

                    const provinces = data.provinces.map((e) => {
                        return {
                            id: e.id,
                            text: e.prov_name,
                            selected: (selectedValue && selectedValue == e.id) ? true : false
                        }
                    })

                    if (setAll) {
                        provinces.unshift({
                            id: 'zero',
                            text: 'Tidak Memiliki Wilayah',
                            selected: (selectedValue && selectedValue == 'zero') ? true : false
                        })
                        provinces.unshift({
                            id: 'all',
                            text: 'Semua',
                            selected: (selectedValue && selectedValue == 'all') ? true : false
                        })
                    }

                    if (!selectedValue) {
                        provinces.unshift({
                            id: '',
                            text: ''
                        })
                    }

                    $('#' + selectId).select2({
                        dropdownParent: isModal,
                        data: provinces,
                        placeholder: "Pilih wilayah",
                        allowClear: true
                    });
                }
            }

            const getCity = async (selectId, selectModal, setAll = false, province = null, selectedValue = null) => {
                if (!province) {
                    //remove options physically from the HTML
                    $('#' + selectId).find("option").remove();
                    $('#' + selectId).attr('disabled', 'disabled');
                    return 0;
                }

                $('#' + selectId).removeAttr('disabled');

                const [data, error] = await yourRequest("{{ route('web.wilayah.cities', ['province' => 'ID']) }}"
                    .replace('ID', province), {
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
                    $('#' + selectId).select2("destroy");

                    //remove options physically from the HTML
                    $('#' + selectId).find("option").remove();

                    const cities = data.cities.map((e) => {
                        return {
                            id: e.id,
                            text: e.city_name,
                            selected: (selectedValue && selectedValue == e.id) ? true : false
                        }
                    })

                    if (setAll) {
                        cities.unshift({
                            id: 'all',
                            text: 'Semua',
                            selected: (selectedValue && selectedValue == 'all') ? true : false
                        })
                    }

                    if (!selectedValue) {
                        cities.unshift({
                            id: '',
                            text: ''
                        })
                    }

                    $('#' + selectId).select2({
                        dropdownParent: selectModal,
                        data: cities,
                        placeholder: "Pilih wilayah",
                        allowClear: true
                    });
                }

            }

            const getDistricts = async (selectId, selectModal, setAll = false, city = null, selectedValue = null) => {
                if (!city) {
                    //remove options physically from the HTML
                    $('#' + selectId).find("option").remove();
                    $('#' + selectId).attr('disabled', 'disabled');
                    return 0;
                }

                const [data, error] = await yourRequest("{{ route('web.wilayah.districts', ['city' => 'ID']) }}"
                    .replace('ID', city), {
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

                $('#' + selectId).removeAttr('disabled');

                if (data.districts.length > 0) {
                    //destroy select2
                    $('#' + selectId).select2("destroy");

                    //remove options physically from the HTML
                    $('#' + selectId).find("option").remove();

                    const districts = data.districts.map((e) => {
                        return {
                            id: e.id,
                            text: e.dis_name,
                            selected: (selectedValue && selectedValue == e.id) ? true : false
                        }
                    })

                    if (setAll) {
                        districts.unshift({
                            id: 'all',
                            text: 'Semua',
                            selected: (selectedValue && selectedValue == 'all') ? true : false
                        })
                    }

                    if (!selectedValue) {
                        districts.unshift({
                            id: '',
                            text: ''
                        })
                    }

                    $('#' + selectId).select2({
                        dropdownParent: selectModal,
                        data: districts,
                        placeholder: "Pilih wilayah",
                        allowClear: true
                    });
                }
            }

            const getSubdistricts = async (selectId, selectModal, setAll = false, district = null, selectedValue = null) => {
                if (!district) {
                    //remove options physically from the HTML
                    $('#' + selectId).find("option").remove();
                    $('#' + selectId).attr('disabled', 'disabled');
                    return 0;
                }

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
                $('#' + selectId).removeAttr('disabled');

                if (data.subdistricts.length > 0) {
                    //destroy select2
                    $('#' + selectId).select2("destroy");

                    //remove options physically from the HTML
                    $('#' + selectId).find("option").remove();

                    const subdistricts = data.subdistricts.map((e) => {
                        return {
                            id: e.id,
                            text: e.subdis_name,
                            selected: (selectedValue && selectedValue == e.id) ? true : false
                        }
                    })

                    if (setAll) {
                        subdistricts.unshift({
                            id: 'all',
                            text: 'Semua',
                            selected: (selectedValue && selectedValue == 'all') ? true : false
                        })
                    }

                    if (!selectedValue) {
                        subdistricts.unshift({
                            id: '',
                            text: ''
                        })
                    }

                    $('#' + selectId).select2({
                        dropdownParent: selectModal,
                        data: subdistricts,
                        placeholder: "Pilih wilayah",
                        allowClear: true
                    });
                }

            }

            const getFarmerGroups = async (subdistrict = null, selectedValue = null) => {
                if (!subdistrict) {
                    //destroy select2
                    $('#data-input-farmer-group').select2("destroy");
                    //remove options physically from the HTML
                    $('#data-input-farmer-group').find("option").remove();

                    $('#data-input-farmer-group').select2({
                        dropdownParent: $('#modalForm'),
                        placeholder: "Pilih wilayah",
                        allowClear: true,
                        data: [{
                            id: 0,
                            text: 'Tidak Memiliki',
                            selected: true
                        }]
                    });
                    return 0;
                }
                $('#data-input-subdistrict').removeAttr('disabled');

                const [data, error] = await yourRequest("{{ route('web.farmer-groups', ['subdistrict' => 'ID']) }}"
                    .replace('ID', subdistrict), {
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

                if (data.groups.length > 0) {
                    //destroy select2
                    $('#data-input-farmer-group').select2("destroy");

                    //remove options physically from the HTML
                    $('#data-input-farmer-group').find("option").remove();

                    const groups = data.groups.map((e) => {
                        return {
                            id: e.id,
                            text: e.name,
                            selected: (selectedValue && selectedValue == e.id) ? true : false
                        }
                    })

                    groups.unshift({
                        id: 0,
                        text: 'tidak memiliki'
                    })

                    $('#data-input-farmer-group').select2({
                        dropdownParent: $('#modalForm'),
                        data: groups,
                        placeholder: "Pilih wilayah",
                        allowClear: true
                    });
                }

            }

            function eventClear(e) {
                document.querySelector('#form-search').submit()
            }

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");
                deleteSpinner()

                document.getElementById("data-input-nik").addEventListener("keypress", function(event) {
                    const key = event.keyCode;
                    // Only allow numbers (key codes 48 to 57)
                    if (key < 48 || key > 57) {
                        event.preventDefault();
                    }
                });

                $('#data-input-province').select2({
                    dropdownParent: $('#modalForm'),
                    placeholder: "Pilih wilayah",
                    allowClear: true
                });

                $('#search-select-province').select2({
                    placeholder: "Pilih wilayah",
                    allowClear: true
                });
                $('#search-select-city').select2({
                    placeholder: "Pilih wilayah",
                    allowClear: true
                });
                $('#search-select-district').select2({
                    placeholder: "Pilih wilayah",
                    allowClear: true
                });
                $('#search-select-subdistrict').select2({
                    placeholder: "Pilih wilayah",
                    allowClear: true
                });

                const provinceSelect = "{{ request()->query('province') }}"
                const citySelect = "{{ request()->query('city') }}"
                const districtSelect = "{{ request()->query('district') }}"
                const subdistrictSelect = "{{ request()->query('subdistrict') }}"

                getProvinces(null, false, 'data-input-province', $('#modalForm'))
                getProvinces(provinceSelect, true, 'search-select-province')

                if (provinceSelect && provinceSelect != 'zero') {
                    getCity('search-select-city', $(document.body), true, provinceSelect, citySelect)
                }
                if (citySelect && provinceSelect != 'zero') {
                    getDistricts('search-select-district', $(document.body), true, citySelect, districtSelect)
                }
                if (districtSelect && provinceSelect != 'zero') {
                    getSubdistricts('search-select-subdistrict', $(document.body), true, districtSelect,
                        subdistrictSelect)
                }

                $('#data-input-city').select2({
                    dropdownParent: $('#modalForm'),
                    placeholder: "Pilih wilayah",
                    allowClear: true
                });

                $('#data-input-district').select2({
                    dropdownParent: $('#modalForm'),
                    placeholder: "Pilih wilayah",
                    allowClear: true
                });

                $('#data-input-subdistrict').select2({
                    dropdownParent: $('#modalForm'),
                    placeholder: "Pilih wilayah",
                    allowClear: true
                });

                $('#data-input-farmer-group').select2({
                    dropdownParent: $('#modalForm'),
                    placeholder: "Pilih wilayah",
                    allowClear: true,
                    data: [{
                        id: 0,
                        text: 'Tidak Memiliki',
                        selected: true
                    }]
                });
                $('#data-input-product').select2({
                    dropdownParent: $('#modalForm'),
                    placeholder: "Pilih produk",
                    allowClear: true,
                    // data: [
                    //     {
                    //         id: 0,
                    //         text: 'Tidak Memiliki',
                    //         selected: true
                    //     }
                    // ]
                });

                $('#data-input-province').on('select2:select', function(e) {
                    // Do submit
                    getCity('data-input-city', $('#modalForm'), false, this.value)
                    $('#data-input-city').find("option").remove();
                    $('#data-input-district').find("option").remove();
                    $('#data-input-subdistrict').find("option").remove();
                });
                $('#data-input-city').on('select2:select', function(e) {
                    // Do submit
                    getDistricts('data-input-district', $('#modalForm'), false, this.value)
                    $('#data-input-district').find("option").remove();
                    $('#data-input-subdistrict').find("option").remove();
                });
                $('#data-input-district').on('select2:select', function(e) {
                    // Do submit
                    getSubdistricts('data-input-subdistrict', $('#modalForm'), false, this.value)
                    $('#data-input-subdistrict').find("option").remove();
                });
                $('#data-input-subdistrict').on('select2:select', function(e) {
                    // Do submit
                    getFarmerGroups(this.value)
                });

                $('#search-select-province').on('select2:clear', eventClear);
                $('#search-select-city').on('select2:clear', eventClear);
                $('#search-select-district').on('select2:clear', eventClear);
                $('#search-select-subdistrict').on('select2:clear', eventClear);

                $('.select-wilayah').on('select2:select', function(e) {
                    // Prevent default button action
                    e.preventDefault();
                    // Do submit
                    console.log(e.target.id);

                    let selectId = e.target.id

                    switch (selectId) {
                        case "search-select-province":
                            $('#search-select-city').val(null).trigger('change')
                            $('#search-select-district').val(null).trigger('change')
                            $('#search-select-subdistrict').val(null).trigger('change')
                            break;
                        case "search-select-city":
                            $('#search-select-district').val(null).trigger('change')
                            $('#search-select-subdistrict').val(null).trigger('change')
                            break;
                        case "search-select-district":
                            $('#search-select-subdistrict').val(null).trigger('change')
                            break;

                        default:
                            break;
                    }

                    document.getElementById('form-search').submit()
                });
            });
        </script>
    @endpush
</x-app-layout>
