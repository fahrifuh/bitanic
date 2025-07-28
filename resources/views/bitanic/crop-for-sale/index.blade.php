<x-app-layout>

    @push('styles')
        <style>
            .bank-avatar {
                width: 100px;
            }

            .bank-avatar img {
                width: 100%;
                height: 100%;
            }

            @media (max-width: 600px) {
                .preview-image {
                    width: calc(100% - 10px);
                }
            }
        </style>
    @endpush
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master /</span> Tanaman untuk dijual</h4>
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
                    <div class="col-4">
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
                            </div>
                        </form>
                        <!-- /Search -->
                    </div>
                    <div class="col-4"></div>
                    <div class="col-4">
                        <div class="float-end m-3">
                            <a
                                href="{{ route('bitanic.crop-for-sale.create') }}"
                                  class="btn btn-primary"
                                >
                                <i class="bx bx-plus"></i>&nbsp;Tambah
                            </a>
                        </div>
                    </div>
                </div>
                <div class="table-responsive text-nowrap">
                  <table class="table table-striped">
                    <thead>
                      <tr class="text-center">
                        <th style="width: 5%;">#</th>
                        <th>Nama</th>
                        <th>Lama Penyimpanan</th>
                        <th style="width: 15%;">Aksi</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($cropForSales as $cropForSale)
                            <tr>
                                <td>{{ ($cropForSales->currentPage() - 1) * $cropForSales->perPage() + $loop->iteration }}</td>
                                <td>
                                    <a href="javascript:;" type="button" class="avatar pull-up" data-bs-toggle="modal"
                                        data-bs-target="#modalFoto" data-foto="{{ asset($cropForSale->picture) }}"
                                        style="display: inline-block;">
                                        <img src="{{ asset($cropForSale->picture) }}" alt="Avatar" class="rounded-circle" />
                                    </a>
                                    {{ $cropForSale->name }}
                                </td>
                                <td class="text-center">{{ $cropForSale->days }}</td>
                                <td>
                                    <div class="d-flex flex-row gap-1 justify-content-center">
                                        <a href="{{ route('bitanic.crop-for-sale.show', $cropForSale->id) }}" class="btn btn-info btn-icon btn-sm" title="Detail tanaman">
                                            <i class="bx bx-list-ul"></i>
                                        </a>
                                        <a
                                              href="{{ route('bitanic.crop-for-sale.edit', $cropForSale->id) }}"
                                              class="btn btn-warning btn-icon btn-sm"
                                              title="Edit data"
                                            >
                                            <i class="bx bx-edit"></i>
                                        </a>
                                        <button type="button" onclick="deletePest(this)" data-id="{{ $cropForSale->id }}" data-name="{{ $cropForSale->name }}" class="btn btn-danger btn-sm btn-icon" title="Hapus data">
                                            <i class="bx bx-trash event-none"></i>
                                        </button>
                                    </div>
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
                                {{ $cropForSales->links() }}
                            </ul>
                          </nav>
                    </div>
                </div>
            </div>
            <!--/ Striped Rows -->
        </div>
    </div>

    @include('bitanic.crop-for-sale._modal-picture')

    @push('scripts')
        <script>
            // btn picture
            const myModalPrev = new bootstrap.Modal(document.getElementById("modalFoto"), {});
            const modalFoto = document.getElementById('modalFoto')
            modalFoto.addEventListener('show.bs.modal', function(event) {
                // Button that triggered the modal
                const button = event.relatedTarget
                // Extract info from data-bs-* attributes
                // const recipient = button.getAttribute('data-bs-whatever')
                const modalTitle = modalFoto.querySelector('.modal-title')
                modalTitle.textContent = 'Foto Tanaman untuk dijual'

                for (let index = 0; index < button.attributes.length; index++) {
                    if (button.attributes[index].nodeName.includes('data-foto')) {
                        document.getElementById('iframe').src = button.attributes[index].nodeValue
                    }
                }

            })

            const deletePest = async e => {
                const result = await Swal.fire({
                    text: "Menghapus data tidak dapat dibatalkan, dan semua data yang berhubungan akan hilang",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Ya, hapus!",
                    cancelButtonText: "Tidak, batalkan",
                    customClass: {
                        confirmButton: "btn fw-bold btn-danger",
                        cancelButton: "btn fw-bold btn-active-light-primary"
                    }
                })

                if (!result.value) {
                    return false
                }

                showSpinner()

                const settings = {
                    method: 'DELETE',
                    headers: {
                        'x-csrf-token': "{{ csrf_token() }}",
                        'Accept': "application/json",
                    }
                }

                const [data, error] = await yourRequest(
                        "{{ route('bitanic.crop-for-sale.destroy', 'ID') }}".replace('ID',
                        e.dataset.id), settings
                    )

                if (error) {

                    deleteSpinner()

                    let errorMessage = ''

                    if ("messages" in error) {
                        let element = ``
                        for (const key in error.messages) {
                            if (Object.hasOwnProperty.call(error.messages, key)) {
                                error.messages[key].forEach(message => {
                                    element += `<li>${message}</li>`;
                                });
                            }
                        }

                        errorMessage = `<ul>${element}</ul>`
                    } else {
                        errorMessage = error.message
                    }

                    Swal.fire({
                            html: errorMessage,
                            icon: "error",
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                }

                Swal.fire({
                    text: "Kamu berhasil menghapus data " + name + "!",
                    icon: "success",
                    showConfirmButton: false,
                    allowOutsideClick: false
                })

                window.location = "{{ route('bitanic.crop-for-sale.index') }}"
            }

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

                // const btnDelete = document.querySelectorAll('.btn-delete')

                // btnDelete.forEach(element => {
                //     element.addEventListener('click', e => {
                //         handleDeleteRows("{{ route('bitanic.pest.destroy', 'ID') }}".replace('ID', e.target.dataset.id), "{{ csrf_token() }}", e.target.dataset.name)
                //     })
                // });
            });
        </script>
    @endpush
</x-app-layout>
