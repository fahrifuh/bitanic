<x-app-layout>
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master /</span> Penarikan Saldo</h4>
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
                            <form action="" method="GET" id="form-search">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="input-group">
                                            <span class="input-group-text bg-primary text-white"
                                                style="cursor: pointer;"
                                                onclick="document.getElementById('form-search').submit()">
                                                <i class="bx bx-search"></i>
                                            </span>
                                            <input type="text" class="form-control shadow-none"
                                                placeholder="Cari nama user, nama admin..." aria-label="Cari nama user, nama admin..."
                                                name="search"
                                                value="{{ request()->query('search') }}" />
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <!-- /Search -->
                        </div>
                        <div class="float-end m-3">
                            <!-- Validation Errors -->
                            <x-auth-validation-errors class="mb-4" :errors="$errors" />
                        </div>
                    </div>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Timestamp</th>
                                <th>User</th>
                                <th>Total Balance</th>
                                <th>Admin</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0" id="body-withdraw">
                            @forelse ($balanceWithdraws as $balanceWithdraw)
                                <tr>
                                    <td>{{ ($balanceWithdraws->currentPage() - 1) * $balanceWithdraws->perPage() + $loop->iteration }}
                                    </td>
                                    <td>{{ $balanceWithdraw->created_at }}</td>
                                    <td>
                                        {{ $balanceWithdraw->user_name }}
                                    </td>
                                    <td>Rp {{ number_format_1($balanceWithdraw->total_balance) }}</td>
                                    <td>{{ $balanceWithdraw->admin_name ?? '-' }}</td>
                                    <td>
                                        @if ($balanceWithdraw->is_succeed === null)
                                            <button type="button" class="btn btn-info btn-sm btn-process"
                                                title="Proses penarikan saldo" data-bs-toggle="modal"
                                                data-bs-target="#modalProcess"
                                                data-id="{{ $balanceWithdraw->id }}"
                                                data-bank-account="{{ $balanceWithdraw->bank_account }}"
                                                data-bank-type="{{ $balanceWithdraw->bank_type }}">
                                                Proses
                                            </button>
                                        @else
                                            @switch($balanceWithdraw->is_succeed)
                                                @case(0)
                                                    <span @class([
                                                        'badge',
                                                        'bg-danger',
                                                    ])>
                                                        Gagal
                                                    </span>
                                                    @break
                                                @case(1)
                                                    <span @class([
                                                        'badge',
                                                        'bg-success',
                                                    ])>
                                                        Berhasil
                                                    </span>
                                                    @break
                                                @default

                                            @endswitch
                                        @endif
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
                                {{ $balanceWithdraws->links() }}
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
            <!--/ Striped Rows -->
        </div>
    </div>

    @include('bitanic.balance-withdraw.admin._modal-process')

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

            function isInt(value) {
                return isNaN(value) ? false : (parseFloat(value) | 0) === parseFloat(value);
            }

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

                document.querySelector('#body-withdraw').addEventListener('click', e => {
                    console.dir(e.target)
                    if (e.target.classList.contains('btn-process')) {
                        console.log(isInt(e.target.dataset?.id))
                        if (!isInt(e.target.dataset?.id)) {
                            alert('Id harus berupa angka!')
                            return 0
                        }
                        const btnData = [e.target.dataset?.bankAccount, e.target.dataset?.bankType]


                        document.querySelector('input[name="bank_account"]').value = btnData[0]
                        document.querySelector('input[name="bank_type"]').value = btnData[1]

                        document.querySelector('#form-update-withdraw-status')
                            .action = "{{ route('bitanic.admin.balance-withdraw.update-status', 'ID') }}".replace('ID', e.target.dataset?.id)
                    }
                })

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
