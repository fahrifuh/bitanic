<x-app-layout>
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master /</span> Transaksi Komoditi</h4>
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
                                        placeholder="Cari order id, nama user..." aria-label="Cari order id, nama user..." name="search"
                                        value="{{ request()->query('search') }}" />
                                </div>
                            </form>
                            <!-- /Search -->
                        </div>
                        <div class="float-end m-3">
                        </div>
                    </div>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Timestamp</th>
                                <th>Order ID</th>
                                <th>User</th>
                                <th>Total</th>
                                <th>Status Pembayaran</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($farmerTransactions as $farmerTransaction)
                                <tr>
                                    <td>{{ ($farmerTransactions->currentPage() - 1) * $farmerTransactions->perPage() + $loop->iteration }}
                                    </td>
                                    <td>{{ $farmerTransaction->created_at }}</td>
                                    <td>
                                        {{ Str::limit($farmerTransaction->code, 20, '...') }}
                                        <span data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="right"
                                            data-bs-html="true"
                                            title="<i class='bx bxs-hand-up bx-xs' ></i> <span>Klik untuk lihat order ID</span>">
                                            <i class='bx bx-info-circle' style="cursor: pointer;"
                                                data-bs-toggle="popover" data-bs-offset="0,14" data-bs-placement="top"
                                                data-bs-html="true" data-bs-content="<p>{{ $farmerTransaction->code }}</p>"
                                                title="Order ID"></i>
                                        </span>
                                    </td>
                                    <td>{{ $farmerTransaction->user?->name ?? '-' }}</td>
                                    <td>Rp {{ number_format_1($farmerTransaction->total) }}</td>
                                    <td>
                                        <span @class([
                                            'badge',
                                            'bg-success' => $farmerTransaction->status == 'settlement',
                                            'bg-secondary' => $farmerTransaction->status == 'pending',
                                            'bg-danger' => in_array($farmerTransaction->status, [
                                                'expire',
                                                'cancel',
                                                'deny',
                                                'failure',
                                            ]),
                                        ])>
                                            {{ $farmerTransaction->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('bitanic.transaction-komodity.show', $farmerTransaction->id) }}"
                                            class="btn btn-icon btn-sm btn-info" title="Detail Transaksi">
                                            <i class="bx bx-list-ul"></i>
                                        </a>
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
                                {{ $farmerTransactions->links() }}
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
            <!--/ Striped Rows -->
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('theme/js/ui-popover.js') }}"></script>
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
                    "{{ route('bitanic.lite-device.destroy', 'ID') }}".replace('ID',
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

                window.location = "{{ route('bitanic.lite-device.index') }}"
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
