<x-app-layout>

    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
    <style>
        .flex-nowrap {
            flex-wrap: nowrap !important;
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
    </style>
    @endpush

    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master / <a href="{{ route('bitanic.farmer.index') }}">Data Pengguna</a> / </span>{{ $user->name }}</h4>
    </x-slot>
    {{-- End Header --}}

    <div class="row g-2">
        <div class="col-12 col-md-4">
            <!-- Striped Rows -->
            <div class="card">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-12">
                            <div class="text-center">
                                <img src="{{ $user->farmer->picture ? asset($user->farmer->picture) : asset('bitanic-landing/default-profile.png') }}" alt="user-avatar" class="rounded" height="100" width="100" id="uploadedAvatar" />
                                <h5 class="card-title mt-3">{{ $user->name }}</h5>
                                <a href="{{ route('bitanic.farmer.edit', $user->id) }}" title="Edit Pengguna" class="btn btn-sm btn-icon btn-warning"><i class="bx bx-edit-alt"></i></a>
                                <button type="button" onclick="handleDeleteRows({{ $user->id }}, '{{ $user->name }}')" title="Hapus Pengguna" class="btn btn-sm btn-icon btn-outline-danger"><i class="bx bx-trash"></i></button>
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="" class="fw-bold">Tipe</label>
                            <p class="card-text">
                                {{ $user->farmer->type }}
                            </p>
                        </div>
                        <div class="col-12">
                            <label for="" class="fw-bold">Kategori</label>
                            <p class="card-text">
                                {{ farmerCategory($user->farmer->category) }}
                            </p>
                        </div>
                        <div class="col-12">
                            <label for="" class="fw-bold">Jenis Kelamin</label>
                            <p class="card-text">
                                @switch($user->farmer->gender)
                                @case('l')
                                Laki-laki
                                @break
                                @case('p')
                                Perempuan
                                @break

                                @default
                                Lain-lain
                                @endswitch
                            </p>
                        </div>
                        <div class="col-12">
                            <label for="" class="fw-bold">NIK</label>
                            <p class="card-text">
                                {{ $user->farmer->nik }}
                            </p>
                        </div>
                        <div class="col-12">
                            <label for="" class="fw-bold">Tanggal Lahir</label>
                            <p class="card-text">
                                @if ($user->farmer->birth_date)
                                    {{ carbon_format_id_flex($user->farmer->birth_date, '-', '/') }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div class="col-12">
                            <label for="" class="fw-bold">No Handphone</label>
                            <p class="card-text">
                                {{ $user->phone_number }}
                            </p>
                        </div>
                        <div class="col-12">
                            <label for="" class="fw-bold">Alamat</label>
                            <p class="card-text">
                                {{ $user->farmer->address }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Striped Rows -->
        </div>
        <div class="col-12 col-md-8">
            <div class="row g-2">
                @php
                    $farmer_menus = collect([
                            [
                                "title" => "Data Lahan",
                                "icon" => "bx-shape-square",
                                "url" => route('bitanic.land.index', $user->farmer->id)
                            ],
                            // [
                            //     "title" => "Data Kebun",
                            //     "icon" => "bx-box",
                            //     "url" => route('bitanic.garden.index', ['farmer' => $user->farmer->id])
                            // ],
                            // [
                            //     "title" => "Data RSC",
                            //     "icon" => "bx-list-ul",
                            //     "url" => route('bitanic.stick-telemetri.index', ['farmer' => $user->farmer->id])
                            // ],
                        ]);
                @endphp
                @foreach ($farmer_menus as $farmer_menu)
                    <div class="col-12">
                        <a href="{{ $farmer_menu['url'] }}">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-0 text-center"><i class="bx {{ $farmer_menu['icon'] }} fs-2"></i> {{ $farmer_menu['title'] }}</h5>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
                <div class="col-12">
                    <div class="row g-2" id="devices-box">
                        @foreach ($devices as $device)
                            <div class="col-12 col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title d-flex justify-content-between">
                                            <span>{{ $device->device_series }}</span>
                                            <span @class([
                                                'device-status',
                                                'bg-on-status' => $device->status,
                                            ])></span>
                                        </h5>
                                        <div class="card-subtitle text-muted">{{ $device->garden->land->name }}</div>
                                        <div class="mt-3 d-flex flex-column">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex justify-content-between">
                                                    <span
                                                        id="status-irigasi-{{ $device->id }}"
                                                        @class([
                                                            'me-1',
                                                            'device-status',
                                                            'bg-on-status' => $device->status_motor_1 == 1,
                                                        ])
                                                    ></span>
                                                    Pompa Irigasi
                                                </div>

                                                <div>
                                                    <button type="button" id="btn-irigasi-{{ $device->id }}-on"
                                                        data-motor="1" data-id="{{ $device->garden->id }}" data-status="1"
                                                        @class([
                                                            'btn',
                                                            'btn-sm',
                                                            'btn-motor-status',
                                                            'btn-primary',
                                                        ])>
                                                        ON
                                                    </button>
                                                    <button type="button" id="btn-irigasi-{{ $device->id }}-off"
                                                        data-motor="1" data-id="{{ $device->garden->id }}" data-status="0"
                                                        @class([
                                                            'btn',
                                                            'btn-sm',
                                                            'btn-motor-status',
                                                            'btn-secondary'
                                                        ])>
                                                        OFF
                                                    </button>
                                                </div>
                                            </div>
                                            @if ($device->irrigation)
                                                @foreach ($device->irrigation as $irrigation)
                                                    <div class="d-flex justify-content-between align-items-center ms-3 mt-1">
                                                        <div class="d-flex justify-content-between">
                                                            <span
                                                                id="status-pe-irrigation-{{ $device->id }}-{{ $irrigation['id'] }}"
                                                                @class([
                                                                    'me-1',
                                                                    'device-status',
                                                                    'bg-on-status' => $irrigation['status'] == 1,
                                                                ])
                                                            ></span>
                                                            PE {{ $irrigation['id'] }}
                                                        </div>

                                                        <div>
                                                            <button type="button"
                                                                data-motor="1" data-pe="{{ $irrigation['id'] }}" data-id="{{ $device->id }}" data-status="1"
                                                                @class([
                                                                    'btn',
                                                                    'btn-sm',
                                                                    'btn-pe-status',
                                                                    'btn-primary',
                                                                ])>
                                                                ON
                                                            </button>
                                                            <button type="button"
                                                                data-motor="1" data-pe="{{ $irrigation['id'] }}" data-id="{{ $device->id }}" data-status="0"
                                                                @class([
                                                                    'btn',
                                                                    'btn-sm',
                                                                    'btn-pe-status',
                                                                    'btn-secondary'
                                                                ])>
                                                                OFF
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                            <hr>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex justify-content-between">
                                                    <span
                                                        id="status-vertigasi-{{ $device->id }}"
                                                        @class([
                                                            'me-1',
                                                            'device-status',
                                                            'bg-on-status' => $device->status_motor_2 == 1,
                                                        ])
                                                    ></span>
                                                    Pompa Fertigasi
                                                </div>
                                                <div>
                                                    <button type="button"
                                                        data-motor="2" data-id="{{ $device->garden->id }}" data-status="1"
                                                        @class([
                                                            'btn',
                                                            'btn-sm',
                                                            'btn-motor-status',
                                                            'btn-primary',
                                                        ])>
                                                        ON
                                                    </button>
                                                    <button type="button"
                                                        data-motor="2" data-id="{{ $device->garden->id }}" data-status="0"
                                                        @class([
                                                            'btn',
                                                            'btn-sm',
                                                            'btn-motor-status',
                                                            'btn-secondary'
                                                        ])>
                                                        OFF
                                                    </button>
                                                </div>
                                            </div>
                                            @if ($device->vertigation)
                                                @foreach ($device->vertigation as $vertigation)
                                                    <div class="d-flex justify-content-between align-items-center ms-3 mt-1">
                                                        <div class="d-flex justify-content-between">
                                                            <span
                                                                id="status-pe-vertigation-{{ $device->id }}-{{ $vertigation['id'] }}"
                                                                @class([
                                                                    'me-1',
                                                                    'device-status',
                                                                    'bg-on-status' => $vertigation['status'] == 1,
                                                                ])
                                                            ></span>
                                                            PE {{ $vertigation['id'] }}
                                                        </div>

                                                        <div>
                                                            <button type="button"
                                                                data-motor="2" data-pe="{{ $vertigation['id'] }}" data-id="{{ $device->id }}" data-status="1"
                                                                @class([
                                                                    'btn',
                                                                    'btn-sm',
                                                                    'btn-pe-status',
                                                                    'btn-primary',
                                                                ])>
                                                                ON
                                                            </button>
                                                            <button type="button"
                                                                data-motor="2" data-pe="{{ $vertigation['id'] }}" data-id="{{ $device->id }}" data-status="0"
                                                                @class([
                                                                    'btn',
                                                                    'btn-sm',
                                                                    'btn-pe-status',
                                                                    'btn-secondary'
                                                                ])>
                                                                OFF
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('bitanic.farmer._modal-foto')

    @push('scripts')
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/device/statuschange.js') }}"></script>
    <script>
        async function reqChangePeStatus(id, pe, status, motor) {
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
                    showSpinner()

                    const formData = new FormData();
                    formData.append('_method', 'PUT')
                    formData.append("status", status)
                    formData.append("pump", motor)
                    formData.append("pe", pe)

                    let url = "{{ route('bitanic.device.change-pe-status', 'ID') }}".replace('ID', id)

                    const settings = {
                        method: 'POST',
                        headers: {
                            'x-csrf-token': '{{ csrf_token() }}'
                        },
                        body: formData
                    }

                    const [data, error] = await yourRequest(url, settings)

                    deleteSpinner()

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
                        }

                        return false
                    }

                    swalWithBootstrapButtons.fire(
                        'Send!',
                        data.message,
                        'success'
                    )
                }

                return 0
            } catch (error) {
                console.log(error);
            }
        }

        async function reqChangeStatus(id, status, motor) {
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
                    showSpinner()

                    const formData = new FormData();
                    formData.append("status", status)
                    formData.append("motor", motor)

                    let url = "{{ route('web.motor-status.update', ['garden' => 'ID']) }}".replace('ID', id)
                    formData.append('_method', 'PUT')

                    const settings = {
                        method: 'POST',
                        headers: {
                            'x-csrf-token': '{{ csrf_token() }}'
                        },
                        body: formData
                    }

                    const [data, error] = await yourRequest(url, settings)

                    deleteSpinner()

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
                        }

                        return false
                    }

                    swalWithBootstrapButtons.fire(
                        'Send!',
                        data.message,
                        'success'
                    )
                }

                return 0
            } catch (error) {
                console.log(error);
            }
        }

        async function handleDeleteRows(id, name) {
            let result = await Swal.fire({
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

            if (result.value) {
                Swal.fire({
                    html: '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span class=""> Loading...</span>',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                });
                // Simulate delete request -- for demo purpose only
                const url = "{{ route('bitanic.farmer.destroy', 'ID') }}"
                let newUrl = url.replace('ID', id)
                let settings = {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json;charset=utf-8',
                        'x-csrf-token': '{{ csrf_token() }}'
                    },
                }

                try {
                    let response = await fetch(newUrl, settings)

                    Swal.fire({
                        text: "Kamu berhasil menghapus data " + name + "!.",
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "Ok",
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        }
                    }).then(function () {
                        // delete row data from server and re-draw datatable
                        window.location.replace("{{ route('bitanic.farmer.index') }}");
                    });
                } catch (error) {
                    let errorMessage = error

                    if (error.hasOwnProperty('response')) {
                        if (error.response.status == 422) {
                            errorMessage = 'Data yang dikirim tidak sesuai'
                        }
                    }

                    Swal.fire({
                        text: errorMessage,
                        icon: "error",
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                }
            }

        }

        Echo.channel('Pumps')
            .listen('.PumpEvent', (e) => {
                console.log(e);

                changeStatus(e.irigasi, document.querySelector("#status-irigasi-" + e.id))
                changeStatus(e.vertigasi, document.querySelector("#status-vertigasi-" + e.id))

                for (const pe1 of e.pe_irrigation) {
                    changeStatus(pe1.status, document.querySelector("#status-pe-irrigation-" + e.id + "-" + pe1.id))
                }

                for (const pe1 of e.pe_vertigation) {
                    changeStatus(pe1.status, document.querySelector("#status-pe-vertigation-" + e.id + "-" + pe1.id))
                }
            })

        document.addEventListener("DOMContentLoaded", () => {
            console.log('Hello world');

            document.querySelector('#devices-box').addEventListener('click', e => {
                if (e.target.type === "button" && e.target.classList.contains("btn-motor-status")) {
                    if (!e.target.dataset.motor || e.target.dataset.motor != 1 && e.target.dataset.motor != 2) {
                        alert("Harap pilih pompa yang benar")
                        return false
                    }

                    if (!e.target.dataset?.id) {
                        alert("ID perangkat tidak ditemukan!")
                        return false
                    }

                    reqChangeStatus(e.target.dataset.id, e.target.dataset.status, e.target.dataset.motor)
                }
                if (e.target.type === "button" && e.target.classList.contains("btn-pe-status")) {
                    console.dir(e.target)
                    if (!e.target.dataset.motor || e.target.dataset.motor != 1 && e.target.dataset.motor != 2) {
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

                    reqChangePeStatus(e.target.dataset.id, e.target.dataset.pe, e.target.dataset.status, e.target.dataset.motor)
                }
            })
        })
    </script>
    @endpush
</x-app-layout>
