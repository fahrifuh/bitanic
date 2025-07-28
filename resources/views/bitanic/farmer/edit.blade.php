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
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master / <a
                    href="{{ route('bitanic.farmer.index') }}">Data Pengguna</a> / <a
                    href="{{ route('bitanic.farmer.show', $user->id) }}">{{ $user->name }}</a> /</span> Edit </h4>
    </x-slot>
    {{-- End Header --}}

    @if (session()->has('success'))
        <x-alert-message class="alert-success">{{ session()->get('success') }}</x-alert-message>
    @endif

    <div class="row">
        <div class="col-md-12">
            <!-- Striped Rows -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('bitanic.farmer.update', $user->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />

                        <div class="row" id="alert">
                            <div class="col mb-3">
                                <div class="rounded p-3 bg-info text-white" role="alert">Password dan Foto
                                    <b>TIDAK WAJIB</b> diisi!</div>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col mb-3">
                                        <label for="data-input-type" class="form-label">Tipe Pengguna</label>
                                        <select class="form-select" id="data-input-type" name="type"
                                            aria-label="Default select example">
                                            <option value="1" @if($user->farmer->type == 1) selected @endif>1</option>
                                            <option value="2" @if($user->farmer->type == 2) selected @endif>2</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col mb-3">
                                        <label for="data-input-category" class="form-label">Kategori Pengguna</label>
                                        <select class="form-select" id="data-input-category" name="category"
                                            aria-label="Default select example">
                                            @foreach (farmerCategory() as $key => $farmerCategory)
                                                <option value="{{ $key }}" @if($user->farmer->category == $key) selected @endif>
                                                    {{ $farmerCategory }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col mb-3">
                                        <label for="data-input-nama" class="form-label">Nama Lengkap</label>
                                        <input type="text" id="data-input-nama" class="form-control" name="name"
                                            autocomplete="name" value="{{ $user->name }}" />
                                    </div>
                                </div>
                                <div class="row g-2">
                                    <div class="col mb-3">
                                        <label for="data-input-nik" class="form-label">NIK</label>
                                        <input type="number" class="form-control" id="data-input-nik" name="nik" value="{{ $user->farmer->nik }}" />
                                    </div>
                                    <div class="col mb-3">
                                        <label for="data-input-phone-number" class="form-label">Nomor HP</label>
                                        <div class="input-group">
                                            <span class="input-group-text" id="basic-addon13">+62</span>
                                            <input type="number" class="form-control" id="data-input-phone-number"
                                                name="phone_number" placeholder="8xxxxxxxxx"
                                                aria-describedby="basic-addon13" value="{{ substr($user->phone_number, 2) }}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col mb-3">
                                        <label for="data-input-password" class="form-label">Password</label>
                                        <input type="password" id="data-input-password" class="form-control"
                                            name="password" aria-describedby="passwordHelp" />
                                        <div id="passwordHelp" class="form-text">*min 8 karakter</div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col mb-3">
                                        <label for="data-input-password-confirm" class="form-label">Password
                                            Konfirmasi</label>
                                        <input type="password" id="data-input-password-confirm" class="form-control"
                                            name="password_confirmation" />
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col mb-0">
                                        <label for="data-input-gender" class="form-label">Jenis Kelamin</label>
                                        <select class="form-select" id="data-input-gender" name="gender"
                                            aria-label="Default select example">
                                            <option value="l" @if($user->farmer->gender == 'l') selected @endif>Laki-laki</option>
                                            <option value="p" @if($user->farmer->gender == 'p') selected @endif>Perempuan</option>
                                        </select>
                                    </div>
                                    <div class="col mb-0">
                                        <label for="data-input-birth-date" class="form-label">Tanggal Lahir</label>
                                        <input type="date" class="form-control" id="data-input-birth-date"
                                            name="birth_date" value="{{ $user->farmer->birth_date }}" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col mb-3">
                                        <label for="data-input-foto" class="form-label">Foto</label>
                                        <input class="form-control" type="file" id="data-input-foto"
                                            name="foto" accept="image/png, image/jpg, image/jpeg"
                                            aria-describedby="pictureHelp" />
                                        <div id="pictureHelp" class="form-text">Format gambar JPG, JPEG, PNG. Maks.
                                            2MB</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row g-2 mb-3">
                                    <div class="col">
                                        <label for="data-input-province" class="form-label">Provinsi</label>
                                        <select class="form-select" id="data-input-province" name="province"
                                            aria-label="Default select example"
                                            aria-describedby="provinceFormControlHelp">
                                        </select>
                                        <div id="provinceFormControlHelp" class="form-text">
                                            <a href="{{ route('bitanic.province.index') }}" target="_blank">+ Tambah
                                                data provinsi</a>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <label for="data-input-city" class="form-label">Kabupaten/Kota</label>
                                        <select class="form-select" id="data-input-city" name="city"
                                            aria-label="Default select example" aria-describedby="cityFormControlHelp"
                                            disabled>
                                        </select>
                                        <div id="cityFormControlHelp" class="form-text">
                                            <a href="{{ route('bitanic.city.index') }}" target="_blank">+ Tambah data
                                                Kabupaten/Kota</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col">
                                        <label for="data-input-district" class="form-label">Kecamatan</label>
                                        <select class="form-select" id="data-input-district" name="district"
                                            aria-label="Default select example"
                                            aria-describedby="districtFormControlHelp" disabled>
                                        </select>
                                        <div id="districtFormControlHelp" class="form-text">
                                            <a href="{{ route('bitanic.district.index') }}" target="_blank">+ Tambah
                                                data Kecamatan</a>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <label for="data-input-subdistrict" class="form-label">Desa</label>
                                        <select class="form-select" id="data-input-subdistrict" name="subdistrict"
                                            aria-label="Default select example"
                                            aria-describedby="subdistrictFormControlHelp" disabled>
                                        </select>
                                        <div id="subdistrictFormControlHelp" class="form-text">
                                            <a href="{{ route('bitanic.subdistrict.index') }}" target="_blank">+
                                                Tambah data Desa</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col mb-3">
                                        <label for="data-input-farmer-group" class="form-label">Kelompok
                                            Pengguna</label>
                                        <select class="form-select" id="data-input-farmer-group" name="farmer_group"
                                            aria-label="Default select example">
                                            <option value="">Tidak Memiliki</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col mb-3">
                                        <label for="data-input-address" class="form-label">Alamat</label>
                                        <textarea class="form-control" id="data-input-address"
                                            name="address" rows="2" placeholder="Jl. XXX">{{ $user->farmer->address }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary w-100" id="submit-btn">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!--/ Striped Rows -->
        </div>
    </div>

    @php
        $subdistrict = $user->subdistrict;
        $district = optional($subdistrict)->district;
        $city = optional($district)->city;
        $province = optional($city)->province;
    @endphp

    @push('scripts')
        <script src="{{ asset('leaflet/leaflet.js') }}"></script>
        <script src="{{ asset('theme/js/ui-popover.js') }}"></script>
        <script src="{{ asset('js/extend.js') }}"></script>
        <script src="{{ asset('js/select2.min.js') }}"></script>
        <!-- <script src="{{ asset('js/extra.js') }}"></script> -->
        <script>
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
                        dropdownParent: $(document.body),
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
                        dropdownParent: $(document.body),
                        data: groups,
                        placeholder: "Pilih wilayah",
                        allowClear: true
                    });
                }

            }

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");
                deleteSpinner()

                $('#data-input-province').select2({
                    placeholder: "Pilih wilayah",
                    allowClear: true
                });

                const provinceSelect = "{{ request()->query('province') }}"
                const citySelect = "{{ request()->query('city') }}"
                const districtSelect = "{{ request()->query('district') }}"
                const subdistrictSelect = "{{ request()->query('subdistrict') }}"


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
                    placeholder: "Pilih wilayah",
                    allowClear: true
                });

                $('#data-input-district').select2({
                    placeholder: "Pilih wilayah",
                    allowClear: true
                });

                $('#data-input-subdistrict').select2({
                    placeholder: "Pilih wilayah",
                    allowClear: true
                });

                $('#data-input-farmer-group').select2({
                    placeholder: "Pilih wilayah",
                    allowClear: true,
                    data: [{
                        id: 0,
                        text: 'Tidak Memiliki',
                        selected: true
                    }]
                });

                $('#data-input-province').on('select2:select', function(e) {
                    // Do submit
                    getCity('data-input-city', $(document.body), false, this.value)
                    $('#data-input-city').find("option").remove();
                    $('#data-input-district').find("option").remove();
                    $('#data-input-subdistrict').find("option").remove();
                });
                $('#data-input-city').on('select2:select', function(e) {
                    // Do submit
                    getDistricts('data-input-district', $(document.body), false, this.value)
                    $('#data-input-district').find("option").remove();
                    $('#data-input-subdistrict').find("option").remove();
                });
                $('#data-input-district').on('select2:select', function(e) {
                    // Do submit
                    getSubdistricts('data-input-subdistrict', $(document.body), false, this.value)
                    $('#data-input-subdistrict').find("option").remove();
                });
                $('#data-input-subdistrict').on('select2:select', function(e) {
                    // Do submit
                    getFarmerGroups(this.value)
                });

                getProvinces('{{ optional($province)->id }}' ?? null, false, 'data-input-province', $(document.body))
                getCity('data-input-city', $(document.body), false, '{{ optional($province)->id }}' ?? null, '{{ optional($city)->id }}' ?? null)
                getDistricts('data-input-district', $(document.body), false, '{{ optional($city)->id }}' ?? null, '{{ optional($district)->id }}' ?? null)
                getSubdistricts('data-input-subdistrict', $(document.body), false, '{{ optional($district)->id }}' ?? null, '{{ optional($subdistrict)->id }}' ?? null)
                getFarmerGroups('{{ optional($subdistrict)->id }}' ?? null, '{{ $user->farmer->group_id }}' ?? null)
            })
        </script>
    @endpush
</x-app-layout>
