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

            .event-none {
                pointer-events: none;
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

            @media (max-width: 600px) {
                .preview-image {
                    width: calc(100% - 10px);
                }
            }
        </style>
    @endpush
    {{-- Header --}}
    <x-slot name="header">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb fs-4">
                <li class="breadcrumb-item">
                    <a href="javascript:void(0);">Hidroponik</a>
                </li>
                <li class="breadcrumb-item active">Perangkat IoT</li>
            </ol>
        </nav>
    </x-slot>
    {{-- End Header --}}

    @if (session()->has('success'))
        <x-alert-message class="alert-success">{{ session()->get('success') }}</x-alert-message>
    @endif

    <div class="row g-2">
        <div class="col-12">
            <!-- Striped Rows -->
            <div class="card">
                <div class="row">
                    <div class="col-12">
                        <div class="float-start m-3">
                        </div>
                        <div class="float-end m-3">
                            <a href="{{ route('bitanic.hydroponic.device.create') }}" class="btn btn-icon btn-primary"
                                title="Tambah Perangkat">
                                <i class="bx bx-plus"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Striped Rows -->
        </div>
        <div class="col-12">
            <div class="row g-2">
                @foreach ($hydroponicDevices as $hydroponicDevice)
                    <div class="col-12 col-md-4">
                        <a href="{{ route('bitanic.hydroponic.device.show', $hydroponicDevice->id) }}">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex flex-column gap-2">
                                        <div class="d-flex flex-wrap justify-content-between gap-2 mb-2">
                                            <div>
                                                <h3 class="card-title">{{ $hydroponicDevice->series }}</h3>
                                                <h6 class="card-subtitle text-muted">{{ $hydroponicDevice->hydroponicUser?->name ?? '-' }}</h6>
                                            </div>
                                            <div>
                                                @if ($hydroponicDevice->activation_date)
                                                    <button @class([
                                                            'btn',
                                                            'h-100',
                                                            'btn-success' => ($hydroponicDevice->is_auto === 1),
                                                            'btn-danger' => ($hydroponicDevice->is_auto === 0),
                                                        ])
                                                        disabled>
                                                        @switch($hydroponicDevice->is_auto)
                                                            @case(0)
                                                                Manual
                                                                @break
                                                            @case(1)
                                                                Auto
                                                                @break
                                                            @default
                                                                -
                                                        @endswitch
                                                    </button>
                                                @else
                                                    <span class="badge bg-danger">Belum Aktivasi</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="d-flex flex-wrap gap-3 text-body">
                                            @foreach ($hydroponicDevice->pumps as $key => $pumpStatus)
                                                <div class="d-flex justify-content-between">
                                                    <span id="status-{{ $key }}" @class([
                                                        'me-1',
                                                        'device-status',
                                                        'bg-on-status' => $pumpStatus == 1,
                                                    ])></span>
                                                    {{ hydroponicPumpLabel($key) }}
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="d-flex flex-wrap justify-content-between text-body">
                                            <div>v{{ $hydroponicDevice->version }}</div>
                                            <div>Produksi: {{ $hydroponicDevice->production_date }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @include('bitanic.lite.farmer._modal-picture')

    @push('scripts')
        <script>
            // btn picture
            const myModalPrev = new bootstrap.Modal(document.getElementById("modalFoto"), {});
            const modalFoto = document.getElementById('modalFoto')
            modalFoto.addEventListener('show.bs.modal', function(event) {
                // Button that triggered the modal
                const button = event.relatedTarget
                // Extract info from data-bs-* attributes
                // const recipient = button.getAttribute('data-bs-whatever')
                const modalTitle = modalFoto.querySelector('.modal-title')
                modalTitle.textContent = 'Foto Petani'

                for (let index = 0; index < button.attributes.length; index++) {
                    if (button.attributes[index].nodeName.includes('data-foto')) {
                        document.getElementById('iframe').src = button.attributes[index].nodeValue
                    }
                }

            })

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
                    "{{ route('bitanic.hydroponic.user.destroy', 'ID') }}".replace('ID',
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

                window.location = "{{ route('bitanic.hydroponic.user.index') }}"
            }

            const changeStatus = async e => {
                const result = await Swal.fire({
                    title: "Ubah status?",
                    text: "Perubahan ini akan mempengaruhi data yang tampil di halaman utama.",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Ya",
                    cancelButtonText: "Batalkan",
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
                    }
                }

                const [data, error] = await yourRequest(
                    "{{ route('bitanic.career.change-status', 'ID') }}".replace('ID',
                        e.dataset.id), settings
                )

                deleteSpinner()
                if (error) {

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
                    text: "Kamu berhasil mengubag data " + name + "!",
                    icon: "success",
                    showConfirmButton: true,
                    allowOutsideClick: false
                })

                window.location = "{{ route('bitanic.career.index') }}"
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
