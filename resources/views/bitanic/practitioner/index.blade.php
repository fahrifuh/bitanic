<x-app-layout>
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master /</span> Data Peneliti</h4>
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
                            <form action="" method="GET" id="form-search">
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
                            </form>
                            <!-- /Search -->
                        </div>
                        <div class="float-end m-3">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#modalForm" data-input-nama="" data-input-bidang-praktisi=""
                                data-input-institusi="" data-input-alamat="" data-input-id="add"
                                title="Tambah peneliti">
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
                                <th>Nama</th>
                                <th>Bidang Peneliti</th>
                                <th>Institusi</th>
                                <th>Alamat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($data as $practitioner)
                                <tr>
                                    <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                                    <td>
                                        <a href="javascript:;" type="button" class="avatar pull-up" data-bs-toggle="modal"
                                            data-bs-target="#modalFoto" data-foto="{{ asset($practitioner->picture) }}"
                                            data-input-id="{{ $practitioner->id }}">
                                            <img src="{{ asset($practitioner->picture) }}" alt="Avatar" class="img-cover rounded" />
                                        </a>
                                        {{ $practitioner->name }}
                                    </td>
                                    <td>{{ $practitioner->practitioner_field }}</td>
                                    <td>{{ $practitioner->institution }}</td>
                                    <td>{{ Str::limit($practitioner->address, 20, '...') }}
                                        <i class='bx bx-info-circle' style="cursor: pointer;" data-bs-toggle="popover"
                                            data-bs-offset="0,14" data-bs-placement="top" data-bs-html="true"
                                            data-bs-content="<p>{{ $practitioner->address }}</p>" title="Alamat"></i>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-icon btn-warning btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#modalForm" data-input-nama="{{ $practitioner->name }}"
                                            data-input-bidang-praktisi="{{ $practitioner->practitioner_field }}"
                                            data-input-institusi="{{ $practitioner->institution }}"
                                            data-input-alamat="{{ $practitioner->address }}"
                                            data-input-id="{{ $practitioner->id }}"
                                            title="Edit peneliti">
                                            <i class="bx bx-edit-alt"></i>
                                        </button>
                                        <button type="button" data-id="{{ $practitioner->id }}"
                                            data-name="{{ $practitioner->name }}"
                                            class="btn btn-icon btn-danger btn-sm btn-delete"
                                            title="Hapus peneliti"
                                            onclick="onClickDestroy('{{ $practitioner->id }}')">
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

    @include('bitanic.practitioner._modal-form')
    @include('bitanic.practitioner._modal-foto')

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
                formData.append("practitioner_field", document.getElementById('data-input-bidang-praktisi').value)
                formData.append("institution", document.getElementById('data-input-institusi').value)
                formData.append("address", document.getElementById('data-input-alamat').value)

                if (editOrAdd.value != 'add') {
                    url = "{{ route('bitanic.researcher.update', 'ID') }}".replace('ID', editOrAdd.value)
                    formData.append("_method", 'PUT')
                } else {
                    url = "{{ route('bitanic.researcher.store') }}"
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
                modalTitle.textContent = 'Foto Peneliti'

                for (let index = 0; index < button.attributes.length; index++) {
                    if (button.attributes[index].nodeName.includes('data-foto')) {
                        document.getElementById('iframe').src = button.attributes[index].nodeValue
                    }
                }

            })

            const onClickDestroy = (id) => {
                handleDeleteRows(
                    "{{ route('bitanic.researcher.destroy', 'ID') }}".replace('ID', id),
                    "{{ csrf_token() }}",
                    "Peneliti"
                )
            }

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");
            });
        </script>
    @endpush
</x-app-layout>
