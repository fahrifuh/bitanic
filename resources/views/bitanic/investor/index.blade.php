<x-app-layout>
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master /</span> Data Investor</h4>
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
                                            <span class="input-group-text bg-primary text-white"><small>Tanggal Perjanjian</small></span>
                                            <input type="date" id="date-search" class="form-control input-search"
                                                name="tanggal_perjanjian" title="Search Tanggal Perjanjian"
                                                value="{{ request()->query('tanggal_perjanjian')
                                                    ? now()->parse(request()->query('tanggal_perjanjian'))->format('Y-m-d')
                                                    : null }}" />
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <!-- /Search -->
                        </div>
                        <div class="float-end m-3">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#modalForm" data-input-nama="" data-input-nama-investasi=""
                                data-input-nomor-perjanjian="" data-input-tanggal-perjanjian="" data-input-id="add"
                                title="Tambah Investor">
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
                                <th>Investasi</th>
                                <th>Nomor Perjanjian</th>
                                <th>Tanggal Perjanjian</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($data as $investor)
                                <tr>
                                    <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                                    <td>{{ $investor->name }}</td>
                                    <td>{{ $investor->investment_name }}</td>
                                    <td>{{ $investor->agreement_number }}</td>
                                    <td>{{ carbon_format_id_flex(now()->parse($investor->agreement_date)->format('d-m-Y'),'-',' ') }}
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-icon btn-warning btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#modalForm" data-input-nama="{{ $investor->name }}"
                                            data-input-nama-investasi="{{ $investor->investment_name }}"
                                            data-input-nomor-perjanjian="{{ $investor->agreement_number }}"
                                            data-input-tanggal-perjanjian="{{ $investor->agreement_date }}"
                                            data-input-id="{{ $investor->id }}" title="Edit investor">
                                            <i class="bx bx-edit-alt"></i>
                                        </button>
                                        <button type="button" data-id="{{ $investor->id }}"
                                            data-name="{{ $investor->name }}"
                                            class="btn btn-icon btn-danger btn-sm btn-delete"
                                            title="Hapus investor"
                                            onclick="onClickDestroy('{{ $investor->id }}')">
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

    @include('bitanic.investor._modal-form')

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

                // Show loading indication
                submitButton.setAttribute('data-kt-indicator', 'on');

                // Disable button to avoid multiple click
                submitButton.disabled = true;

                showSpinner()

                // Simulate form submission. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                let url, formSubmited;
                const editOrAdd = document.getElementById('data-input-id');
                const formData = new FormData();

                formData.append("name", document.getElementById('data-input-nama').value)
                formData.append("investment_name", document.getElementById('data-input-nama-investasi').value)
                formData.append("agreement_number", document.getElementById('data-input-nomor-perjanjian').value)
                formData.append("agreement_date", document.getElementById('data-input-tanggal-perjanjian').value)

                if (editOrAdd.value != 'add') {
                    url = "{{ route('bitanic.investor.update', 'ID') }}".replace('ID', document.getElementById(
                        'data-input-id').value)
                    formData.append("_method", 'PUT')
                } else {
                    url = "{{ route('bitanic.investor.store') }}"
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

            const onClickDestroy = (id) => {
                handleDeleteRows(
                    "{{ route('bitanic.investor.destroy', 'ID') }}".replace('ID', id),
                    "{{ csrf_token() }}",
                    "Investor"
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
