<x-app-layout>
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Mobile /</span> Data Iklan</h4>
    </x-slot>
    {{-- End Header --}}

    <div class="row">
        <div class="col-md-12">
            <!-- Striped Rows -->
            <div class="card">
                <div class="row">
                    <div class="col-md-4">
                        <div class="float-start">
                            <!-- Search -->
                            <form action="{{ route('bitanic.advertisement.index') }}" method="GET" id="form-search">
                                <div class="row p-1">
                                    <div class="col-md-12 m-1">
                                        <div class="input-group">
                                            <span class="input-group-text bg-primary text-white" style="cursor: pointer;" onclick="document.getElementById('form-search').submit()">
                                                <i class="bx bx-search"></i>
                                            </span>
                                            <input type="text" class="form-control shadow-none"
                                                placeholder="Cari Judul..." aria-label="Cari Judul..." name="search"
                                                value="{{ request()->query('search') }}" />
                                        </div>
                                    </div>
                                    <div class="col-md-12 m-1">
                                        <div class="input-group">
                                            <span class="input-group-text bg-primary text-white"><small>Tanggal Dimulai</small></span>
                                            <input type="date" id="date-search" class="form-control input-search"
                                                name="tanggal_dimulai" title="Search Tanggal Dimulai"
                                                value="{{ request()->query('tanggal_dimulai')? now()->parse(request()->query('tanggal_dimulai'))->format('Y-m-d'): null }}" />
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <!-- /Search -->
                        </div>
                    </div>
                    <div class="col-md-4"></div>
                    <div class="col-md-4">
                        <div class="float-end m-3">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#modalForm" data-input-judul="" data-input-keterangan=""
                                data-input-tanggal-mulai=""
                                data-input-id="add" title="Tambah Iklan">
                                <i class="bx bx-plus"></i>&nbsp;Tambah
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Judul</th>
                                <th>Keterangan</th>
                                <th>Tanggal Dimulai</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($data as $advertisement)
                                <tr>
                                    <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                                    <td>{{ $advertisement->title }}</td>
                                    <td>{{ Str::limit($advertisement->description, 20, '...') }}
                                        <span
                                        data-bs-toggle="tooltip"
                                        data-bs-offset="0,4"
                                        data-bs-placement="right"
                                        data-bs-html="true"
                                        title="<i class='bx bx-trending-up bx-xs' ></i> <span>Klik Untuk Lihat Keterangan</span>">
                                        <i class='bx bx-info-circle' style="cursor: pointer;" data-bs-toggle="popover"
                                            data-bs-offset="0,14" data-bs-placement="top" data-bs-html="true"
                                            data-bs-content="<p>{{ $advertisement->description }}</p>" title="Keterangan"></i>
                                        </span>
                                    </td>
                                    <td>{{ $advertisement->ads_start }}</td>
                                    <td>
                                        <button type="button" class="btn btn-icon btn-info btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#modalFoto" data-foto="{{ asset($advertisement->picture) }}"
                                            data-input-id="{{ $advertisement->id }}">
                                            <i class="bx bx-image"></i>
                                        </button>
                                        <button type="button" class="btn btn-icon btn-warning btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#modalForm" data-input-judul="{{ $advertisement->title }}"
                                            data-input-keterangan="{{ $advertisement->description }}"
                                            data-input-tanggal-mulai="{{ now()->parse($advertisement->ads_start)->format('Y-m-d') }}T{{ now()->parse($advertisement->ads_start)->format('H:i') }}"
                                            data-input-id="{{ $advertisement->id }}"
                                            title="Edit Iklan">
                                            <i class="bx bx-edit-alt"></i>
                                        </button>
                                        <button type="button" data-id="{{ $advertisement->id }}" data-name="{{ $advertisement->title }}"
                                            class="btn btn-icon btn-danger btn-sm btn-delete"
                                            title="Hapus Iklan">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center" colspan="5">Tidak ada data</td>
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

    @include('bitanic.advertisement._modal-form')
    @include('bitanic.advertisement._modal-foto')

    @push('scripts')
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
                                // validator.validate()
                            } else {
                                modalTitle.textContent = 'Tambah'
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

                showSpinner()

                // Show loading indication
                submitButton.setAttribute('data-kt-indicator', 'on');

                // Disable button to avoid multiple click
                submitButton.disabled = true;

                // Simulate form submission. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                let url, formSubmited;
                const editOrAdd = document.getElementById('data-input-id');
                const formData = new FormData();
                let myFoto = document.getElementById('data-input-foto').files;

                if (myFoto.length > 0) {
                    formData.append("picture", myFoto[0])
                }

                formData.append("title", document.getElementById('data-input-judul').value)
                formData.append("description", document.getElementById('data-input-keterangan').value)
                formData.append("ads_start", document.getElementById('data-input-tanggal-mulai').value)

                if (editOrAdd.value != 'add') {
                    url = "{{ route('bitanic.advertisement.update', 'ID') }}".replace('ID', editOrAdd.value)
                    formData.append("_method", 'PUT')
                } else {
                    url = "{{ route('bitanic.advertisement.store') }}"
                }

                myModal.toggle()

                const settings = {
                    method: 'POST',
                    headers: {
                        'x-csrf-token': '{{ csrf_token() }}'
                    },
                    body: formData
                }

                const [data, error] = await yourRequest(url, settings)

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
                modalTitle.textContent = 'Foto Iklan'

                for (let index = 0; index < button.attributes.length; index++) {
                    if (button.attributes[index].nodeName.includes('data-foto')) {
                        document.getElementById('iframe').src = button.attributes[index].nodeValue
                    }
                }

            })

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

                const btnDelete = document.querySelectorAll('.btn-delete')

                btnDelete.forEach(element => {
                    element.addEventListener('click', e => {
                        handleDeleteRows("{{ route('bitanic.advertisement.destroy', 'ID') }}".replace('ID', e.target.dataset.id), "{{ csrf_token() }}", e.target.dataset.name)
                    })
                });

                const inputSearch = document.querySelectorAll('.input-search')
                inputSearch.forEach(eInput => {
                    eInput.addEventListener('change', e => {
                        document.getElementById('form-search').submit()
                    })
                });
            });
        </script>
    @endpush
</x-app-layout>
