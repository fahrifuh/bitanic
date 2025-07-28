<x-app-layout>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
    @endpush

    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master /</span> Data Kebun Terjangkit Hama</h4>
    </x-slot>
    {{-- End Header --}}

    <div class="row">
        <div class="col-md-12">
            <!-- Striped Rows -->
            <div class="card">
                <div class="row">
                    <div class="col-md-12">
                        <div class="float-start">
                            <!-- Search -->
                            <form action="" method="GET" id="form-search">
                                <div class="input-group m-3">
                                    <span class="input-group-text bg-primary text-white"
                                        style="cursor: pointer;"
                                        onclick="document.getElementById('form-search').submit()"
                                        title="Cari">
                                        <i class="bx bx-search"></i>
                                    </span>
                                    <input type="text" class="form-control shadow-none"
                                        placeholder="Cari nama..." aria-label="Cari nama hama, nama kebun..." name="search"
                                        value="{{ request()->query('search') }}" />
                                </div>
                            </form>
                            <!-- /Search -->
                        </div>
                        <div class="float-end m-3">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#modalForm" data-input-pest-id="" data-input-pest-name=""
                                data-input-garden-id="0" data-input-invected-date="" data-input-id="add"
                                title="Tambah Kebun Terinfeksi">
                                <i class="bx bx-plus"></i>&nbsp;Tambah
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive text-wrap">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th>Nama Hama</th>
                                <th>Kebun</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($data as $invected)
                                <tr>
                                    <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                                    <td>
                                        <a href="javascript:;" type="button" class="avatar pull-up" data-bs-toggle="modal"
                                            data-bs-target="#modalFoto" data-foto="{{ asset($invected->picture) }}"
                                            data-input-id="{{ $invected->id }}" title="Klik untuk melihat foto">
                                            <img src="{{ asset($invected->picture) }}" alt="Avatar" class="rounded" />
                                        </a>
                                        {{ optional($invected->pest)->pest_type ?? $invected->pest_name }}
                                    </td>
                                    <td>{{ $invected->garden->land->name }}</td>
                                    <td>{{ carbon_format_id_flex(now()->parse($invected->invected_date)->format('d-m-Y'), '-', '/') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $invected->status == 'addressed' ? 'success' : 'danger' }}"
                                            onclick="updateStatus('{{ $invected->status }}', {{ $invected->id }})"
                                            title="Klik untuk mengubah status menjadi {{ $invected->status == 'addressed'
                                                        ? 'belum ditangani'
                                                        : 'Sudah ditangani' }}" style="cursor: pointer;">
                                            {{ $invected->status == 'addressed' ? 'Sudah ditangani' : 'belum ditangani' }}
                                            &nbsp;
                                            <i class="bx bx-edit-alt"></i>
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-warning btn-icon btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#modalForm" data-input-pest-id="{{ $invected->pest_id }}"
                                            data-input-pest-name="{{ $invected->pest_name }}"
                                            data-input-garden-id="{{ $invected->garden_id }}"
                                            data-input-invected-date="{{ $invected->invected_date }}"
                                            data-input-id="{{ $invected->id }}" title="Edit Data">
                                            <i class="bx bx-edit"></i>
                                        </button>
                                        <button type="button" onclick="destroy({{ $invected->id }}, '{{ optional($invected->pest)->name ?? $invected->pest_name }}')"
                                            class="btn btn-danger btn-icon btn-sm" title="Hapus">
                                            <i class="bx bx-trash event-none"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data</td>
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

    @include('bitanic.invected-gardens._modal-form')
    @include('bitanic.invected-gardens._modal-picture')

    @push('scripts')
        <script src="{{ asset('js/select2.min.js') }}"></script>
        <script src="{{ asset('theme/js/ui-popover.js') }}"></script>
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
                                $('#alert').removeClass('d-none');
                                getPests(document.getElementById('data-input-pest-id').value)
                                getGardens(document.getElementById('data-input-garden-id').value)
                                if (document.getElementById('data-input-pest-id').value) {
                                    document.getElementById('data-input-pest-name').disabled = true
                                } else {
                                    document.getElementById('data-input-pest-name').disabled = false
                                }

                                // validator.validate()
                            } else {
                                modalTitle.textContent = 'Tambah'
                                $('#alert').addClass('d-none');
                            }
                        }
                    }
                }

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
                const picture = document.getElementById('data-input-foto').files
                const pestID = document.getElementById('data-input-pest-id').value
                const pestName = document.getElementById('data-input-pest-name').value

                if (picture.length > 0) {
                    formData.append("picture", picture[0])
                }

                if (pestID) {
                    formData.append("pest_id", document.getElementById('data-input-pest-id').value)
                }
                if (pestName) {
                    formData.append("pest_name", document.getElementById('data-input-pest-name').value)
                }
                formData.append("garden_id", document.getElementById('data-input-garden-id').value)
                formData.append("invected_date", document.getElementById('data-input-invected-date').value)

                if (editOrAdd.value != 'add') {
                    url = "{{ route('bitanic.invected-gardens.update', 'ID') }}".replace('ID', editOrAdd.value)
                    formData.append("_method", 'PUT')
                } else {
                    url = "{{ route('bitanic.invected-gardens.store') }}"
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
                    button: false,
                });
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

            const destroy = (id, name) => {
                handleDeleteRows("{{ route('bitanic.invected-gardens.destroy', 'ID') }}".replace('ID', id), "{{ csrf_token() }}", name)
            }

            const updateStatus = async (status, id) => {
                let textConfirmation = "Apakah anda yakin ingin mengubah status menjadi sudah ditangani?"
                let changeStatus = 'addressed'

                if (status == 'addressed') {
                    textConfirmation = "Apakah anda yakin ingin mengubah status menjadi belum ditangani?"
                    changeStatus = 'unaddressed'
                }

                const result = await Swal.fire({
                    text: textConfirmation,
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Iya, ubah status!",
                    cancelButtonText: "Tidak, batalkan",
                    customClass: {
                        confirmButton: "btn fw-bold btn-danger",
                        cancelButton: "btn fw-bold btn-active-light-primary"
                    }
                })

                if (result.value) {
                    showSpinner()

                    const formData = new FormData();
                    let url = "{{ route('bitanic.invected-gardens.update-status', 'ID') }}".replace('ID', id)
                    formData.append("_method", 'PUT')
                    formData.append("status", changeStatus)

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
                        button: false,
                    });
                    window.location.reload();
                }
            }

            const getGardens = async (selectedValue = null) => {
                let url = "{{ route('bitanic.get-garden') }}"

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

                if (data.gardens.length > 0) {
                    const gardens = data.gardens.map((e) => {
                        return {
                            id: e.id,
                            text: e.name,
                            selected: (selectedValue && selectedValue == e.id) ? true : false
                        }
                    })

                    if (!selectedValue) {
                        gardens.unshift({id: '', text: ''})
                    }

                    $('#data-input-garden-id').select2({
                        dropdownParent: $('#modalForm'),
                        data: gardens,
                        placeholder: 'Pilih Kebun',
                        allowClear: true,
                        language: {
                            noResults: function () {
                                return "Kebun tidak ditemukan";
                            }
                        },
                        escapeMarkup: function (markup) {
                            return markup;
                        }
                    });
                }
            }

            const getPests = async (selectedValue = null) => {
                let url = "{{ route('web.pests.get') }}"

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

                if (data.pests.length > 0) {
                    const pests = data.pests.map((e) => {
                        return {
                            id: e.id,
                            text: e.pest_type,
                            selected: (selectedValue && selectedValue == e.id) ? true : false
                        }
                    })

                    if (!selectedValue) {
                        pests.unshift({id: '', text: ''})
                    }

                    $('#data-input-pest-id').select2({
                        dropdownParent: $('#modalForm'),
                        data: pests,
                        placeholder: 'Pilih Hama',
                        allowClear: true,
                        language: {
                            noResults: function () {
                                return "Hama tidak ditemukan";
                            }
                        },
                        escapeMarkup: function (markup) {
                            return markup;
                        }
                    });
                }
            }

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

                getPests()
                getGardens()

                $('#data-input-pest-id').on('select2:select', function (e) {
                    document.getElementById('data-input-pest-name').disabled = true
                });
                $('#data-input-pest-id').on('select2:clear', function (e) {
                    document.getElementById('data-input-pest-name').disabled = false
                });
            });
        </script>
    @endpush
</x-app-layout>
