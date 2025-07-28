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

    <div class="row g-2">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="d-flex flex-column align-items-center gap-4">
                                @if ($device->picture)
                                <img src="{{ asset($device->picture) }}" alt="perangkat-foto" class="d-block" style="width: 100%;" id="uploadedAvatar" />
                                @endif
                                <h3>{{ $device->device_series }}</h3>
                            </div>
                        </div>
                        <div class="col-12 mb-2">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('bitanic.device.edit', $device->id) }}" class="btn btn-warning btn-sm w-100">
                                    <i class="bx bx-edit-alt"></i> Edit Perangkat
                                </a>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="table-responsive text-wrap">
                                <table class="table table-bordered">
                                    <tbody class="table-border-bottom-0">
                                        <tr>
                                            <th class="align-middle text-center bg-primary text-white" colspan="2">Detail</th>
                                        </tr>
                                        <tr>
                                            <td class="text-start bg-info text-white">Kategori</td>
                                            <td class="text-start" id="device-category">{{ $device->category }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-start bg-info text-white">Seri Perangkat</td>
                                            <td class="text-start" id="device-seri-perangkat">{{ $device->device_series }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-start bg-info text-white">Tipe</td>
                                            <td class="text-start" id="device-type">{{ $device->type }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-start bg-info text-white">Versi</td>
                                            <td class="text-start" id="device-versi">{{ $device->version }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-start bg-info text-white">Tanggal Produksi</td>
                                            <td class="text-start" id="device-tgl-produksi">{{ $device->production_date }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-start bg-info text-white">Tanggal Pembelian</td>
                                            <td class="text-start" id="device-tgl-pembelian">{{ $device->purchase_date }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-start bg-info text-white">Tanggal Diaktifkan</td>
                                            <td class="text-start" id="device-tgl-diaktifkan">{{ $device->activate_date }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-start bg-info text-white">Status</td>
                                            <td class="text-start" id="device-status">

                                                <span @class([
                                                        "badge",
                                                        "bg-label-primary" => $device->status == 1,
                                                        "bg-label-danger" => $device->status == 0,
                                                    ])>{{ $device->status == 1 ? 'Aktif' : 'Tidak Aktif' }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-start bg-info text-white">Pemilik Perangkat</td>
                                            <td class="text-start" id="device-farmer">{{ $device?->farmer?->full_name }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-12 mb-2">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('bitanic.device.edit-specification', $device->id) }}" class="btn btn-warning btn-sm w-100">
                                    <i class="bx bx-edit-alt"></i> Edit Spesifikasi
                                </a>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="table-responsive text-wrap">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="align-middle text-center bg-primary text-white" colspan="2">Specification</th>
                                        </tr>
                                        <tr>
                                            <th class="align-middle text-center bg-info text-white">Name</th>
                                            <th class="align-middle text-center bg-info text-white">Value</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0" id="view-spesifik">
                                        @foreach ($device->specification as $specification)
                                        <tr>
                                            <td class="text-center">{{ $specification->name }}</td>
                                            <td class="text-center">{{ $specification->value }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if ($device->garden)
            <div class="col-12 col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="row" id="devices-box">
                            <div class="col-12">
                                <h5 class="card-title d-flex justify-content-between">
                                    <span>{{ $device->device_series }}</span>
                                    <span @class([ 'device-status' , 'bg-on-status'=> $device->status,
                                        ])></span>
                                </h5>
                                <div class="card-subtitle text-muted">{{ $device->garden?->land?->name }}</div>
                            </div>
                            <div class="col-12">
                                <div class="mt-3 d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex justify-content-between">
                                            <span id="status-irigasi-{{ $device->id }}" @class([ 'me-1' , 'device-status' , 'bg-on-status'=> $device->status_motor_1 == 1,
                                                ])
                                                ></span>
                                            Pompa Irigasi
                                        </div>

                                        <div>
                                            <button type="button" id="btn-irigasi-{{ $device->id }}-on" data-motor="1" data-id="{{ $device->garden->id }}" data-status="1" @class([ 'btn' , 'btn-sm' , 'btn-motor-status' , 'btn-primary' , ])>
                                                ON
                                            </button>
                                            <button type="button" id="btn-irigasi-{{ $device->id }}-off" data-motor="1" data-id="{{ $device->garden->id }}" data-status="0" @class([ 'btn' , 'btn-sm' , 'btn-motor-status' , 'btn-secondary' ])>
                                                OFF
                                            </button>
                                        </div>
                                    </div>
                                    @if ($device->type == 2 && $device->irrigation)
                                    @foreach ($device->irrigation as $irrigation)
                                    <div class="d-flex justify-content-between align-items-center ms-3 mt-1">
                                        <div class="d-flex justify-content-between">
                                            <span id="status-pe-irrigation-{{ $device->id }}-{{ $irrigation['id'] }}" @class([ 'me-1' , 'device-status' , 'bg-on-status'=> $irrigation['status'] == 1,
                                                ])
                                                ></span>
                                            PE {{ $irrigation['id'] }}
                                        </div>

                                        <div>
                                            <button type="button" data-motor="1" data-pe="{{ $irrigation['id'] }}" data-id="{{ $device->id }}" data-status="1" @class([ 'btn' , 'btn-sm' , 'btn-pe-status' , 'btn-primary' , ])>
                                                ON
                                            </button>
                                            <button type="button" data-motor="1" data-pe="{{ $irrigation['id'] }}" data-id="{{ $device->id }}" data-status="0" @class([ 'btn' , 'btn-sm' , 'btn-pe-status' , 'btn-secondary' ])>
                                                OFF
                                            </button>
                                        </div>
                                    </div>
                                    @endforeach
                                    @endif
                                    <hr>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex justify-content-between">
                                            <span id="status-vertigasi-{{ $device->id }}" @class([ 'me-1' , 'device-status' , 'bg-on-status'=> $device->status_motor_2 == 1,
                                                ])
                                                ></span>
                                            Pompa Fertigasi
                                        </div>
                                        <div>
                                            <button type="button" data-motor="2" data-id="{{ $device->garden->id }}" data-status="1" @class([ 'btn' , 'btn-sm' , 'btn-motor-status' , 'btn-primary' , ])>
                                                ON
                                            </button>
                                            <button type="button" data-motor="2" data-id="{{ $device->garden->id }}" data-status="0" @class([ 'btn' , 'btn-sm' , 'btn-motor-status' , 'btn-secondary' ])>
                                                OFF
                                            </button>
                                        </div>
                                    </div>
                                    @if ($device->type == 2 && $device->vertigation)
                                    @foreach ($device->vertigation as $vertigation)
                                    <div class="d-flex justify-content-between align-items-center ms-3 mt-1">
                                        <div class="d-flex justify-content-between">
                                            <span id="status-pe-vertigation-{{ $device->id }}-{{ $vertigation['id'] }}" @class([ 'me-1' , 'device-status' , 'bg-on-status'=> $vertigation['status'] == 1,
                                                ])
                                                ></span>
                                            PE {{ $vertigation['id'] }}
                                        </div>

                                        <div>
                                            <button type="button" data-motor="2" data-pe="{{ $vertigation['id'] }}" data-id="{{ $device->id }}" data-status="1" @class([ 'btn' , 'btn-sm' , 'btn-pe-status' , 'btn-primary' , ])>
                                                ON
                                            </button>
                                            <button type="button" data-motor="2" data-pe="{{ $vertigation['id'] }}" data-id="{{ $device->id }}" data-status="0" @class([ 'btn' , 'btn-sm' , 'btn-pe-status' , 'btn-secondary' ])>
                                                OFF
                                            </button>
                                        </div>
                                    </div>
                                    @endforeach
                                    @endif
                                </div>
                            </div>
                            <div class="col-12 mt-3">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('bitanic.device.edit-pe', ['device' => $device->id, 'pe' => 'irrigation']) }}" class="btn btn-sm btn-warning w-100" title="Edit PE Irigasi">
                                        <i class="bx bx-edit-alt"></i> Edit Irigasi
                                    </a>
                                    <a href="{{ route('bitanic.device.edit-pe', ['device' => $device->id, 'pe' => 'vertigation']) }}" class="btn btn-sm btn-warning w-100" title="Edit PE Fertigasi">
                                        <i class="bx bx-edit-alt"></i> Edit Fertigasi
                                    </a>
                                </div>
                            </div>
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

        // async function reqChangePeStatus(eButton, id, pe, status, type) {
        //     try {
        //         const swalWithBootstrapButtons = Swal.mixin({
        //             customClass: {
        //                 confirmButton: 'btn btn-success',
        //                 cancelButton: 'btn btn-danger'
        //             },
        //             buttonsStyling: false
        //         })

        //         // const swalText = (status == 1)
        //         //     ? "Mengubah status data menjadi tidak menerima membuat sistem tidak akan menerima data dari alat. (Anda masih bisa mengubah settingan ini)"
        //         //     : "Mengubah status data menjadi menerima membuat sistem dapat menerima data dari alat."

        //         const result = await Swal.fire({
        //             title: 'Apakah anda yakin?',
        //             text: "Anda akan mengirim command kepada alat anda.",
        //             icon: 'warning',
        //             showCancelButton: true,
        //             confirmButtonColor: '#3085d6',
        //             cancelButtonColor: '#d33',
        //             confirmButtonText: 'Iya, ganti status!',
        //             cancelButtonText: 'Batalkan'
        //         })

        //         if (result.isConfirmed) {
        //             // Show loading indication
        //             eButton.setAttribute('data-kt-indicator', 'on');

        //             // Disable button to avoid multiple click
        //             eButton.disabled = true;

        //             const formData = new FormData();
        //             formData.append("type", type)
        //             formData.append("selenoid", pe)
        //             formData.append("status", status)

        //             let url = "{{ route('bitanic.v3-device.status-change', 'ID') }}".replace('ID', id)

        //             const settings = {
        //                 method: 'POST',
        //                 headers: {
        //                     'x-csrf-token': '{{ csrf_token() }}',
        //                     'Accept': 'application/json'
        //                 },
        //                 body: formData
        //             }

        //             const [data, error] = await yourRequest(url, settings)

        //             if (error) {
        //                 if ("messages" in error) {
        //                     let errorMessage = ''

        //                     let element = ``
        //                     for (const key in error.messages) {
        //                         if (Object.hasOwnProperty.call(error.messages, key)) {
        //                             error.messages[key].forEach(message => {
        //                                 element += `<li>${message}</li>`;
        //                             });
        //                         }
        //                     }

        //                     errorMessage = `<ul>${element}</ul>`

        //                     Swal.fire({
        //                         html: errorMessage,
        //                         icon: "error",
        //                         buttonsStyling: false,
        //                         customClass: {
        //                             confirmButton: "btn btn-primary"
        //                         }
        //                     });
        //                 } else if ("message" in error) {
        //                     Swal.fire({
        //                         html: error.message,
        //                         icon: "error",
        //                         buttonsStyling: false,
        //                         customClass: {
        //                             confirmButton: "btn btn-primary"
        //                         }
        //                     });
        //                 }

        //                 // Remove loading indication
        //                 eButton.removeAttribute('data-kt-indicator');

        //                 // Enable button
        //                 eButton.disabled = false;

        //                 return false
        //             }


        //             // Remove loading indication
        //             eButton.removeAttribute('data-kt-indicator');

        //             // Enable button
        //             eButton.disabled = false;

        //             swalWithBootstrapButtons.fire(
        //                 'Send!',
        //                 data.message,
        //                 'success'
        //             )
        //         }

        //         return 0
        //     } catch (error) {
        //         console.log(error);

        //         // Remove loading indication
        //         eButton.removeAttribute('data-kt-indicator');

        //         // Enable button
        //         eButton.disabled = false;
        //     }
        // }

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

        window.onload = () => {
            console.log('Hello World');

            // let buttonArea = document.querySelector('#btn-action')
            // if (buttonArea) {
            //     buttonArea.addEventListener('click', e => {
            //         console.dir(e.target)
            //         switch (e.target.id) {
            //             case "btn-kirim-setting":
            //                 sendSettingMessage(e.target)
            //                 break;
            //             case "btn-reset-perangkat":
            //                 deleteResetDevice(e.target)
            //                 break;
            //             case "btn-pemupukan-berhenti":
            //                 stopFertilization(e.target)
            //                 break;

            //             default:
            //                 break;
            //         }
            //     })
            // }

            // let svArea = document.querySelector('#devices-box')
            // if (svArea) {
            //     svArea.addEventListener('click', e => {
            //         if (e.target.type === "button" && e.target.classList.contains("btn-pe-status")) {

            //             if (!e.target.dataset.motor || e.target.dataset.motor != 1) {
            //                 alert("Harap pilih pompa yang benar")
            //                 return false
            //             }

            //             if (!e.target.dataset?.type && (e.target.dataset.type == 'penyiraman' || e.target.dataset.type == 'pemupukan')) {
            //                 alert("Harap pilih pompa yang benar")
            //                 return false
            //             }

            //             if (!e.target.dataset?.id) {
            //                 alert("ID perangkat tidak ditemukan!")
            //                 return false
            //             }

            //             if (!e.target.dataset?.pe) {
            //                 alert("PE perangkat tidak ditemukan!")
            //                 return false
            //             }

            //             reqChangePeStatus(e.target, e.target.dataset.id, e.target.dataset.pe, e.target.dataset.status, e.target.dataset.type)
            //         } else if (e.target.type === "button" && e.target.classList.contains("btn-selenoid-delete")) {
            //             deleteSelenoid(e.target)
            //         }
            //     })
            // }

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
        }
    </script>
    @endpush
</x-app-layout>
