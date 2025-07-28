<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
    <style>
        .set-avatar-ratio {
            object-fit: cover;
            aspect-ratio: 3/2;
        }

        i {
            pointer-events: none;
        }
    </style>
    @endpush
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Setting / </span> Account</h4>
    </x-slot>
    {{-- End Header --}}

    <div class="row">
        <div class="col-12">

            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    {{ session()->get('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card mb-4">
                <h5 class="card-header">Profile Details</h5>
                <!-- Account -->
                <div class="card-body">
                    <div class="d-flex align-items-start align-items-sm-center gap-4">
                        <img src="{{ asset($user->farmer->picture) }}" alt="user-avatar" class="d-block rounded set-avatar-ratio" height="100" width="100" id="uploadedAvatar" />
                        <div class="button-wrapper">
                            <label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                                <span class="d-none d-sm-block">Upload new photo</span>
                                <i class="bx bx-upload d-block d-sm-none"></i>
                                <input type="file" id="upload" class="account-file-input" hidden accept="image/png, image/jpeg" />
                            </label>
                            <button type="button" class="btn btn-outline-secondary account-image-reset mb-4">
                                <i class="bx bx-reset d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Reset</span>
                            </button>

                            <p class="text-muted mb-0">Allowed JPG or PNG. Max size of 10M</p>
                        </div>
                    </div>
                </div>
                <hr class="my-0" />
                <div class="card-body">
                    <!-- Validation Errors -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />
                    <form id="formAccountSettings" method="POST" action="{{ route('bitanic.setting-account.update') }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <input type="file" class="d-none" name="picture">
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">Nama Lengkap</label>
                                <input class="form-control" type="text" id="name" name="name" value="{{ $user->name }}" autofocus />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="nik" class="form-label">NIK</label>
                                <input class="form-control" type="text" name="nik" id="nik" value="{{ $user->farmer->nik }}" />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label" for="phoneNumber">Nomor Handphone</label>

                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon13">+62</span>
                                    <input type="number" class="form-control" id="phoneNumber" name="phone_number"
                                        placeholder="XXX XXXX XXXX" value="{{ substr($user->phone_number, 2) }}" aria-describedby="basic-addon13" />
                                </div>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="gender" class="form-label">Jenis Kelamin</label>
                                <select class="form-select" id="gender" name="gender" aria-label="Default select example">
                                    <option value="l">Laki-laki</option>
                                    <option value="p">Perempuan</option>
                                </select>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="birth-date" class="form-label">Tanggal Lahir</label>
                                <input type="date" class="form-control" id="birth-date" name="birth_date" value="{{ $user->farmer->birth_date }}" />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="address" class="form-label">Alamat</label>
                                <input type="text" class="form-control" id="address" name="address" placeholder="Address" value="{{ $user->farmer->address }}" />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="data-input-province" class="form-label">Provinsi</label>
                                <select class="form-select" id="data-input-province" name="province" aria-label="Default select example" aria-describedby="provinceFormControlHelp">
                                </select>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="data-input-city" class="form-label">Kabupaten/Kota</label>
                                <select class="form-select" id="data-input-city" name="city" aria-label="Default select example" aria-describedby="cityFormControlHelp" disabled>
                                </select>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="data-input-district" class="form-label">Kecamatan</label>
                                <select class="form-select" id="data-input-district" name="district" aria-label="Default select example" aria-describedby="districtFormControlHelp" disabled>
                                </select>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="data-input-subdistrict" class="form-label">Desa</label>
                                <select class="form-select" id="data-input-subdistrict" name="subdistrict" aria-label="Default select example" aria-describedby="subdistrictFormControlHelp" disabled>
                                </select>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="data-input-farmer-group" class="form-label">Kelompok Petani</label>
                                <select class="form-select" id="data-input-farmer-group" name="farmer_group"
                                    aria-label="Default select example">
                                    <option value="">Tidak Memiliki</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="submit" class="btn btn-primary me-2">Save changes</button>
                            <button type="reset" class="btn btn-outline-secondary">Cancel</button>
                        </div>
                    </form>
                </div>
                <!-- /Account -->
            </div>
            <div class="card mb-4">
                <h5 class="card-header">Verifikasi KTP</h5>
                <div class="card-body">
                    <div class="mb-3 col-12 mb-0">
                        @if (!$user->farmer->is_ktp_uploaded)
                            <div class="alert alert-warning">
                                <h6 class="alert-heading fw-bold mb-1">Harap upload foto KTP untuk verifikasi akun anda</h6>
                            </div>
                        @elseif ($user->farmer->is_ktp_validated === null)
                            <div class="alert alert-secondary">
                                <h6 class="alert-heading fw-bold mb-1">KTP sedang diverifikasi.</h6>
                            </div>
                            <a href="{{ route('bitanic.setting-account.show-ktp') }}" target="__blank">Lihat KTP</a>
                        @elseif ($user->farmer->is_ktp_validated === 0)
                            <div class="alert alert-danger">
                                <h6 class="alert-heading fw-bold mb-1">Verifikasi kamu DITOLAK. Upload kembali KTP untuk diverifikasi ulang. Pastikan
                                    foto KTP yang kamu kirim jelas.</h6>
                            </div>
                        @elseif ($user->farmer->is_ktp_validated === 1)
                            <div class="alert alert-success">
                                <h6 class="alert-heading fw-bold mb-1">Verifikasi KTP DITERIMA.</h6>
                            </div>
                            <a href="{{ route('bitanic.setting-account.show-ktp') }}" target="__blank">Lihat KTP</a>
                        @endif
                    </div>
                    @if (!$user->farmer->is_ktp_uploaded || $user->farmer->is_ktp_validated === 0)
                    <form id="formAccountDeactivation" method="POST" action="{{ route('bitanic.setting-account.update-ktp') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="birth-date" class="form-label">Foto KTP</label>
                            <input class="form-control" type="file" name="ktp" id="ktpFile" accept="image/png, image/jpg, image/jpeg"
                              aria-describedby="ktpFileHelp"/>
                            <div id="ktpFileHelp" class="form-text">
                              Format: PNG,JPG,JPEG; Size: 10MB;
                            </div>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary">Upload KTP</button>
                            @if ($user->farmer->is_ktp_validated === 0)
                                <a href="{{ route('bitanic.setting-account.show-ktp') }}" target="__blank" class="ms-3">Lihat KTP</a>
                            @endif
                        </div>
                    </form>
                    @endif
                </div>
            </div>
            <div class="card">
                <h5 class="card-header">Delete Account</h5>
                <div class="card-body">
                    <div class="mb-3 col-12 mb-0">
                        <div class="alert alert-warning">
                            <h6 class="alert-heading fw-bold mb-1">Are you sure you want to delete your account?</h6>
                            <p class="mb-0">Once you delete your account, there is no going back. Please be certain.</p>
                        </div>
                    </div>
                    <form id="formAccountDeactivation" method="POST" action="{{ route('bitanic.setting-account.destroy-account') }}">
                        @csrf
                        @method('DELETE')
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="accountActivation" id="accountActivation" />
                            <label class="form-check-label" for="accountActivation">I confirm my account deactivation</label>
                        </div>
                        <button type="submit" class="btn btn-danger deactivate-account">Deactivate Account</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('theme/js/ui-popover.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script>
        showSpinner()

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

        const getCity = async (selectId, setAll = false, province = null, selectedValue = null) => {
            if (!province) {
                //remove options physically from the HTML
                $('#' + selectId).find("option").remove();
                $('#' + selectId).attr('disabled', 'disabled');
                return 0;
            }

            $('#' + selectId).removeAttr('disabled');

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
                    data: cities,
                    placeholder: "Pilih wilayah",
                    allowClear: true
                });
            }

        }

        const getDistricts = async (selectId, setAll = false, city = null, selectedValue = null) => {
            if (!city) {
                //remove options physically from the HTML
                $('#' + selectId).find("option").remove();
                $('#' + selectId).attr('disabled', 'disabled');
                return 0;
            }

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
                    data: districts,
                    placeholder: "Pilih wilayah",
                    allowClear: true
                });
            }
        }

        const getSubdistricts = async (selectId, setAll = false, district = null, selectedValue = null) => {
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
                    data: groups,
                    placeholder: "Pilih wilayah",
                    allowClear: true
                });

            }

        }

        const requestUpdateProfilePicture = async (filePicture) => {
            try {
                showSpinner()

                const formData = new FormData();
                formData.append("picture", filePicture)

                let settings = {
                    method: 'POST',
                    headers: {
                        'x-csrf-token': '{{ csrf_token() }}',
                        // 'Content-Type': 'multipart/form-data'
                    },
                    body: formData
                }

                let response = await fetch("{{ route('bitanic.setting-account.update-picture') }}", settings)

                deleteSpinner()

                if (response.ok) {
                    Swal.fire({
                        text: "Berhasil diubah",
                        icon: "success",
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                } else if (response.status >= 400 && response.status <= 500) {
                    let json = await response.json()
                    let errorMessage = json.messages

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
                } else if (!response.ok) {
                    console.log(response);
                }

            } catch (error) {
                console.error(error)
                deleteSpinner()
            }
        }

        document.addEventListener("DOMContentLoaded", () => {
            console.log("Hello World!");
            deleteSpinner()

            $('#data-input-province').select2({
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

            getProvinces("{{ $user?->subdistrict?->district?->city?->province_id }}" ?? null, false, 'data-input-province')
            getCity('data-input-city', false, "{{ $user?->subdistrict?->district?->city?->province_id }}" ?? null, "{{ $user?->subdistrict?->district?->city_id }}" ?? null)
            getDistricts('data-input-district', false, "{{ $user?->subdistrict?->district?->city_id }}" ?? null, "{{ $user?->subdistrict?->dis_id }}" ?? null)
            getSubdistricts('data-input-subdistrict', false, "{{ $user?->subdistrict?->dis_id }}" ?? null, "{{ $user?->subdis_id }}" ?? null)
            getFarmerGroups("{{ $user?->subdis_id }}" ?? null, "{{ $user?->farmer?->group_id }}" ?? null)
            // getProvinces(provinceSelect, true, 'search-select-province')

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
                getCity('data-input-city', false, this.value)
                $('#data-input-city').find("option").remove();
                $('#data-input-district').find("option").remove();
                $('#data-input-subdistrict').find("option").remove();
            });
            $('#data-input-city').on('select2:select', function(e) {
                // Do submit
                getDistricts('data-input-district', false, this.value)
                $('#data-input-district').find("option").remove();
                $('#data-input-subdistrict').find("option").remove();
            });
            $('#data-input-district').on('select2:select', function(e) {
                // Do submit
                getSubdistricts('data-input-subdistrict', false, this.value)
                $('#data-input-subdistrict').find("option").remove();
            });
            $('#data-input-subdistrict').on('select2:select', function(e) {
                // Do submit
                getFarmerGroups(this.value)
            });

            const deactivateAcc = document.querySelector('#formAccountDeactivation');

            // Update/reset user image of account page
            let accountUserImage = document.getElementById('uploadedAvatar');
            const fileInput = document.querySelector('.account-file-input'),
                resetFileInput = document.querySelector('.account-image-reset');
            let list = new DataTransfer();

            if (accountUserImage) {
                const resetImage = accountUserImage.src;
                fileInput.onchange = () => {
                    if (fileInput.files[0]) {
                        accountUserImage.src = window.URL.createObjectURL(fileInput.files[0]);
                        list.items.clear()
                        list.items.add(fileInput.files[0])

                        document.querySelector('[name="picture"]').files = list.files
                        // requestUpdateProfilePicture(fileInput.files[0])
                    }
                };
                resetFileInput.onclick = () => {
                    fileInput.value = '';
                    accountUserImage.src = resetImage;
                    list.items.clear()
                    document.querySelector('[name="picture"]').files = list.files
                };
            }
        });
    </script>
    @endpush
</x-app-layout>
