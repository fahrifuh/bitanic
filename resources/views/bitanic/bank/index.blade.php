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
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master /</span> Bank</h4>
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
                        <div class="float-start m-3">
                        </div>
                        <div class="float-end m-3">
                            <a
                                href="{{ route('bitanic.bank.create') }}"
                                  class="btn btn-primary"
                                  title="Tambah Bank"
                                >
                                <i class="bx bx-plus"></i>&nbsp;Tambah
                            </a>
                        </div>
                    </div>
                </div>
                <div class="table-responsive text-nowrap">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Biaya Transfer Bank</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($banks as $bank)
                            <tr>
                                <td>{{ ($banks->currentPage() - 1) * $banks->perPage() + $loop->iteration }}</td>
                                <td>
                                    <a href="javascript:;" type="button" class="bank-avatar" data-bs-toggle="modal"
                                        data-bs-target="#modalFoto" data-foto="{{ asset($bank->picture) }}"
                                        style="display: inline-block;">
                                        <img src="{{ asset($bank->picture) }}" alt="Avatar" />
                                    </a>
                                    {{ $bank->name }}
                                </td>
                                <td>
                                    @php
                                        $fees = collect($bank->fees)
                                            ->map(function($fee, $key){
                                                switch ($fee['type']) {
                                                    case 0:
                                                        return "Rp " . number_format($fee['fee'], 0, '.', ',');
                                                        break;
                                                    case 1:
                                                        return number_format($fee['fee'], 1, '.', ',') . "%";
                                                        break;
                                                }
                                            })
                                            ->join(" + ");
                                    @endphp
                                    {{ $fees }}
                                </td>
                                <td>
                                    <a href="{{ route('bitanic.bank.show', $bank->id) }}"
                                        class="btn btn-info btn-icon btn-sm" title="Detail Bank">
                                        <i class="bx bx-list-ul"></i>
                                    </a>
                                    <a
                                          href="{{ route('bitanic.bank.edit', $bank->id) }}"
                                          class="btn btn-warning btn-icon btn-sm"
                                          title="Edit Bank"
                                        >
                                        <i class="bx bx-edit"></i>
                                    </a>
                                    <button type="button" onclick="deletePest(this)" data-id="{{ $bank->id }}"
                                        data-name="{{ $bank->name }}" class="btn btn-danger btn-sm btn-icon"
                                        title="Hapus Bank">
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
                                {{ $banks->links() }}
                            </ul>
                          </nav>
                    </div>
                </div>
            </div>
            <!--/ Striped Rows -->
        </div>
    </div>

    @include('bitanic.lite.farmer._modal-picture')

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
                modalTitle.textContent = 'Foto Petani'

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
                        "{{ route('bitanic.bank.destroy', 'ID') }}".replace('ID',
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

                window.location = "{{ route('bitanic.bank.index') }}"
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
