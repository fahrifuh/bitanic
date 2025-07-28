<x-app-layout>
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master / <a href="{{ route('bitanic.shop.index') }}">Toko</a> /</span> Penarikan Saldo</h4>
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
                            Saldo Rp&nbsp;{{ number_format($shop->balance) }}
                            <br>
                            <!-- Validation Errors -->
                            <x-auth-validation-errors class="mb-4" :errors="$errors" />
                        </div>
                        <div class="float-end m-3">
                            <form action="{{ route('bitanic.shop.balance-withdraw.store') }}" method="POST">
                                @csrf

                                <button type="submit" class="btn btn-primary">Tarik Saldo</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Timestamp</th>
                                <th>User</th>
                                <th>Total Balance</th>
                                <th>Admin</th>
                                <th>Status</th>
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
                                            <span @class([
                                                'badge',
                                                'bg-label-secondary',
                                            ])>
                                                Sedang Diproses
                                            </span>
                                        @else
                                            @switch($balanceWithdraw->is_succeed)
                                                @case(0)
                                                    <span @class([
                                                        'badge',
                                                        'bg-label-danger',
                                                    ])>
                                                        Gagal
                                                    </span>
                                                    @break
                                                @case(1)
                                                    <span @class([
                                                        'badge',
                                                        'bg-label-success',
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

    @push('scripts')
        <script src="{{ asset('theme/js/ui-popover.js') }}"></script>
        <script>
        </script>
    @endpush
</x-app-layout>
