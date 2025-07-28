<x-app-layout>
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master /</span> Data Mitra Strategi</h4>
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
                        <div class="float-start p-3">
                            <!-- Search -->
                            <form action="{{ route('bitanic.partner.index') }}" method="GET" id="form-search">
                                <div class="row g-2">
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
                                    <div class="col-12 col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-text bg-primary text-white"><small>Tanggal
                                                    Bergabung</small></span>
                                            <input type="date" class="form-control input-search"
                                                name="tanggal_bergabung"
                                                value="{{ request()->query('tanggal_bergabung')
                                                    ? now()->parse(request()->query('tanggal_bergabung'))->format('Y-m-d')
                                                    : null }}" />
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-text bg-primary text-white"><small>Tanggal
                                                    Kontrak</small></span>
                                            <input type="date" class="form-control input-search"
                                                name="tanggal_kontrak"
                                                value="{{ request()->query('tanggal_kontrak')
                                                    ? now()->parse(request()->query('tanggal_kontrak'))->format('Y-m-d')
                                                    : null }}" />
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <!-- /Search -->
                        </div>
                        <div class="float-end m-3">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#modalForm" data-input-nama="" data-input-kontak=""
                                data-input-tipe-kemitraan="" data-input-tanggal-bergabung=""
                                data-input-tanggal-kontrak="" data-input-id="add" title="Tambah mitra">
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
                                <th>Kontak</th>
                                <th>Jenis Kemitraan</th>
                                <th>Tanggal Bergabung</th>
                                <th>Tanggal Kontrak</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($data as $investor)
                                <tr>
                                    <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                                    {{-- <td>{{ $investor->name }}</td> --}}
                                    <td>
                                        <a href="javascript:;" type="button" class="avatar pull-up"
                                            data-bs-toggle="modal" data-bs-target="#modalFoto"
                                            data-foto="{{ asset($investor->picture) }}"
                                            data-input-id="{{ $investor->id }}">
                                            <img src="{{ $investor->picture ? asset($investor->picture) : asset('bitanic-photo/dummy-image.png') }}"
                                                alt="Avatar" class="img-cover" />
                                        </a>
                                        {{ $investor->name }}
                                    </td>
                                    <td>{{ $investor->contact }}</td>
                                    <td>{{ $investor->partner_type }}</td>
                                    <td>{{ carbon_format_id_flex(now()->parse($investor->date_joining)->format('d-m-Y'), '-', '/') }}
                                    </td>
                                    <td>{{ carbon_format_id_flex(now()->parse($investor->contract_date)->format('d-m-Y'), '-', '/') }}
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-icon btn-warning btn-sm"
                                            data-bs-toggle="modal" data-bs-target="#modalForm"
                                            data-input-nama="{{ $investor->name }}"
                                            data-input-kontak="{{ $investor->contact }}"
                                            data-input-tipe-kemitraan="{{ $investor->partner_type }}"
                                            data-input-tanggal-bergabung="{{ $investor->date_joining }}"
                                            data-input-tanggal-kontrak="{{ $investor->contract_date }}"
                                            data-input-id="{{ $investor->id }}" title="Edit mitra">
                                            <i class="bx bx-edit-alt"></i>
                                        </button>
                                        <button type="button" data-id="{{ $investor->id }}"
                                            data-name="{{ $investor->name }}"
                                            class="btn btn-icon btn-danger btn-sm btn-delete"
                                            onclick="onClickDestroy('{{ $investor->id }}')" title="Hapus mitra">
                                            <i class="bx bx-trash event-none"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center" colspan="7">Tidak ada data</td>
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

    @include('bitanic.partner._modal-form')
    @include('bitanic.partner._modal-foto')

    @push('scripts')
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
                formData.append("contact", document.getElementById('data-input-kontak').value)
                formData.append("partner_type", document.getElementById('data-input-tipe-kemitraan').value)
                formData.append("date_joining", document.getElementById('data-input-tanggal-bergabung').value)
                formData.append("contract_date", document.getElementById('data-input-tanggal-kontrak').value)

                if (editOrAdd.value != 'add') {
                    url = "{{ route('bitanic.partner.update', 'ID') }}".replace('ID', editOrAdd.value)
                    formData.append("_method", 'PUT')
                } else {
                    url = "{{ route('bitanic.partner.store') }}"
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
                modalTitle.textContent = 'Foto Tenant'

                for (let index = 0; index < button.attributes.length; index++) {
                    if (button.attributes[index].nodeName.includes('data-foto')) {
                        document.getElementById('iframe').src = button.attributes[index].nodeValue
                    }
                }

            })

            const onClickDestroy = (id) => {
                handleDeleteRows(
                    "{{ route('bitanic.partner.destroy', 'ID') }}".replace('ID', id),
                    "{{ csrf_token() }}",
                    "Mitra Strategi"
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
