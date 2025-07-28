<x-app-layout>
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master /</span> Data Provinsi</h4>
    </x-slot>
    {{-- End Header --}}

    <div class="row">
        <div class="col-md-12">
            <!-- Striped Rows -->
            <div class="card">
                <div class="row">
                    <div class="col-md-5">
                        <div class="float-start p-3">
                            <!-- Search -->
                            <form action="{{ route('bitanic.province.index') }}" method="GET" id="form-search">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="input-group">
                                            <span class="input-group-text bg-primary text-white"
                                                style="cursor: pointer;"
                                                onclick="document.getElementById('form-search').submit()">
                                                <i class="bx bx-search"></i>
                                            </span>
                                            <input type="text" class="form-control shadow-none"
                                                placeholder="Cari..." aria-label="Cari..." name="search"
                                                value="{{ request()->query('search') }}" />
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
                                data-bs-target="#modalForm" data-input-prov-name="" data-input-id="add"
                                title="Tambah Provinsi">
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
                                <th>Provinsi</th>
                                <th>Total Kabupaten/Kota</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($data as $province)
                                <tr>
                                    <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                                    <td>{{ $province->prov_name }}</td>
                                    <td>{{ $province->city_count }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-icon btn-warning my-1" data-bs-toggle="modal"
                                            data-bs-target="#modalForm"
                                            data-input-prov-name="{{ $province->prov_name }}"
                                            data-input-id="{{ $province->id }}"
                                            title="Edit Provinsi"><i
                                                class="bx bx-edit-alt"></i>
                                        </button>
                                        <button class="btn btn-sm btn-icon btn-danger my-1"
                                            onclick="destroyProvince({{ $province->id }}, '{{ $province->prov_name }}')"
                                            href="javascript:void(0);" title="Hapus Provinsi"><i class="bx bx-trash"></i>
                                        </button>
                                    </td>
                                </tr>

                            @empty

                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada data</td>
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

    @include('bitanic.wilayah.province._modal-form')

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
                                $('.text-tidak-wajib').removeClass('d-none');
                                // validator.validate()
                            } else {
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

                formData.append("prov_name", document.getElementById('data-input-prov-name').value)

                if (editOrAdd.value != 'add') {
                    url = "{{ route('bitanic.province.update', 'ID') }}".replace('ID', document.getElementById(
                        'data-input-id').value)
                    formData.append('_method', 'PUT')
                } else {
                    url = "{{ route('bitanic.province.store') }}"
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
                }).then(function() {
                    // delete row data from server and re-draw datatable
                    window.location.reload();
                });
            });

            const destroyProvince = (id, name) => {
                handleDeleteRows("{{ route('bitanic.province.destroy', 'ID') }}".replace('ID', id), "{{ csrf_token() }}", name)
            }

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

                const selectSearch = document.querySelectorAll('.select-search')
                selectSearch.forEach(eSelect => {
                    eSelect.addEventListener('change', e => {
                        document.getElementById('form-search').submit()
                    })
                });
            });
        </script>
    @endpush
</x-app-layout>
