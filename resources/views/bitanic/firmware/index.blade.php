<x-app-layout>
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master /</span> Update Firmware</h4>
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
                                <div class="row p-1">
                                    <div class="col-md-12 m-1">
                                        <div class="input-group">
                                            <span class="input-group-text bg-primary text-white" style="cursor: pointer;" onclick="document.getElementById('form-search').submit()">
                                                <i class="bx bx-search"></i>
                                            </span>
                                            <input type="text" class="form-control shadow-none"
                                                placeholder="Cari Versi..." aria-label="Cari Versi..." name="search"
                                                value="{{ request()->query('search') }}" />
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <!-- /Search -->
                        </div>
                        <div class="float-end m-3">
                            <a href="{{ route('bitanic.firmware.create') }}" class="btn btn-primary">
                                Tambah
                            </a>
                        </div>
                    </div>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Timestamp</th>
                                <th>ID Perangkat</th>
                                <th>Versi</th>
                                <th>File Dipilih</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($update_firmware as $firmware)
                                <tr>
                                    <td>{{ ($update_firmware->currentPage() - 1) * $update_firmware->perPage() + $loop->iteration }}
                                    </td>
                                    <td>{{ $firmware->created_at }}</td>
                                    <td>{{ $firmware->series }}</td>
                                    <td>{{ $firmware->version }}</td>
                                    <td>
                                        @switch($firmware->is_selected)
                                            @case(0)
                                                <span class="badge bg-danger"><i class="bx bx-x"></i></span>
                                            @break

                                            @case(1)
                                                <span class="badge bg-success"><i class="bx bx-check"></i></span>
                                            @break

                                            @default
                                        @endswitch
                                    </td>
                                    <td>
                                        <a href="{{ route('firmware.douwnload', ['id' => $firmware->series]) }}"
                                            class="btn btn-info btn-icon btn-sm" title="Unduh" target="_blank">
                                            <i class="bx bx-download"></i>
                                        </a>
                                        <button type="button" onclick="updateSelected(this)"
                                            data-id="{{ $firmware->id }}" data-name="{{ $firmware->series }}"
                                            class="btn btn-warning btn-sm btn-icon" title="Pilih versi ini untuk diunduh">
                                            <i class="bx bx-check event-none"></i>
                                        </button>
                                        <button type="button" onclick="deletePest(this)" data-id="{{ $firmware->id }}"
                                            data-name="{{ $firmware->series }}" class="btn btn-danger btn-sm btn-icon"
                                            title="Hapus firmware">
                                            <i class="bx bx-trash event-none"></i>
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
                                    {{ $update_firmware->links() }}
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
                <!--/ Striped Rows -->
            </div>
        </div>

        @push('scripts')
            <script>
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
                        "{{ route('bitanic.firmware.destroy', 'ID') }}".replace('ID',
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

                        return false
                    }

                    Swal.fire({
                        text: "Kamu berhasil menghapus data " + name + "!",
                        icon: "success",
                        showConfirmButton: false,
                        allowOutsideClick: false
                    })

                    window.location = "{{ route('bitanic.firmware.index') }}"
                }

                const updateSelected = async e => {
                    const result = await Swal.fire({
                        text: "File dengan series sama yang dipilih sebelumnya akan diganti dengan file ini. Apakah anda yakin?",
                        icon: "warning",
                        showCancelButton: true,
                        buttonsStyling: false,
                        confirmButtonText: "Ya, ganti!",
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
                        method: 'PUT',
                        headers: {
                            'x-csrf-token': "{{ csrf_token() }}",
                            'Accept': "application/json",
                        },
                    }

                    const [data, error] = await yourRequest(
                        "{{ route('bitanic.firmware.update-selected', 'ID') }}".replace('ID',
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

                        return false
                    }

                    Swal.fire({
                        text: "Kamu berhasil mengganti file data " + e.dataset.name + "!",
                        icon: "success",
                        showConfirmButton: false,
                        allowOutsideClick: false
                    })

                    window.location = "{{ route('bitanic.firmware.index') }}"
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
