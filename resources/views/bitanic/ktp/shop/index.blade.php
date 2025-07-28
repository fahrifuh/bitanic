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
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master / KTP /</span> Toko</h4>
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
                        </div>
                    </div>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th>Nama Toko</th>
                                <th>Pemilik</th>
                                <th>Status</th>
                                <th>KTP</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($shops as $shop)
                                <tr>
                                    <td>{{ ($shops->currentPage() - 1) * $shops->perPage() + $loop->iteration }}</td>
                                    <td>{{ $shop->name }}</td>
                                    <td>{{ $shop->farmer->full_name }}</td>
                                    <td>
                                        <span class="badge bg-warning">Belum Diverifikasi</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('bitanic.ktp-shop.ktp', $shop->id) }}" target="__blank">Lihat
                                            KTP</a>
                                    </td>
                                    <td>
                                        <div class="d-flex align-item-center gap-1">
                                            <button type="button" onclick="acceptKtp(this)"
                                                data-id="{{ $shop->id }}" data-name="{{ $shop->name }}"
                                                class="btn btn-success btn-sm btn-icon" title="Terima KTP">
                                                <i class="bx bx-check event-none"></i>
                                            </button>
                                            <button type="button" onclick="deletePest(this)"
                                                data-id="{{ $shop->id }}" data-name="{{ $shop->name }}"
                                                class="btn btn-danger btn-sm btn-icon" title="Tolak KTP">
                                                <i class="bx bx-x event-none"></i>
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
                                {{ $shops->links() }}
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
            const acceptKtp = async e => {
                const result = await Swal.fire({
                    text: "Menerima KTP. Apa anda yakin?",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Ya, terima!",
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

                const formData = new FormData();

                formData.append("status", 1)

                const settings = {
                    method: 'post',
                    headers: {
                        'x-csrf-token': "{{ csrf_token() }}",
                        'Accept': "application/json",
                    },
                    body: formData
                }

                const [data, error] = await yourRequest(
                    "{{ route('bitanic.ktp-shop.update', 'ID') }}".replace('ID',
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
                    } else if ("errors" in error) {
                        let element = ``
                        for (const key in error.errors) {
                            if (Object.hasOwnProperty.call(error.errors, key)) {
                                error.errors[key].forEach(message => {
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
                    text: data?.message ?? 'Berhasil disimpan!',
                    icon: "success",
                    showConfirmButton: false,
                    allowOutsideClick: false
                })

                window.location = "{{ route('bitanic.ktp-shop.index') }}"
            }
            const deletePest = async e => {
                const result = await Swal.fire({
                    text: "Menolak KTP. Apa anda yakin?",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Ya, tolak!",
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

                const formData = new FormData();

                formData.append("status", 0)

                const settings = {
                    method: 'post',
                    headers: {
                        'x-csrf-token': "{{ csrf_token() }}",
                        'Accept': "application/json",
                    },
                    body: formData
                }

                const [data, error] = await yourRequest(
                    "{{ route('bitanic.ktp-shop.update', 'ID') }}".replace('ID',
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
                    text: data?.message ?? 'Berhasil disimpan',
                    icon: "success",
                    showConfirmButton: false,
                    allowOutsideClick: false
                })

                window.location = "{{ route('bitanic.ktp-shop.index') }}"
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
