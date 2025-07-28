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
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master /</span> Data Kecamatan</h4>
    </x-slot>
    {{-- End Header --}}

    <div class="row">
        <div class="col-md-12">
            <!-- Striped Rows -->
            <div class="card">
                <div class="row">
                    <div class="col-md-5">
                        <div class="float-start">
                            <!-- Search -->
                            <form action="" method="GET" id="form-search">
                                <div class="row g-2 p-3">
                                    <div class="col-md-12">
                                        <div class="input-group">
                                            <span class="input-group-text bg-primary text-white"
                                                style="cursor: pointer;"
                                                onclick="document.getElementById('form-search').submit()">
                                                <i class="bx bx-search"></i>
                                            </span>
                                            <input type="text" class="form-control shadow-none"
                                                placeholder="Cari Kecamatan..." aria-label="Cari Kecamatan..." name="search"
                                                value="{{ request()->query('search') }}" />
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="input-group flex-nowrap">
                                            <span class="input-group-text bg-primary text-white"><small>Kabupaten/Kota</small></span>
                                            <select class="form-select" id="select-city" name="city"
                                                aria-label="Default select example">
                                                <option value="all">Semua</option>
                                                @forelse ($cities as $id => $city_name)
                                                    <option value="{{ $id }}"
                                                        @if (request()->query('city') == $id) selected @endif>{{ $city_name }}
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
                    </div>
                    <div class="col-md-3"></div>
                    <div class="col-md-4">
                        <div class="float-end m-3">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#modalForm" data-input-dis-name=""
                                data-input-city-id="" data-input-id="add"
                                title="Tambah Kecamatan">
                                <i class="bx bx-plus"></i>&nbsp;Tambah
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive text-wrap">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Kabupaten/Kota</th>
                                <th>Nama Kecamatan</th>
                                <th>Total Desa</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($data as $district)
                                <tr>
                                    <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                                    <td>{{ $district->city->city_name }}</td>
                                    <td>{{ $district->dis_name }}</td>
                                    <td>{{ $district->subdistrict_count }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-icon btn-warning my-1" data-bs-toggle="modal"
                                            data-bs-target="#modalForm"
                                            data-input-dis-name="{{ $district->dis_name }}"
                                            data-input-city-id="{{ $district->city_id }}"
                                            data-input-id="{{ $district->id }}"
                                            title="Edit Kecamatan"><i
                                                class="bx bx-edit-alt"></i>
                                        </button>
                                        <button class="btn btn-sm btn-icon btn-danger my-1"
                                            onclick="destroyDistrict({{ $district->id }}, '{{ $district->dis_name }}')"
                                            href="javascript:void(0);" title="Hapus Kecamatan"><i class="bx bx-trash"></i>
                                        </button>
                                    </td>
                                </tr>

                            @empty

                                <tr>
                                    <td colspan="5" class="text-center">Tidak Ada Data</td>
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

    @include('bitanic.wilayah.kecamatan._modal-form')

    @push('scripts')
    <script src="{{ asset('js/select2.min.js') }}"></script>
        <script>
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
                                getCity(null, button.attributes['data-input-city-id'].nodeValue)
                                // validator.validate()
                            } else {
                                getCity()
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

                formData.append("dis_name", document.getElementById('data-input-dis-name').value)
                formData.append("city_id", document.getElementById('data-input-city-id').value)

                if (editOrAdd.value != 'add') {
                    url = "{{ route('bitanic.district.update', 'ID') }}".replace('ID', document.getElementById(
                        'data-input-id').value)
                    formData.append('_method', 'PUT')
                } else {
                    url = "{{ route('bitanic.district.store') }}"
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

            const destroyDistrict = (id, name) => {
                handleDeleteRows("{{ route('bitanic.district.destroy', 'ID') }}".replace('ID', id), "{{ csrf_token() }}", name)
            }


            const getCity = async (province = null, selectedValue = null) => {
                let url = "{{ route('web.wilayah.cities') }}"
                if (province) {
                    url = "{{ route('web.wilayah.cities', ['province' => 'ID']) }}".replace('ID', province)
                }

                const [data, error] = await yourRequest(url, {
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
                    $('#data-input-city-id').select2("destroy");

                    //remove options physically from the HTML
                    $('#data-input-city-id').find("option").remove();

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

                    $('#data-input-city-id').select2({
                        dropdownParent: $('#modalForm'),
                        data: cities,
                        placeholder: 'Pilih Kabupaten/Kota',
                        allowClear: true
                    });
                }

            }

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

                $('#data-input-city-id').select2({
                    dropdownParent: $('#modalForm')
                });
                $('#select-city').select2({
                    placeholder: 'Semua Province'
                });

                const selectSearch = document.querySelectorAll('.select-search')
                selectSearch.forEach(eSelect => {
                    eSelect.addEventListener('change', e => {
                        document.getElementById('form-search').submit()
                    })
                });

                $('#select-city').on('select2:select', function (e) {
                    // Do submit
                    document.getElementById('form-search').submit()
                });
            });
        </script>
    @endpush
</x-app-layout>
