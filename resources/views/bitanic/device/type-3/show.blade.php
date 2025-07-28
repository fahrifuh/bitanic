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

        .device-status {
            background-color: #c3c3c3;
            width: 20px;
            height: 20px;
            border-radius: 20px;
        }

        .bg-on-status {
            background-color: #00ff08 !important;
        }
        .event-none {
            pointer-events: none;
        }
    </style>
    @endpush
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Menu /</span> <a href="{{ route('bitanic.device.index') }}">Perangkat</a> / {{ $device->device_series }}</h4>
    </x-slot>
    {{-- End Header --}}

    @if (session()->has('success'))
    <x-alert-message class="alert-success">{{ session()->get('success') }}</x-alert-message>
    @endif
    @if (session()->has('failed'))
    <x-alert-message class="alert-danger">{{ session()->get('failed') }}</x-alert-message>
    @endif

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-column align-items-center gap-4">
                        @if ($device->picture)
                        <img src="{{ asset($device->picture) }}" alt="perangkat-foto" class="d-block" style="width: 100%;" id="uploadedAvatar" />
                        @endif
                        <h3>{{ $device->device_series }}</h3>
                    </div>
                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ route('bitanic.v3-device.edit', $device->id) }}" class="btn btn-warning btn-sm">
                            Edit Perangkat
                        </a>
                        @if ($device->type == 3)
                            <a href="" class="btn btn-warning btn-sm d-none">
                                Edit Selenoid Lahan
                            </a>
                        @endif
                        <button type="button" id="btn-farmer-delete" class="btn btn-outline-danger btn-sm d-none">
                            Hapus
                        </button>
                    </div>
                    <hr />
                    <div class="d-flex flex-column align-items-start gap-2 mb-3">
                        <span><strong>Versi</strong>: {{ $device->version }}</span>
                        <span><strong>Tipe</strong>: {{ $device->type }}</span>
                        <span><strong>Delay</strong>: {{ $device->delay }} detik</span>
                        <span><strong>Debit (Liter)</strong>: {{ $device?->toren_pemupukan->debit ?? '-' }} / menit</span>
                        <span><strong>Tanggal Produksi</strong>: {{ now()->parse($device->production_date)->formatLocalized('%d %B %Y') }}</span>
                        <span><strong>Tanggal Pembelian</strong>: {{ now()->parse($device->purchase_date)->formatLocalized('%d %B %Y') }}</span>
                        <span><strong>Tanggal Diaktifkan</strong>: {{ now()->parse($device->activate_date)->formatLocalized('%d %B %Y') }}</span>
                    </div>
                    @if ($device->type == 3)
                    <div class="mb-3">
                        <a href="{{ route('bitanic.v3-device.telemetri-selenoids', $device->id) }}" class="btn btn-sm btn-primary d-block">Detail Telemetri Status SV</a>
                    </div>
                    <div class="mt-3 d-flex flex-column" id="devices-box">
                        @foreach ($device->selenoids as $selenoid)
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex justify-content-between">
                                    <span id="status-sv-{{ $selenoid->selenoid_id }}" @class([ 'me-1' , 'device-status' , 'bg-on-status'=> $selenoid->selenoid_status == 1,
                                        ])
                                        ></span>
                                    Lahan {{ $selenoid->land->name }} (SV{{ $selenoid->selenoid_id }})
                                </div>
                                <div>
                                    <a href="{{ route('bitanic.v3-device.selenoid.edit', ['device' => $device->id, 'selenoid' => $selenoid->id]) }}" class="btn btn-sm btn-icon btn-warning" title="Edit Selenoid">
                                        <i class="bx bx-edit-alt"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-icon btn-danger btn-selenoid-delete" data-id="{{ $selenoid->id }}"><i class="bx bx-trash event-none"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center ms-3 mt-1">
                                <div class="d-flex justify-content-between">
                                    <span id="status-penyiraman-{{ $selenoid->selenoid_id }}" @class([ 'me-1' , 'device-status' , 'bg-on-status'=> $selenoid->selenoid_status == 1 && $device->status_penyiraman == 1,
                                        ])
                                        ></span>
                                    Penyiraman
                                </div>
                                <div>
                                    <button type="button" data-motor="1" data-type="penyiraman" data-pe="{{ $selenoid->selenoid_id }}"
                                        data-id="{{ $device->id }}" data-status="on" class="btn btn-sm btn-pe-status btn-secondary">
                                        ON
                                    </button>
                                    <button type="button" data-motor="1" data-type="penyiraman" data-pe="{{ $selenoid->selenoid_id }}"
                                        data-id="{{ $device->id }}" data-status="off" class="btn btn-sm btn-pe-status btn-secondary">
                                        OFF
                                    </button>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center ms-3 mt-1 mb-3">
                                <div class="d-flex justify-content-between">
                                    <span id="status-pemupukan-{{ $selenoid->selenoid_id }}" @class([ 'me-1' , 'device-status' , 'bg-on-status'=> $selenoid->selenoid_status == 1 && $device->status_pemupukan == 1,
                                        ])
                                        ></span>
                                    Pemupukan
                                </div>
                                <div>
                                    <button type="button" data-motor="1" data-type="pemupukan" data-pe="{{ $selenoid->selenoid_id }}"
                                        data-id="{{ $device->id }}" data-status="on" class="btn btn-sm btn-pe-status btn-secondary">
                                        ON
                                    </button>
                                    <button type="button" data-motor="1" data-type="pemupukan" data-pe="{{ $selenoid->selenoid_id }}"
                                        data-id="{{ $device->id }}" data-status="off" class="btn btn-sm btn-pe-status btn-secondary">
                                        OFF
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                        @if (count($device->selenoids) <= 4)
                            <div class="mb-3">
                                <a href="{{ route('bitanic.v3-device.selenoid.create', $device->id) }}" class="btn btn-sm btn-primary d-block">Tambah Selenoid Lahan</a>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        @if ($device->type == 3)
        <div class="col-md-8">
            <div class="row">
                <div class="col-12 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="float-end">
                                <a href="{{ route('bitanic.v3-device.formula', $device->id) }}" class="btn btn-primary"><span class='tf-icons bx bx-list-ol'></span> List Formula Pemupukan</a>
                            </div>
                        </div>
                    </div>
                </div>
                @if ($device->fertilization)
                <div class="col-12 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Penjadwalan Berjalan</h4>
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <div class="d-flex flex-column align-items-start gap-2">
                                        <span><strong>Tanggal Dibuat</strong>: {{ $device->fertilization->created_at }}</span>
                                        <span><strong>Jenis Tanaman</strong>: {{ $device->fertilization->crop_name }}</span>
                                        <span><strong>Jumlah Pekan</strong>: {{ $device->fertilization->weeks }}</span>
                                        <span><strong>Tanggal Selesai</strong>: {{ $device->fertilization->end_datetime }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 px-2 border-end border-dark">
                                    @if ($device->fertilization->valves->watering)
                                        <div class="m-3">
                                            <h5 class="text-info">Penyiraman</h5>
                                            <span><strong>Hari Penyiraman</strong>: {{ collect($device->fertilization->valves->watering->days)->map(fn($item, $key) => ucfirst($item))->join(', ') }}</span>
                                        </div>
                                        <div class="row pb-1">
                                            @foreach ($device->fertilization->valves->watering->setontimes as $setontime)
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
                                                                    <strong>Delay</strong>: {{ $setontime->delay }} detik / <small>lahan</small> <br/>
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
                                <div class="col-12 col-md-6 px-2 border-start border-dark">
                                    @if ($device->fertilization->valves->fertilization)
                                        <div class="m-3">
                                            <h5 class="text-info">Pemupukan</h5>
                                            <span><strong>Hari Pemupukan</strong>: {{ collect($device->fertilization->valves->fertilization->days)->map(fn($item, $key) => ucfirst($item))->join(', ') }}</span>
                                        </div>
                                        <div class="row pb-1">
                                            @foreach ($device->fertilization->valves->fertilization->setontimes as $setontime)
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
                                                                            <th class="text-sm">Durasi</th>
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
                                                                    <strong>Delay</strong>: {{ $setontime->delay }} detik / <small>lahan</small> <br/>
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
                            <hr />

                            <div class="d-flex flex-row align-items-start gap-2" id="btn-action">
                                <a href="{{ route('bitanic.v3-device.fertilization-show', ['device' => $device->id, 'fertilization' => $device->fertilization->id]) }}" class="btn btn-sm btn-info">
                                    <span class='tf-icons bx bx-list-ol'></span> Detail
                                </a>
                                |
                                <button type="button" class="btn btn-sm btn-warning" id="btn-kirim-setting" data-id="{{ $device->fertilization->id }}">Kirim Ulang Setting untuk Perangkat</button>
                                <button type="button" class="btn btn-sm btn-danger" id="btn-reset-perangkat" data-id="{{ $device->fertilization->id }}">Hapus Penjadwalan & Reset Perangkat</button>
                                <button type="button" class="btn btn-sm btn-secondary" id="btn-pemupukan-berhenti" data-id="{{ $device->fertilization->id }}">Penjadwalan dihentikan & Reset Perangkat</button>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="col-12 mb-3 d-none">
                    <div class="card">
                        <div class="card-body">
                            <div class="float-end">
                                <a href="#" class="btn btn-primary"><span class='tf-icons bx bx-plus-circle'></span> Buat Pemupukan</a>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                <div class="col-12">
                    <div class="card">
                        <h4 class="card-title m-3">Pemupukan Selesai</h4>
                        <div class="text-wrap table-change-scroll flipped mb-3">
                            <table class="table table-striped content">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Timestamp</th>
                                        <th>Jenis Tanaman</th>
                                        <th>Jumlah Pekan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0" id="tbody-finished-schedules">
                                    @forelse ($finished_fertilizations as $finished_fertilization)
                                    <tr>
                                        <td>{{ ($loop->iteration + (($finished_fertilizations->currentPage() - 1) * 10)) }}</td>
                                        <td>{{ $finished_fertilization->created_at }}</td>
                                        <td>{{ $finished_fertilization->crop_name }}</td>
                                        <td>{{ $finished_fertilization->weeks }}</td>
                                        <td>
                                            <a href="{{ route('bitanic.v3-device.fertilization-show', ['device' => $device->id, 'fertilization' => $finished_fertilization->id]) }}" class="btn btn-sm btn-icon btn-info">
                                                <span class='tf-icons bx bx-list-ol'></span>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-icon btn-danger btn-schedule-delete" data-id="{{ $finished_fertilization->id }}">
                                                <span class='tf-icons bx bx-trash event-none'></span>
                                            </button>
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

                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-start ps-3">
                                {{ $finished_fertilizations->links() }}
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    @push('scripts')
    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        const changeStatus = (motorStatus, statusElement) => {
            if (!statusElement) {
                return false
            }
            switch (motorStatus) {
                case 0:
                    statusElement.classList.remove("bg-on-status")
                    break;
                case 1:
                    statusElement.classList.add("bg-on-status")
                    break;

                default:
                    break;
            }

            return true
        }

        const sendSettingMessage = async (eButton) => {
            try {
                // Show loading indication
                eButton.setAttribute('data-kt-indicator', 'on');

                // Disable button to avoid multiple click
                eButton.disabled = true;

                // Simulate form submission. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                let url;
                const formData = new FormData();

                url = "{{ route('bitanic.v3-schedule.resend-message', 'ID') }}"

                const formSubmited = await axios.post(url.replace('ID', eButton.dataset.id), formData, {
                    headers: {
                        'Accept': 'application/json'
                    }
                })

                const response = formSubmited.data.message

                // Remove loading indication
                eButton.removeAttribute('data-kt-indicator');

                // Enable button
                eButton.disabled = false;

                Swal.fire({
                    text: response,
                    icon: "success",
                });
            } catch (error) {
                console.error(error);

                // Remove loading indication
                eButton.removeAttribute('data-kt-indicator');

                // Enable button
                eButton.disabled = false;
            }
        }

        const deleteResetDevice = async (eButton) => {
            try {
                // Show loading indication
                eButton.setAttribute('data-kt-indicator', 'on');

                // Disable button to avoid multiple click
                eButton.disabled = true;

                const result = await Swal.fire({
                    text: "Data penjadwalan akan dihapus dan tidak dapat dikembalikan serta mereset setting perangkatnya. Data penjadwalan juga tidak akan masuk kedalam riwayat penjadwalan. Apakah anda yakin?",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Yes, delete!",
                    cancelButtonText: "No, cancel",
                    customClass: {
                        confirmButton: "btn fw-bold btn-danger",
                        cancelButton: "btn fw-bold btn-active-light-primary"
                    }
                })

                if (result.value) {
                    // Simulate form submission. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                    let url;
                    const formData = new FormData();

                    url = "{{ route('bitanic.v3-schedule.reset', 'ID') }}"

                    const formSubmited = await axios.post(url.replace('ID', eButton.dataset.id), formData, {
                        headers: {
                            'X-ferads-token': 'cPzC7advUBmnAJe1hx8P'
                        }
                    })

                    const response = formSubmited.data.message

                    // Remove loading indication
                    eButton.removeAttribute('data-kt-indicator');

                    // Enable button
                    eButton.disabled = false;

                    Swal.fire({
                        text: response,
                        icon: "success",
                    });

                    window.location.reload();
                } else {
                    // Remove loading indication
                    eButton.removeAttribute('data-kt-indicator');

                    // Enable button
                    eButton.disabled = false;

                    Swal.fire({
                        text: 'Penghapusan tidak dilaksanakan.',
                        icon: "failed",
                    });
                }

            } catch (error) {
                console.error(error);

                // Remove loading indication
                eButton.removeAttribute('data-kt-indicator');

                // Enable button
                eButton.disabled = false;
            }
        }

        const deleteFinishedSchedule = async (eButton) => {
            try {
                // Show loading indication
                eButton.setAttribute('data-kt-indicator', 'on');

                // Disable button to avoid multiple click
                eButton.disabled = true;

                const result = await Swal.fire({
                    text: "Data penjadwalan akan dihapus dan tidak dapat dikembalikan serta mereset setting perangkatnya. Apakah anda yakin?",
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

                if (result.value) {
                    // Simulate form submission. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                    let url;
                    const formData = new FormData();

                    url = "{{ route('bitanic.fertilization.destroy-finishid', 'ID') }}"

                    const formSubmited = await axios.delete(url.replace('ID', eButton.dataset.id), {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })

                    const response = formSubmited.data.message

                    // Remove loading indication
                    eButton.removeAttribute('data-kt-indicator');

                    // Enable button
                    eButton.disabled = false;

                    Swal.fire({
                        text: response,
                        icon: "success",
                    });

                    window.location.reload();
                } else {
                    // Remove loading indication
                    eButton.removeAttribute('data-kt-indicator');

                    // Enable button
                    eButton.disabled = false;

                    Swal.fire({
                        text: 'Penghapusan tidak dilaksanakan.',
                        icon: "failed",
                    });
                }

            } catch (error) {
                console.error(error);

                // Remove loading indication
                eButton.removeAttribute('data-kt-indicator');

                // Enable button
                eButton.disabled = false;
            }
        }

        const deleteSelenoid = async (eButton) => {
            try {
                // Show loading indication
                eButton.setAttribute('data-kt-indicator', 'on');

                // Disable button to avoid multiple click
                eButton.disabled = true;

                const result = await Swal.fire({
                    text: "Data selenoid akan dihapus dan tidak dapat dikembalikan. Apakah anda yakin?",
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

                if (result.value) {
                    // Simulate form submission. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                    let url;
                    const formData = new FormData();

                    url = "{{ route('bitanic.v3-device.selenoid.destroy', ['device' => $device->id, 'selenoid' => 'ID']) }}"

                    const formSubmited = await axios.delete(url.replace('ID', eButton.dataset.id), {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })

                    const response = formSubmited.data.message

                    // Remove loading indication
                    eButton.removeAttribute('data-kt-indicator');

                    // Enable button
                    eButton.disabled = false;

                    Swal.fire({
                        text: response,
                        icon: "success",
                    });

                    window.location.reload();
                } else {
                    // Remove loading indication
                    eButton.removeAttribute('data-kt-indicator');

                    // Enable button
                    eButton.disabled = false;

                    Swal.fire({
                        text: 'Penghapusan tidak dilaksanakan.',
                        icon: "failed",
                    });
                }

            } catch (error) {
                console.error(error);

                // Remove loading indication
                eButton.removeAttribute('data-kt-indicator');

                // Enable button
                eButton.disabled = false;
            }
        }

        const stopFertilization = async (eButton) => {
            try {
                // Show loading indication
                eButton.setAttribute('data-kt-indicator', 'on');

                // Disable button to avoid multiple click
                eButton.disabled = true;

                const result = await Swal.fire({
                    text: "Pemupukan akan dihentikan dan akan dimasukan kedalam riwayat pemupukan serta mereset setting perangkatnya. Apakah anda yakin?",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Ya, hentikan!",
                    cancelButtonText: "Tidak, batalkan",
                    customClass: {
                        confirmButton: "btn fw-bold btn-danger",
                        cancelButton: "btn fw-bold btn-active-light-primary"
                    }
                })

                if (result.value) {
                    // Simulate form submission. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                    let url;
                    const formData = new FormData();

                    url = "{{ route('bitanic.v3-schedule.stop', 'ID') }}"

                    const formSubmited = await axios.post(url.replace('ID', eButton.dataset.id), formData, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })

                    const response = formSubmited.data.message

                    // Remove loading indication
                    eButton.removeAttribute('data-kt-indicator');

                    // Enable button
                    eButton.disabled = false;

                    Swal.fire({
                        text: response,
                        icon: "success",
                    });

                    window.location.reload();
                } else {
                    // Remove loading indication
                    eButton.removeAttribute('data-kt-indicator');

                    // Enable button
                    eButton.disabled = false;

                    Swal.fire({
                        text: 'Penghapusan tidak dilaksanakan.',
                        icon: "failed",
                    });
                }

            } catch (error) {
                console.error(error);

                Swal.fire({
                    text: error.message,
                    icon: "failed",
                });

                // Remove loading indication
                eButton.removeAttribute('data-kt-indicator');

                // Enable button
                eButton.disabled = false;
            }
        }

        async function reqChangePeStatus(eButton, id, pe, status, type) {
            try {
                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-danger'
                    },
                    buttonsStyling: false
                })

                // const swalText = (status == 1)
                //     ? "Mengubah status data menjadi tidak menerima membuat sistem tidak akan menerima data dari alat. (Anda masih bisa mengubah settingan ini)"
                //     : "Mengubah status data menjadi menerima membuat sistem dapat menerima data dari alat."

                const result = await Swal.fire({
                    title: 'Apakah anda yakin?',
                    text: "Anda akan mengirim command kepada alat anda.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Iya, ganti status!',
                    cancelButtonText: 'Batalkan'
                })

                if (result.isConfirmed) {
                    // Show loading indication
                    eButton.setAttribute('data-kt-indicator', 'on');

                    // Disable button to avoid multiple click
                    eButton.disabled = true;

                    const formData = new FormData();
                    formData.append("type", type)
                    formData.append("selenoid", pe)
                    formData.append("status", status)

                    let url = "{{ route('bitanic.v3-device.status-change', 'ID') }}".replace('ID', id)

                    const settings = {
                        method: 'POST',
                        headers: {
                            'x-csrf-token': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    }

                    const [data, error] = await yourRequest(url, settings)

                    if (error) {
                        if ("messages" in error) {
                            let errorMessage = ''

                            let element = ``
                            for (const key in error.messages) {
                                if (Object.hasOwnProperty.call(error.messages, key)) {
                                    error.messages[key].forEach(message => {
                                        element += `<li>${message}</li>`;
                                    });
                                }
                            }

                            errorMessage = `<ul>${element}</ul>`

                            Swal.fire({
                                html: errorMessage,
                                icon: "error",
                                buttonsStyling: false,
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            });
                        } else if ("message" in error) {
                            Swal.fire({
                                html: error.message,
                                icon: "error",
                                buttonsStyling: false,
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            });
                        }

                        // Remove loading indication
                        eButton.removeAttribute('data-kt-indicator');

                        // Enable button
                        eButton.disabled = false;

                        return false
                    }


                    // Remove loading indication
                    eButton.removeAttribute('data-kt-indicator');

                    // Enable button
                    eButton.disabled = false;

                    swalWithBootstrapButtons.fire(
                        'Send!',
                        data.message,
                        'success'
                    )
                }

                return 0
            } catch (error) {
                console.log(error);

                // Remove loading indication
                eButton.removeAttribute('data-kt-indicator');

                // Enable button
                eButton.disabled = false;
            }
        }

        Echo.channel('device-status')
            .listen('DeviceEvent', (e) => {
                for (const selenoid of e.selenoid) {
                    changeStatus(selenoid.status, document.querySelector("#status-sv-" + selenoid.id))
                    changeStatus(selenoid.status == 1 ? e.air : 0, document.querySelector("#status-penyiraman-" + selenoid.id))
                    changeStatus(selenoid.status == 1 ? e.pemupukan : 0, document.querySelector("#status-pemupukan-" + selenoid.id))
                }
            })

        window.onload = () => {
            console.log('Hello World');

            let buttonArea = document.querySelector('#btn-action')
            if (buttonArea) {
                buttonArea.addEventListener('click', e => {
                    console.dir(e.target)
                    switch (e.target.id) {
                        case "btn-kirim-setting":
                            sendSettingMessage(e.target)
                            break;
                        case "btn-reset-perangkat":
                            deleteResetDevice(e.target)
                            break;
                        case "btn-pemupukan-berhenti":
                            stopFertilization(e.target)
                            break;

                        default:
                            break;
                    }
                })
            }

            let svArea = document.querySelector('#devices-box')
            if (svArea) {
                svArea.addEventListener('click', e => {
                    if (e.target.type === "button" && e.target.classList.contains("btn-pe-status")) {

                        if (!e.target.dataset.motor || e.target.dataset.motor != 1) {
                            alert("Harap pilih pompa yang benar")
                            return false
                        }

                        if (!e.target.dataset?.type && (e.target.dataset.type == 'penyiraman' || e.target.dataset.type == 'pemupukan')) {
                            alert("Harap pilih pompa yang benar")
                            return false
                        }

                        if (!e.target.dataset?.id) {
                            alert("ID perangkat tidak ditemukan!")
                            return false
                        }

                        if (!e.target.dataset?.pe) {
                            alert("PE perangkat tidak ditemukan!")
                            return false
                        }

                        reqChangePeStatus(e.target, e.target.dataset.id, e.target.dataset.pe, e.target.dataset.status, e.target.dataset.type)
                    } else if (e.target.type === "button" && e.target.classList.contains("btn-selenoid-delete")) {
                        deleteSelenoid(e.target)
                    }
                })
            }

            document.querySelector('#tbody-finished-schedules').addEventListener('click', e => {
                if (e.target.type === "button" && e.target.classList.contains("btn-schedule-delete")) {
                    // console.dir(e.target.dataset)
                    deleteFinishedSchedule(e.target)
                }
            })

        }
    </script>
    @endpush
</x-app-layout>
