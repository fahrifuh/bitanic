<x-app-layout>
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master /</span> Pengguna Bitanic Lite</h4>
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
                            <!-- Search -->
                            <form action="{{ route('bitanic.lite-user.index') }}" method="GET" id="form-search">
                                <!-- Validation Errors -->
                                <x-auth-validation-errors class="mb-4" :errors="$errors" />
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="input-group">
                                            <span class="input-group-text bg-primary text-white" title="Cari" style="cursor: pointer;" onclick="document.getElementById('form-search').submit()">
                                                <i class="bx bx-search"></i>
                                            </span>
                                            <input type="text" class="form-control shadow-none" placeholder="Cari nama..." aria-label="Cari nama..." name="search" value="{{ request()->query('search') }}" />
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <!-- /Search -->
                        </div>
                        <div class="float-end m-3">
                            <div class="d-flex flex-row-reverse gap-2">
                                <a
                                    href="{{ route('bitanic.lite-user.create') }}"
                                      class="btn btn-primary btn-icon" title="Tambah Pengguna"
                                    >
                                    <i class="bx bx-plus"></i>
                                </a>
                                <a href="{{ route('bitanic.lite-user.export-excel') }}"
                                    class="btn btn-success btn-icon"
                                    title="Export excel">
                                    <i class="bx bx-export"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive text-nowrap">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>No HP</th>
                        <th>Jenis Kelamin</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($lite_users as $lite_user)
                            @php
                                $picture = $lite_user->picture ? asset($lite_user->picture) : null;

                                if (!$picture) {
                                    if ($lite_user->gender == 'male') {
                                        $picture = asset('theme/img/avatars/5.png');
                                    } else {
                                        $picture = asset('theme/img/avatars/6.png');
                                    }
                                }
                            @endphp
                            <tr>
                                <td>{{ ($lite_users->currentPage() - 1) * $lite_users->perPage() + $loop->iteration }}</td>
                                <td>
                                    <a href="javascript:;" type="button" class="avatar pull-up" data-bs-toggle="modal"
                                        data-bs-target="#modalFoto" data-foto="{{ $picture }}"
                                        style="display: inline-block;">
                                        <img src="{{ $picture }}" alt="Avatar" class="rounded-circle" />
                                    </a>
                                    {{ $lite_user->name }}
                                </td>
                                <td>{{ $lite_user->phone_number }}</td>
                                <td>{{ gender_format($lite_user->gender) ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('bitanic.lite-user.show', $lite_user->id) }}"
                                        class="btn btn-info btn-icon btn-sm" title="Detail Pengguna">
                                        <i class="bx bx-list-ul"></i>
                                    </a>
                                    <a
                                          href="{{ route('bitanic.lite-user.edit', $lite_user->id) }}"
                                          class="btn btn-warning btn-icon btn-sm" title="Edit Pengguna"
                                        >
                                        <i class="bx bx-edit"></i>
                                    </a>
                                    <button type="button" onclick="deletePest(this)"
                                        data-id="{{ $lite_user->id }}" data-name="{{ $lite_user->name }}"
                                        class="btn btn-danger btn-sm btn-icon" title="Delete data hama"
                                        title="Hapus Pengguna">
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
                                {{ $lite_users->links() }}
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
                        "{{ route('bitanic.lite-user.destroy', 'ID') }}".replace('ID',
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

                window.location = "{{ route('bitanic.lite-user.index') }}"
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
