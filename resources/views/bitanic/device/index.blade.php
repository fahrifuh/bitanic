<x-app-layout>
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master /</span> Data Bitanic Pro & RSC</h4>
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
                    <div class="col-md-5">
                        <div class="float-start">
                            <!-- Search -->
                            <form action="{{ route('bitanic.device.index') }}" method="GET" id="form-search">
                                <div class="row g-2 p-3 pb-1">
                                    <div class="col-md-12">
                                        <div class="input-group">
                                            <span class="input-group-text bg-primary text-white"
                                                style="cursor: pointer;"
                                                onclick="document.getElementById('form-search').submit()">
                                                <i class="bx bx-search"></i>
                                            </span>
                                            <input type="text" class="form-control shadow-none"
                                                placeholder="Cari Seri Perangkat..." aria-label="Cari Seri Perangkat..."
                                                name="search" value="{{ request()->query('search') }}" />
                                        </div>
                                    </div>
                                    @if (auth()->user()->role != 'farmer')
                                        <div class="col-md-12">
                                            <div class="input-group">
                                                <span class="input-group-text bg-primary text-white"><small>Status
                                                        Pemilik</small></span>
                                                <select class="form-select select-search" id="select-pemilik"
                                                    name="pemilik" aria-label="Default select example">
                                                    <option value="all"
                                                        @if (!in_array(request()->query('pemilik'), [0, 1])) selected @endif>Semua</option>
                                                    <option value="1"
                                                        @if (is_numeric(request()->query('pemilik')) && request()->query('pemilik') == 1) selected @endif>Memiliki
                                                        Pemilik</option>
                                                    <option value="0"
                                                        @if (is_numeric(request()->query('pemilik')) && request()->query('pemilik') == 0) selected @endif>Tidak Memiliki
                                                        Pemilik</option>
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-md-12">
                                        <div class="input-group">
                                            <span class="input-group-text bg-primary text-white"><small>Status
                                                    Aktivasi</small></span>
                                            <select class="form-select select-search" id="select-aktivasi"
                                                name="aktivasi" aria-label="Default select example">
                                                <option value="all"
                                                    @if (!in_array(request()->query('aktivasi'), ['sudah', 'belum'])) selected @endif>Semua</option>
                                                <option value="sudah"
                                                    @if (request()->query('aktivasi') == 'sudah') selected @endif>Sudah Aktivasi
                                                </option>
                                                <option value="belum"
                                                    @if (request()->query('aktivasi') == 'belum') selected @endif>Belum Aktivasi
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="input-group">
                                            <span class="input-group-text bg-primary text-white"><small>Kategori
                                                    Perangkat</small></span>
                                            <select class="form-select select-search" id="select-tipe" name="tipe"
                                                aria-label="Default select example">
                                                <option value="all"
                                                    @if (!in_array(request()->query('tipe'), ['controller', 'tongkat'])) selected @endif>Semua</option>
                                                <option value="controller"
                                                    @if (request()->query('tipe') == 'controller') selected @endif>Controller
                                                </option>
                                                <option value="tongkat"
                                                    @if (request()->query('tipe') == 'tongkat') selected @endif>Tongkat
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <!-- /Search -->
                        </div>
                    </div>
                    <div class="col-md-3"></div>
                    <div class="col-md-4">
                        <div class="float-end m-3">
                            <button type="button" data-bs-toggle="modal" data-bs-target="#modalSelect"
                                class="btn btn-primary">
                                <i class="bx bx-plus"></i>&nbsp;Tambah
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive text-wrap">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ID Perangkat</th>
                                <th>Kategori</th>
                                <th>Pemilik Perangkat</th>
                                <th>Tanggal Aktivasi</th>
                                <th>Status</th>
                                <th>Status Data</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($data as $device)
                                <tr>
                                    <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                                    <td>{{ $device->device_series }}</td>
                                    <td>{{ ucwords($device->category) }}</td>
                                    <td @class(['text-danger' => $device->farmer_id ? false : true])>
                                        {{ optional($device->farmer)->full_name ?? 'Tidak Memiliki Pemilik' }}
                                    </td>
                                    <td>{{ $device->activate_date ?? 'Belum Aktivasi' }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-label-{{ $device->status == 0 ? 'danger' : 'success' }}">
                                            {{ $device->status == 0 ? 'Tidak Aktif' : 'Aktif' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge bg-label-{{ $device->block_data == 0 ? 'success' : 'danger' }}"
                                            id="text-status-{{ $device->id }}">
                                            {{ $device->block_data == 0 ? 'Menerima' : 'Tidak Menerima' }}
                                        </span>
                                        <br />
                                        <button
                                            class="btn btn-sm rounded-pill mt-1 btn-icon btn-{{ $device->block_data == 0 ? 'success' : 'danger' }}"
                                            onclick="changeStatus({{ $device->id }}, {{ $device->block_data == 0 ? 1 : 0 }})"
                                            title="Klik untuk mengubah status penerimaan data alat."
                                            id="btn-status-{{ $device->id }}"><i
                                                class='bx bx-power-off'></i></button>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-stretch gap-1">
                                            @if ($device->category == 'controller' && in_array($device->type, [1, 2]))
                                                <a class="btn btn-sm btn-icon btn-info"
                                                    href="{{ route('bitanic.device.show', $device->id) }}"
                                                    title="Detail Perangkat">
                                                    <i class="bx bx-list-ul"></i>
                                                </a>
                                                <a href="{{ route('bitanic.device.edit', $device->id) }}"
                                                    class="btn btn-sm btn-icon btn-warning" title="Edit Perangkat">
                                                    <i class="bx bx-edit-alt"></i>
                                                </a>
                                                <a href="{{ route('bitanic.device.edit-pe', ['device' => $device->id, 'pe' => 'irrigation']) }}"
                                                    class="btn btn-sm btn-icon btn-warning" title="Edit PE Irigasi">
                                                    <i class="bx bx-comment-edit"></i>
                                                </a>
                                                <a href="{{ route('bitanic.device.edit-pe', ['device' => $device->id, 'pe' => 'vertigation']) }}"
                                                    class="btn btn-sm btn-icon btn-warning" title="Edit PE Fertigasi">
                                                    <i class="bx bx-message-square-edit"></i>
                                                </a>
                                            @elseif ($device->category == 'controller' && $device->type == 3)
                                                <a href="{{ route('bitanic.v3-device.show', $device->id) }}"
                                                    class="btn btn-sm btn-icon btn-info"
                                                    title="Detail Perangkat">
                                                    <i class="bx bx-list-ul"></i>
                                                </a>
                                                <a href="{{ route('bitanic.v3-device.edit', $device->id) }}"
                                                    class="btn btn-sm btn-icon btn-warning"
                                                    title="Edit Perangkat">
                                                    <i class="bx bx-edit-alt"></i>
                                                </a>
                                            @elseif ($device->category == 'tongkat')
                                                <a href="{{ route('bitanic.device.edit', $device->id) }}"
                                                    class="btn btn-sm btn-icon btn-warning" title="Edit Perangkat">
                                                    <i class="bx bx-edit-alt"></i>
                                                </a>
                                            @endif
                                            <a href="{{ route('bitanic.device.edit-specification', $device->id) }}"
                                                class="btn btn-sm btn-icon btn-warning" title="Edit spesifikasi">
                                                <i class="bx bx-edit"></i>
                                            </a>
                                            <button class="btn btn-sm btn-icon btn-danger"
                                                onclick="destroyDevice({{ $device->id }}, '{{ $device->device_name }}')"
                                                href="javascript:void(0);" title="Hapus Perangkat">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                            @empty

                                <tr>
                                    <td colspan="8" class="text-center">Tidak Ada Data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                {{ $data->links() }}
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
            <!--/ Striped Rows -->
        </div>
    </div>

    @include('bitanic.device._modal-foto')
    @include('bitanic.device._modal-specification')
    @include('bitanic.device._modal-select')

    @push('scripts')
        <script>
            // const myModal = new bootstrap.Modal(document.getElementById("modalForm"), {});
            // const modal = document.getElementById('modalForm')
            // modal.addEventListener('show.bs.modal', function(event) {
            //     // Button that triggered the modal
            //     const button = event.relatedTarget
            //     // Extract info from data-bs-* attributes
            //     // const recipient = button.getAttribute('data-bs-whatever')
            //     const modalTitle = modal.querySelector('.modal-title')

            //     for (let index = 0; index < button.attributes.length; index++) {
            //         if (button.attributes[index].nodeName.includes('data-input')) {
            //             document.getElementById(button.attributes[index].nodeName).value = button.attributes[index].nodeValue

            //             if (button.attributes[index].nodeName == 'data-input-id') {
            //                 if (document.getElementById(button.attributes[index].nodeName).value != 'add') {
            //                     modalTitle.textContent = 'Edit'
            //                     $('.text-tidak-wajib').removeClass('d-none');
            //                     // validator.validate()
            //                 } else {
            //                     modalTitle.textContent = 'Tambah'
            //                     $('.text-tidak-wajib').addClass('d-none');
            //                 }
            //             }
            //         }
            //     }

            // })

            // Submit button handler
            // const submitButton = document.getElementById('submit-btn');
            // submitButton.addEventListener('click', async function(e) {
            //     showSpinner()
            //     // Prevent default button action
            //     e.preventDefault();

            //     // Show loading indication
            //     submitButton.setAttribute('data-kt-indicator', 'on');

            //     // Disable button to avoid multiple click
            //     submitButton.disabled = true;

            //     // Simulate form submission. For more info check the plugin's official documentation: https://sweetalert2.github.io/
            //     let url, formSubmited;
            //     const editOrAdd = document.getElementById('data-input-id');
            //     const formData = new FormData();
            //     let myFoto = document.getElementById('data-input-foto').files[0];

            //     if (typeof myFoto !== 'undefined') {
            //         formData.append("picture", myFoto)
            //     }

            //     formData.append("device_series", document.getElementById('data-input-seri-perangkat').value)
            //     formData.append("type", document.getElementById('data-input-type').value)
            //     formData.append("category", document.getElementById('data-input-category').value)
            //     formData.append("version", document.getElementById('data-input-versi').value)
            //     formData.append("production_date", document.getElementById('data-input-tgl-produksi').value)
            //     formData.append("purchase_date", document.getElementById('data-input-tgl-pembelian').value)
            //     formData.append("activate_date", document.getElementById('data-input-tgl-aktifkan').value)
            //     formData.append("farmer_id", document.getElementById('data-input-petani-id').value)

            //     let dataSpesifikasi = []

            //     document.querySelectorAll('.custom-spesifikasi').forEach(a => {
            //         let namaSpesifikasi = a.childNodes[1].children['nama_spesifikasi'].value
            //         let valueSpesifikasi = a.childNodes[3].children['value_spesifikasi'].value
            //         let idSpesifikasi = a.childNodes[3].children['value_spesifikasi'].dataset.id

            //         dataSpesifikasi.push({
            //             'name': namaSpesifikasi,
            //             'value': valueSpesifikasi,
            //             'id': idSpesifikasi
            //         })
            //     });

            //     formData.append('spesifikasi', JSON.stringify(dataSpesifikasi))

            //     if (editOrAdd.value != 'add') {
            //         url = "{{ route('bitanic.device.update', 'ID') }}".replace('ID', document.getElementById(
            //             'data-input-id').value)
            //         formData.append('_method', 'PUT')
            //     } else {
            //         url = "{{ route('bitanic.device.store') }}"
            //     }

            //     const settings = {
            //         method: 'POST',
            //         headers: {
            //             'x-csrf-token': '{{ csrf_token() }}'
            //         },
            //         body: formData
            //     }

            //     const [data, error] = await yourRequest(url, settings)

            //     deleteSpinner()
            //     myModal.toggle()

            //     // Remove loading indication
            //     submitButton.removeAttribute('data-kt-indicator');

            //     // Enable button
            //     submitButton.disabled = false;

            //     if (error) {
            //         if ("messages" in error) {
            //             let errorMessage = ''

            //             let element = ``
            //             for (const key in error.messages) {
            //                 if (Object.hasOwnProperty.call(error.messages, key)) {
            //                     error.messages[key].forEach(message => {
            //                         element += `<li>${message}</li>`;
            //                     });
            //                 }
            //             }

            //             errorMessage = `<ul>${element}</ul>`

            //             Swal.fire({
            //                 html: errorMessage,
            //                 icon: "error",
            //                 buttonsStyling: false,
            //                 customClass: {
            //                     confirmButton: "btn btn-primary"
            //                 }
            //             });
            //         }

            //         return false
            //     }

            //     Swal.fire({
            //         text: "Kamu berhasil menyimpan data!.",
            //         icon: "success",
            //         buttonsStyling: false,
            //         confirmButtonText: "Ok",
            //         customClass: {
            //             confirmButton: "btn fw-bold btn-primary",
            //         }
            //     }).then(function() {
            //         // delete row data from server and re-draw datatable
            //         window.location.reload();
            //     });
            // });

            const myModalPrev = new bootstrap.Modal(document.getElementById("modalFoto"), {});
            const modalFoto = document.getElementById('modalFoto')
            modalFoto.addEventListener('show.bs.modal', function(event) {
                // Button that triggered the modal
                const button = event.relatedTarget
                // Extract info from data-bs-* attributes
                // const recipient = button.getAttribute('data-bs-whatever')
                const modalTitle = modalFoto.querySelector('.modal-title')
                modalTitle.textContent = 'Foto Perangkat'

                for (let index = 0; index < button.attributes.length; index++) {
                    if (button.attributes[index].nodeName.includes('data-foto')) {
                        document.getElementById('iframe').src = button.attributes[index].nodeValue
                    }
                }

            })

            const destroyDevice = (id, name) => {
                handleDeleteRows("{{ route('bitanic.device.destroy', 'ID') }}".replace('ID', id), "{{ csrf_token() }}",
                    name)
            }

            async function changeStatus(id, status) {
                try {
                    const swalWithBootstrapButtons = Swal.mixin({
                        customClass: {
                            confirmButton: 'btn btn-success',
                            cancelButton: 'btn btn-danger'
                        },
                        buttonsStyling: false
                    })

                    const swalText = (status == 1) ?
                        "Mengubah status data menjadi tidak menerima membuat sistem tidak akan menerima data dari alat. (Anda masih bisa mengubah settingan ini)" :
                        "Mengubah status data menjadi menerima membuat sistem dapat menerima data dari alat."

                    const result = await Swal.fire({
                        title: 'Apakah anda yakin?',
                        text: swalText,
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

                        let url = "{{ route('bitanic.device.change-status', ['id' => 'ID']) }}".replace('ID', id)
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

                        document.location.reload()
                    }

                    return 0
                } catch (error) {
                    console.log(error);
                }
            }

            const typeChange = e => {
                switch (e.value) {
                    case "tongkat":
                        document.querySelector('#data-input-select-type').innerHTML = `
                                  <option value="1">1</option>
                                  <option value="2">2</option>`
                        break;
                    case "controller":
                    default:
                        document.querySelector('#data-input-select-type').innerHTML = `
                                  <option value="1">1</option>
                                  <option value="2">2</option>
                                  <option value="3">3</option>`
                        break;
                }
            }

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

                const selectSearch = document.querySelectorAll('.select-search')
                selectSearch.forEach(eSelect => {
                    eSelect.addEventListener('change', e => {
                        document.getElementById('form-search').submit()
                    })
                });

                // const btnNewSpesifik = document.getElementById('btn-new-spesifik')
                // btnNewSpesifik.addEventListener("click", e => {
                //     e.preventDefault();
                //     let formSpesifik = document.getElementById('form-spesifik')

                //     formSpesifik.insertAdjacentHTML("beforeend", `<div class="row g-2 mb-3 custom-spesifikasi">
        //             <div class="col-5 mb-0">
        //                 <label class="form-label">Nama Spesifikasi</label>
        //                 <input
        //                     type="text"
        //                     class="form-control data-input-nama-spesifik"
        //                     name="nama_spesifikasi"
        //                     data-id=""
        //                 />
        //             </div>
        //             <div class="col-5 mb-0">
        //                 <label class="form-label">Isi Spesifikasi</label>
        //                 <input
        //                     type="text"
        //                     class="form-control data-input-value-spesifik"
        //                     name="value_spesifikasi"
        //                     data-id=""
        //                 />
        //             </div>
        //             <div class="col-2 mb-0 d-grid gap-2">
        //                 <button class="btn btn-danger btn-block mt-3 btn-delete-spesifik">X</button>
        //             </div>
        //         </div>`)

                //     deleteSpesifik()
                // })
            });

            const deleteSpesifik = () => {

                document.querySelectorAll('.btn-delete-spesifik').forEach(element => {
                    element.addEventListener("click", e => {

                        e.target.parentNode.parentNode.remove()
                    })
                });
            }

            const editSpesifikasi = (data) => {
                let formSpesifik = document.getElementById('form-spesifik')
                let element = ``

                for (let i = 0; i < data.specification.length; i++) {
                    const specification = data.specification[i];

                    element += `<div class="row g-2 mb-3 custom-spesifikasi">
                        <div class="col-5 mb-0">
                            <label class="form-label">Nama Spesifikasi</label>
                            <input
                                type="text"
                                class="form-control data-input-nama-spesifik"
                                name="nama_spesifikasi"
                                value="${specification.name}"
                                data-id="${specification.id}"
                            />
                        </div>
                        <div class="col-5 mb-0">
                            <label class="form-label">Isi Spesifikasi</label>
                            <input
                                type="text"
                                class="form-control data-input-value-spesifik"
                                name="value_spesifikasi"
                                value="${specification.value}"
                                data-id="${specification.id}"
                            />
                        </div>
                        <div class="col-2 mb-0 d-grid gap-2">
                            <button class="btn btn-danger btn-block mt-3 btn-delete-spesifik">X</button>
                        </div>
                    </div>`
                }

                formSpesifik.innerHTML = element
                deleteSpesifik()
            }

            const viewSpesifikasi = (device) => {
                document.getElementById('device-seri-perangkat').innerHTML = device.device_series
                document.getElementById('device-category').innerHTML = device.category.charAt(0).toUpperCase() + device
                    .category.slice(1)
                document.getElementById('device-type').innerHTML = device.type
                document.getElementById('device-versi').innerHTML = device.version
                document.getElementById('device-tgl-produksi').innerHTML = device.production_date
                document.getElementById('device-tgl-pembelian').innerHTML = device.purchase_date
                document.getElementById('device-tgl-diaktifkan').innerHTML = device.activate_date ?? '-'
                document.getElementById('device-status').innerHTML = device.status == 1 ?
                    '<span class="badge bg-label-primary">Aktif</span>' :
                    '<span class="badge bg-label-danger">Tidak Aktif</span>'
                document.getElementById('device-farmer').innerHTML = device.farmer ? device.farmer.full_name :
                    `<span class="badge bg-label-danger">Belum Memiliki Pemilik</span>`
                let element = device.specification.length > 0 ? `` : `<tr>
                                <td colspan="2" class="text-center">Tidak ada data</td>
                            </tr>`

                for (let i = 0; i < device.specification.length; i++) {
                    const specification = device.specification[i];

                    element += `<tr>
                                <td class="text-center">${specification.name}</td>
                                <td class="text-center">${specification.value}</td>
                            </tr>`
                }

                document.getElementById('view-spesifik').innerHTML = element
            }
        </script>
    @endpush
</x-app-layout>
