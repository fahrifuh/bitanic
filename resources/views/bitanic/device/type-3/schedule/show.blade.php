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
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master / <a href="{{ route('bitanic.device.index') }}">Data Perangkat</a> / <a href="{{ route('bitanic.v3-device.show', $device->id) }}">{{ $device->device_series }}</a> </span>/ Data Penjadwalan </h4>
    </x-slot>
    {{-- End Header --}}

    @if (session()->has('success'))
    <x-alert-message class="alert-success">{{ session()->get('success') }}</x-alert-message>
    @endif

    <div class="row d-flex justify-content-center">
        @if ($device->type == 3)
        <div class="col-12 col-md-6">
            <div class="row">
                <div class="col-12 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title ">Penjadwalan</h4>
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <div class="d-flex flex-column align-items-start gap-2">
                                        <span><strong>Tanggal Dibuat</strong>: {{ $fertilization->created_at }}</span>
                                        <span><strong>Jenis Tanaman</strong>: {{ $fertilization->crop_name }}</span>
                                        <span><strong>Jumlah Pekan</strong>: {{ $fertilization->weeks }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 px-2  border-end ">
                                    @if ($fertilization->valves->watering)
                                        <div class="m-3">
                                            <h5 class="text-info">Penyiraman</h5>
                                            <span><strong>Hari Penyiraman</strong>: {{ collect($fertilization->valves->watering->days)->map(fn($item, $key) => ucfirst($item))->join(', ') }}</span>
                                        </div>
                                        <div class="row pb-1">
                                            @foreach ($fertilization->valves->watering->setontimes as $setontime)
                                                <div class="col-12 my-1">
                                                    <a
                                                        class="btn btn-primary d-block"
                                                        data-bs-toggle="collapse"
                                                        href="#kelolaWaktuPenyiraman{{ $loop->iteration }}"
                                                        role="button"
                                                        aria-expanded="false"
                                                        aria-controls="kelolaWaktuPenyiraman{{ $loop->iteration }}"
                                                    >Kelola Waktu {{ $loop->iteration }} {{ $setontime->time }}</a
                                                    >
                                                    <div class="collapse multi-collapse mt-2" id="kelolaWaktuPenyiraman{{ $loop->iteration }}">
                                                        <div class="flex-column align-items-start text-info p-3 border">
                                                            <div class="d-flex flex-wrap justify-content-start gap-1 mb-3">
                                                                @foreach ($setontime->lands as $land)
                                                                    <span>
                                                                        @if(!$loop->first) -> @endif
                                                                        <span class="badge bg-label-warning">
                                                                            Lahan {{ $land->id }}
                                                                            @if ($land->duration > 0)
                                                                                &nbsp;|&nbsp;{{ $land->duration }}&nbsp;
                                                                                <small>Menit</small>
                                                                            @endif
                                                                            @if (isset($land->seconds) && $land->seconds > 0)
                                                                                &nbsp;|&nbsp;{{ $land->seconds }}&nbsp;
                                                                                <small>Detik</small>
                                                                            @endif
                                                                        </span>
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                            @php
                                                                [$sumMinutes, $sumSeconds] = collect($setontime->lands)->reduce(fn($carry, $item) => [$carry[0] + $item->duration, (isset($item->seconds)) ? $carry[1] + $item->seconds : 0], [0, 0]);
                                                                $totalDuration = now()->parse($setontime->time)->addMinutes($sumMinutes)->addSeconds(($setontime->delay * (count($setontime->lands) - 1)) + $sumSeconds)
                                                            @endphp
                                                            <p>
                                                                @if (count($setontime->lands) > 1)
                                                                    <strong>Delay</strong>: {{ $setontime->delay }} / <small>lahan</small> <br/>
                                                                @endif
                                                                <strong>Waktu Mulai</strong>: {{ $setontime->time }} <br/>
                                                                <strong>Perkiraan Selesai</strong>: {{ $totalDuration->format('H:i:s') }} <br/>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="m-3">
                                            <h5 class="text-secondary">Tidak Ada Penyiraman</h5>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-12 col-md-6 px-2  border-start ">
                                    @if ($fertilization->valves->fertilization)
                                        <div class="m-3">
                                            <h5 class="text-info">Pemupukan</h5>
                                            <span><strong>Hari Pemupukan</strong>: {{ collect($fertilization->valves->fertilization->days)->map(fn($item, $key) => ucfirst($item))->join(', ') }}</span>
                                        </div>
                                        <div class="row pb-1">
                                            @foreach ($fertilization->valves->fertilization->setontimes as $setontime)
                                                <div class="col-12 my-1">
                                                    <a
                                                        class="btn btn-primary d-block"
                                                        data-bs-toggle="collapse"
                                                        href="#kelolaWaktuPemupukan{{ $loop->iteration }}"
                                                        role="button"
                                                        aria-expanded="false"
                                                        aria-controls="kelolaWaktuPemupukan{{ $loop->iteration }}"
                                                    >Kelola Waktu {{ $loop->iteration }} {{ $setontime->time }}</a
                                                    >
                                                    <div class="collapse multi-collapse mt-2" id="kelolaWaktuPemupukan{{ $loop->iteration }}">
                                                        <div class="flex-column align-items-start text-info p-3 border">
                                                            <div class="text-wrap table-change-scroll flipped mb-3">
                                                                <table class="table table-striped content">
                                                                    <thead>
                                                                        <tr>
                                                                            <th class="text-sm">#</th>
                                                                            <th class="text-sm">Lahan ID</th>
                                                                            <th class="text-sm">Durasi Pemupukan</th>
                                                                            <th class="text-sm">Pengairan Awal</th>
                                                                            <th class="text-sm">Pengairan Akhir</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody class="table-border-bottom-0" id="tbody-finished-schedules">
                                                                        @forelse ($setontime->lands as $land)
                                                                        <tr>
                                                                            <td>{{ $loop->iteration }}</td>
                                                                            <td>Lahan {{ $land->id }}</td>
                                                                            <td>
                                                                                @if ($land->duration > 0)
                                                                                    {{ $land->duration }}
                                                                                    <small>Menit</small>
                                                                                @endif
                                                                                &nbsp;
                                                                                @if (isset($land->seconds) && $land->seconds > 0)
                                                                                    {{ $land->seconds }}
                                                                                    <small>Detik</small>
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                @if (isset($land->first_water))
                                                                                    <span>
                                                                                        {{ $land->first_water->minutes }}
                                                                                        <small>Menit</small>
                                                                                        &nbsp;
                                                                                        {{ $land->first_water->seconds }}
                                                                                        <small>Detik</small>
                                                                                    </span>
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                @if (isset($land->last_water))
                                                                                    <span>
                                                                                        {{ $land->last_water->minutes }}
                                                                                        <small>Menit</small>
                                                                                        &nbsp;
                                                                                        {{ $land->last_water->seconds }}
                                                                                        <small>Detik</small>
                                                                                    </span>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                        @empty
                                                                        <tr>
                                                                            <td colspan="5" class="text-center">Tidak ada data</td>
                                                                        </tr>
                                                                        @endforelse
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            @php
                                                                [$sumMinutes, $sumSeconds] = collect($setontime->lands)->reduce(fn($carry, $item) => [
                                                                    $carry[0] + $item->duration + ((isset($item->first_water)) ? $item->first_water->minutes : 0) + ((isset($item->last_water)) ? $item->last_water->minutes : 0),
                                                                    $carry[1] + ((isset($item->seconds)) ? $item->seconds : 0) + ((isset($item->first_water)) ? $item->first_water->seconds : 0) + ((isset($item->last_water)) ? $item->last_water->seconds : 0)
                                                                    ], [0, 0]
                                                                );
                                                                $totalDuration = now()->parse($setontime->time)->addMinutes($sumMinutes)->addSeconds(($setontime->delay * (count($setontime->lands) - 1)) + $sumSeconds + ($setontime->delay * 2))
                                                            @endphp
                                                            <p>
                                                                @if (count($setontime->lands) > 1)
                                                                    <strong>Delay</strong>: {{ $setontime->delay }} / <small>lahan</small> <br/>
                                                                @endif
                                                                <strong>Waktu Mulai</strong>: {{ $setontime->time }} <br/>
                                                                <strong>Perkiraan Selesai</strong>: {{ $totalDuration->format('H:i:s') }} <br/>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="m-3">
                                            <h5 class="text-secondary">Tidak Ada Pemupukan</h5>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <h3 class="card-title  p-3">Jadwal Pemupukan/Penyiraman</h3>
                        <div class="text-wrap table-change-scroll flipped mb-3">
                            <table class="table table-striped content">
                                <thead>
                                    <tr>
                                        <th class="">#</th>
                                        <th class="">Waktu (Server)</th>
                                        <th class="">Waktu (Perangkat)</th>
                                        <th class="">Status Motor</th>
                                        <th class="">Pekan</th>
                                        <th class="">Hari</th>
                                        <th class="">SV Status</th>
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
                                        <td>{{ $fertilization_schedule->week }}</td>
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
        @endif
    </div>

    @push('scripts')
    <script>

    </script>
    @endpush
</x-app-layout>
