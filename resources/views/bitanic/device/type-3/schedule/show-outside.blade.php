<x-app-layout>

    @push('styles')
    <style>
        .table-change-scroll {
            width: 100%;
            overflow-y: auto;
        }

        .flipped,
        .flipped .content {
            transform: rotateX(180deg);
            -ms-transform: rotateX(180deg);
            /* IE 9 */
            -webkit-transform: rotateX(180deg);
            /* Safari and Chrome */
        }
    </style>
    @endpush
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master / <a href="{{ route('bitanic.device.index') }}">Data Perangkat</a> / <a href="{{ route('bitanic.v3-device.show', $device->id) }}">{{ $device->device_series }}</a> </span>/ Data Telemetri SV </h4>
    </x-slot>
    {{-- End Header --}}

    @if (session()->has('success'))
    <x-alert-message class="alert-success">{{ session()->get('success') }}</x-alert-message>
    @endif

    <div class="row d-flex justify-content-center">
        <div class="col-12">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <h3 class="card-title p-3">Telemetri Perubahan Status SV Perangkat</h3>
                        <div class="text-wrap table-change-scroll flipped mb-3">
                            <table class="table table-striped content">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Waktu (Server)</th>
                                        <th>Waktu (Perangkat)</th>
                                        <th>Status Motor</th>
                                        <th>Hari</th>
                                        <th>SV Status</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @forelse ($fertilization_schedules as $fertilization_schedule)
                                    <tr>
                                        <td>{{ ($loop->iteration + (($fertilization_schedules->currentPage() - 1) * 10)) }}</td>
                                        <td>{{ $fertilization_schedule->created_at }}</td>
                                        <td>{{ $fertilization_schedule->start_time }}</td>
                                        <td>
                                            <span @class([
                                                    "badge",
                                                    "bg-label-success" => $fertilization_schedule->motor_status == 1,
                                                    "bg-label-danger" => $fertilization_schedule->motor_status == 0,
                                                ])>{{ $fertilization_schedule->motor_status ? 'HIDUP' : 'MATI' }}</span>
                                        </td>
                                        <td>{{ getHari($fertilization_schedule->day) }}</td>
                                        <td>
                                            <span @class([
                                                    "badge",
                                                    "bg-label-success" => $fertilization_schedule->extras->pemupukan == 1,
                                                    "bg-label-danger" => $fertilization_schedule->extras->pemupukan == 0,
                                                ])>Pemupukan {{ $fertilization_schedule->extras->pemupukan ? 'ON' : 'OFF' }}</span>
                                            <span @class([
                                                    "badge",
                                                    "bg-label-success" => $fertilization_schedule->extras->air == 1,
                                                    "bg-label-danger" => $fertilization_schedule->extras->air == 0,
                                                ])>Penyiraman {{ $fertilization_schedule->extras->air ? 'ON' : 'OFF' }}</span>
                                            @foreach ($fertilization_schedule->extras->lahan as $lahan)
                                                <span @class([
                                                    "badge",
                                                    "bg-label-success" => $lahan->status == 1,
                                                    "bg-label-danger" => $lahan->status == 0,
                                                ])>SV{{ $loop->iteration }} {{ $lahan->status ? 'ON' : 'OFF' }}</span>
                                            @endforeach
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Tidak ada data</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-start ps-3">
                                {{ $fertilization_schedules->links() }}
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>

    </script>
    @endpush
</x-app-layout>
