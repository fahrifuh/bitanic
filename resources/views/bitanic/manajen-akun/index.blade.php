<x-app-layout>
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master /</span> Data Hama</h4>
    </x-slot>
    {{-- End Header --}}

    <div class="row">
        <div class="col-md-12">
            <!-- Striped Rows -->
            <div class="card">
                <div class="row">
                    <div class="col-md-12">
                        <div class="float-start">
                            <h5 class="card-header">Striped rows</h5>
                        </div>
                        <div class="float-end m-3">
                            <button
                                  type="button"
                                  class="btn btn-primary"
                                  data-bs-toggle="modal"
                                  data-bs-target="#modalForm"
                                  data-input-nama-hama=""
                                  data-input-jenis-hama=""
                                  data-input-id="add"
                                >
                                  Tambah
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive text-nowrap">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>log name</th>
                        <th>Deskripsi</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach ($datas as $hama)

                            <tr>
                                <td>{{ $hama->nama }}</td>
                                <td>{{ $hama->jenis_hama }}</td>
                                <td>
                                    <button
                                          type="button"
                                          class="btn btn-info btn-sm"
                                          data-bs-toggle="modal"
                                          data-bs-target="#modalFoto"
                                          data-foto="{{ asset($hama->foto) }}"
                                          data-input-id="{{ $hama->id }}"
                                        >
                                          Foto
                                    </button>
                                    <button
                                          type="button"
                                          class="btn btn-warning btn-sm"
                                          data-bs-toggle="modal"
                                          data-bs-target="#modalForm"
                                          data-input-nama-hama="{{ $hama->nama }}"
                                          data-input-jenis-hama="{{ $hama->jenis_hama }}"
                                          data-input-id="{{ $hama->id }}"
                                        >
                                          Edit
                                    </button>
                                    <button type="button" onclick="handleDeleteRows({{ $hama }})" class="btn btn-danger btn-sm">Delete</button>
                                </td>
                            </tr>

                        @endforeach
                    </tbody>
                  </table>
                </div>
            </div>
            <!--/ Striped Rows -->
        </div>
    </div>

    @include('bitanic.hama._modal-form')
    @include('bitanic.hama._modal-foto')

    @push('scripts')
        <script>
            const myModal = new bootstrap.Modal(document.getElementById("modalForm"), {});
            const modal = document.getElementById('modalForm')
            modal.addEventListener('show.bs.modal', function (event) {
                // Button that triggered the modal
                const button = event.relatedTarget
                // Extract info from data-bs-* attributes
                // const recipient = button.getAttribute('data-bs-whatever')
                const modalTitle = modal.querySelector('.modal-title')

                for (let index = 0; index < button.attributes.length; index++) {
                    if (button.attributes[index].nodeName.includes('data-input')) {
                        document.getElementById(button.attributes[index].nodeName).value = button.attributes[index].nodeValue

                        if (button.attributes[index].nodeName == 'data-input-id') {
                            if (document.getElementById(button.attributes[index].nodeName).value != 'add') {
                                modalTitle.textContent = 'Edit'
                                // validator.validate()
                            } else {
                                modalTitle.textContent = 'Tambah'
                            }
                        }
                    }
                }

            })

            // Submit button handler
            const submitButton = document.getElementById('submit-btn');
            submitButton.addEventListener('click', function(e) {
                // Prevent default button action
                e.preventDefault();

                // Show loading indication
                submitButton.setAttribute('data-kt-indicator', 'on');

                // Disable button to avoid multiple click
                submitButton.disabled = true;

                // Simulate form submission. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                let url, formSubmited;
                const editOrAdd = document.getElementById('data-input-id');
                const formData = new FormData();

                formData.append("foto", document.getElementById(
                    'data-input-foto').files[0])
                formData.append("nama", document.getElementById('data-input-nama-hama').value)
                formData.append("jenis_hama", document.getElementById('data-input-jenis-hama').value)

                console.log(formData, document.getElementById(
                    'data-input-foto').files[0]);

                if (editOrAdd.value != 'add') {
                    url = "{{ route('bitanic.hama.update', 'ID') }}"
                    formSubmited = axios.post(url.replace('ID', document.getElementById(
                        'data-input-id').value), formData)
                } else {
                    url = "{{ route('bitanic.hama.store') }}"
                    formSubmited = axios.post(url, formData)
                }


                formSubmited.then((response) => {

                        window.location.reload();

                        // Remove loading indication
                        submitButton.removeAttribute('data-kt-indicator');

                        // Enable button
                        submitButton.disabled = false;
                    })
                    .catch((error) => {
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

                        // Remove loading indication
                        submitButton.removeAttribute('data-kt-indicator');

                        // Enable button
                        submitButton.disabled = false;
                    });
            });

            const myModalPrev = new bootstrap.Modal(document.getElementById("modalFoto"), {});
            const modalFoto = document.getElementById('modalFoto')
            modalFoto.addEventListener('show.bs.modal', function(event) {
                // Button that triggered the modal
                const button = event.relatedTarget
                // Extract info from data-bs-* attributes
                // const recipient = button.getAttribute('data-bs-whatever')
                const modalTitle = modalFoto.querySelector('.modal-title')
                modalTitle.textContent = 'Foto Hama'

                for (let index = 0; index < button.attributes.length; index++) {
                    if (button.attributes[index].nodeName.includes('data-foto')) {
                        document.getElementById('iframe').src = button.attributes[index].nodeValue
                    }
                }

            })

            function handleDeleteRows(data) {
                Swal.fire({
                    text: "Menghapus data tidak dapat dibatalkan, dan semua data yang berhubungan akan hilang",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Yes, delete!",
                    cancelButtonText: "No, cancel",
                    customClass: {
                        confirmButton: "btn fw-bold btn-danger",
                        cancelButton: "btn fw-bold btn-active-light-primary"
                    }
                }).then(function (result) {
                    if (result.value) {
                        Swal.fire({
                            html: '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span class=""> Loading...</span>',
                            showConfirmButton: false,
                            allowOutsideClick: false,
                        });
                        // Simulate delete request -- for demo purpose only
                        const url = "{{ route('bitanic.hama.destroy', 'ID') }}"
                        let newUrl = url.replace('ID', data.id)

                        axios.delete(newUrl)
                        .then((response) => {
                            this.loading = false;
                            Swal.fire({
                                text: "Kamu berhasil menghapus data " + data.nama + "!.",
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            }).then(function () {
                                // delete row data from server and re-draw datatable
                                window.location.reload();
                            });
                        })
                        .catch((error) => {
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

                            // Remove loading indication
                            // submitButton.removeAttribute('data-kt-indicator');

                            // Enable button
                            // submitButton.disabled = false;
                        });
                    }
                });

            }
        </script>
    @endpush
</x-app-layout>
