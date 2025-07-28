<x-app-layout>
    @push('styles')
        {{-- Cluster --}}
        <style>
            .img-cover {
                object-fit: cover;
            }
        </style>
    @endpush
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master /</span> Data Tenant (Mitra Bisnis)</h4>
    </x-slot>
    {{-- End Header --}}

    @if (session()->has('success'))
    <x-alert-message class="alert-success">{{ session()->get('success') }}</x-alert-message>
    @endif

    <div class="row">
        <div class="col-md-12">
            <!-- Striped Rows -->
            <div class="card">
                <div class="row">
                    <div class="col-md-12">
                        <div class="float-start">
                            <!-- Search -->
                            <form action="" method="GET" id="form-search">
                                <div class="row g-2 p-3 pb-1">
                                    <div class="col-12">
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
                                    <div class="col-12">
                                        <div class="input-group flex-nowrap">
                                            <span class="input-group-text bg-primary text-white">
                                                <small>Tanggal Bergabung</small></span>
                                            <input type="date" id="date-search" class="form-control input-search"
                                                name="tanggal_bergabung" title="Search Tanggal Perjanjian"
                                                value="{{ request()->query('tanggal_bergabung')
                                                    ? now()->parse(request()->query('tanggal_bergabung'))->format('Y-m-d')
                                                    : null }}" />
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <!-- /Search -->
                        </div>
                        <div class="float-end m-3">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#modalForm" data-input-nama="" data-input-tanggal-bergabung=""
                                data-input-segmen-pasar="" data-input-alamat="" data-input-id="add"
                                title="Tambah data">
                                <i class="bx bx-plus"></i>&nbsp;Tambah
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive text-wrap">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Tanggal Bergabung</th>
                                <th>Segmen Pasar</th>
                                <th>Alamat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($data as $seller)
                                <tr>
                                    <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                                    <td>
                                        <a href="javascript:;" type="button" class="avatar pull-up" data-bs-toggle="modal"
                                            data-bs-target="#modalFoto" data-foto="{{ asset($seller->picture) }}"
                                            data-input-id="{{ $seller->id }}">
                                            <img src="{{ asset($seller->picture ?? 'bitanic-photo/dummy-image.png') }}" alt="Avatar" class="img-cover" />
                                        </a>
                                        {{ $seller->name }}
                                    </td>
                                    <td>{{ carbon_format_id_flex(now()->parse($seller->date_joining)->format('d-m-Y'), '-', ' ') }}</td>
                                    <td>{{ $seller->bussiness_segment }}</td>
                                    <td>{{ Str::limit($seller->address, 20, '...') }}
                                        <i class='bx bx-info-circle' style="cursor: pointer;" data-bs-toggle="popover"
                                            data-bs-offset="0,14" data-bs-placement="top" data-bs-html="true"
                                            data-bs-content="<p>{{ $seller->address }}</p>" title="Alamat"></i>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-icon btn-warning btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#modalForm" data-input-nama="{{ $seller->name }}"
                                            data-input-tanggal-bergabung="{{ $seller->date_joining }}"
                                            data-input-segmen-pasar="{{ $seller->bussiness_segment }}"
                                            data-input-alamat="{{ $seller->address }}"
                                            data-input-id="{{ $seller->id }}"
                                            title="Edit data">
                                            <i class="bx bx-edit-alt"></i>
                                        </button>
                                        <button type="button" data-id="{{ $seller->id }}" data-name="{{ $seller->name }}"
                                            class="btn btn-icon btn-danger btn-sm btn-delete" title="Hapus data"
                                            onclick="onClickDestroy('{{ $seller->id }}')">
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

    @include('bitanic.seller._modal-form')
    @include('bitanic.seller._modal-foto')

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
                                $('#alert').removeClass('d-none');

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

                if (picture.length > 0) {
                    formData.append("picture", picture[0])
                }

                formData.append("name", document.getElementById('data-input-nama').value)
                formData.append("date_joining", document.getElementById('data-input-tanggal-bergabung').value)
                formData.append("bussiness_segment", document.getElementById('data-input-segmen-pasar').value)
                formData.append("address", document.getElementById('data-input-alamat').value)

                if (editOrAdd.value != 'add') {
                    url = "{{ route('bitanic.seller.update', 'ID') }}".replace('ID', editOrAdd.value)
                    formData.append("_method", 'PUT')
                } else {
                    url = "{{ route('bitanic.seller.store') }}"
                }
                const settings = {
                    method: 'POST',
                    headers: {
                        'x-csrf-token': '{{ csrf_token() }}'
                    },
                    body: formData
                }

                const [data, error] = await yourRequest(url, settings)

                deleteSpinner()
                myModal.toggle()

                // Remove loading indication
                submitButton.removeAttribute('data-kt-indicator');

                // Enable button
                submitButton.disabled = false;

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
                    text: "Kamu berhasil menyimpan data!.",
                    icon: "success",
                    buttonsStyling: false,
                    confirmButtonText: "Ok",
                    customClass: {
                        confirmButton: "btn fw-bold btn-primary",
                    }
                }).then(function () {
                    // delete row data from server and re-draw datatable
                    window.location.reload();
                });
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
                modalTitle.textContent = 'Foto Tenant'

                for (let index = 0; index < button.attributes.length; index++) {
                    if (button.attributes[index].nodeName.includes('data-foto')) {
                        document.getElementById('iframe').src = button.attributes[index].nodeValue
                    }
                }

            })

            const onClickDestroy = (id) => {
                handleDeleteRows(
                    "{{ route('bitanic.seller.destroy', 'ID') }}".replace('ID', id),
                    "{{ csrf_token() }}",
                    "Mitra Bisnis"
                )
            }

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

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
