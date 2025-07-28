<x-app-layout>
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Menu /</span> Kebutuhan Dolomit</h4>
    </x-slot>
    {{-- End Header --}}

    <div class="row">
        @if (session()->has('success'))
            <div class="col-12">
                <div class="alert alert-success alert-dismissible" role="alert">
                    {{ session()->get('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif
        <div class="col-12 col-md-9 mt-3">
            <!-- Striped Rows -->
            <div class="card">
                <div class="row">
                    <div class="col-12">
                        <div class="float-start">
                            <h5 class="card-header"></h5>
                        </div>
                        <div class="float-end m-3">
                            <button
                                  type="button"
                                  class="btn btn-primary"
                                  data-bs-toggle="modal"
                                  data-bs-target="#modalSelisihKebutuhan"
                                  data-input-kebutuhan-dolomit=""
                                  data-input-selisih-ph=""
                                  data-input-id-selisih="add"
                                  title="Tambah Selisih Dolomit"
                                >
                                <i class="bx bx-plus"></i>&nbsp;Tambah
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive text-nowrap">
                  <table class="table table-striped table-bordered">
                    <thead>
                      <tr>
                        <th class="align-middle">Selisih PH</th>
                        <th class="text-center">Kebutuhan dolomit (<span class="text-lowercase">ton/ha</span>)</th>
                        <th class="align-middle text-center">Aksi</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($necessity_differences as $data)

                            <tr>
                                <td>{{ $data->selisih_ph }}</td>
                                <td>{{ $data->kebutuhan_dolomit }}</td>
                                <td>
                                    <button
                                          type="button"
                                          class="btn btn-warning btn-sm"
                                          data-bs-toggle="modal"
                                          data-bs-target="#modalSelisihKebutuhan"
                                          data-input-selisih-ph="{{ $data->selisih_ph }}"
                                          data-input-kebutuhan-dolomit="{{ $data->kebutuhan_dolomit }}"
                                          data-input-id-selisih="{{ $data->id }}"
                                          title="Edit Selisih Dolomit"
                                        >
                                        <i class="bx bx-edit"></i>
                                    </button>
                                    <button type="button" onclick="handleDeleteRows({{ $data }}, 'selisih')"
                                        class="btn btn-danger btn-sm" title="Hapus Selisih Dolomit">
                                        <i class="bx bx-trash event-none"></i>
                                    </button>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="17" class="text-center">Data kosong</td>
                            </tr>
                        @endforelse
                    </tbody>
                  </table>
                </div>
            </div>
            <!--/ Striped Rows -->
        </div>
    </div>

    @include('bitanic.dolomit._modal-selesih')

    @push('scripts')
        <script>
            const myModalSelisih = new bootstrap.Modal(document.getElementById("modalSelisihKebutuhan"), {});
            const modalSelisih = document.getElementById('modalSelisihKebutuhan')
            modalSelisih.addEventListener('show.bs.modal', function (event) {
                // Button that triggered the modal
                const button = event.relatedTarget
                // Extract info from data-bs-* attributes
                // const recipient = button.getAttribute('data-bs-whatever')
                const modalTitle = modalSelisih.querySelector('.modal-title')

                // console.log(button.attributes);

                for (let index = 0; index < button.attributes.length; index++) {
                    if (button.attributes[index].nodeName.includes('data-input')) {
                        document.getElementById(button.attributes[index].nodeName).value = button.attributes[index].nodeValue

                        if (button.attributes[index].nodeName == 'data-input-id-selisih') {
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
            const submitBtnSelisih = document.getElementById('submit-btn-selisih');
            submitBtnSelisih.addEventListener('click', async function(e) {
                // Prevent default button action
                e.preventDefault();

                // Show loading indication
                submitBtnSelisih.setAttribute('data-kt-indicator', 'on');

                // Disable button to avoid multiple click
                submitBtnSelisih.disabled = true;

                // Simulate form submission. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                let url, formSubmited;
                const editOrAdd = document.getElementById('data-input-id-selisih');
                const formData = new FormData();

                formData.append("selisih_ph", document.getElementById('data-input-selisih-ph').value)
                formData.append("kebutuhan_dolomit", document.getElementById('data-input-kebutuhan-dolomit').value)

                if (editOrAdd.value != 'add') {
                    formData.append("_method", "PUT")
                    url = "{{ route('bitanic.necessity-difference.update', 'ID') }}".replace('ID', document.getElementById('data-input-id-selisih').value)
                } else {
                    url = "{{ route('bitanic.necessity-difference.store') }}"
                }

                const settings = {
                    method: 'POST',
                    headers: {
                        'x-csrf-token': "{{ csrf_token() }}",
                        'Accept': "application/json",
                    },
                    body: formData
                }

                const [data, error] = await yourRequest(
                    url, settings
                )

                if (error) {
                    console.error(error)
                    let errorMessage = 'Terjadi kesalahan'

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

                    // Remove loading indication
                    submitBtnSelisih.removeAttribute('data-kt-indicator');

                    myModalSelisih.toggle()

                    // Enable button
                    submitBtnSelisih.disabled = false;

                    return 0
                }


                window.location.reload()
                // Remove loading indication
                submitBtnSelisih.removeAttribute('data-kt-indicator');

                myModalSelisih.toggle()

                // Enable button
                submitBtnSelisih.disabled = false;
            });

            async function handleDeleteRows(data, jenis) {
                const result = await Swal.fire({
                    text: "Menghapus data tidak dapat dibatalkan, dan semua data yang berhubungan akan hilang",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Ya, hapus!",
                    cancelButtonText: "Tidak, batalkan!",
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
                    let url = "{{ route('bitanic.necessity-difference.destroy', 'ID') }}"

                    let newUrl = url.replace('ID', data.id)
                    const formData = new FormData();
                    formData.append("_method", "DELETE")

                    const response = await fetch(newUrl, {
                        method: 'POST',
                        headers: {
                            // 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: formData
                    })

                    const result = await response.json()

                    console.log(result);
                    Swal.fire({
                        text: "Kamu berhasil menghapus sebuah data!.",
                        icon: "success",
                        buttonsStyling: false,
                    });
                    window.location.reload();

                    // axios.delete(newUrl)
                    // .then((response) => {
                    //     this.loading = false;
                    //     Swal.fire({
                    //         text: "Kamu berhasil menghapus sebuah data!.",
                    //         icon: "success",
                    //         buttonsStyling: false,
                    //         confirmButtonText: "Ok",
                    //         customClass: {
                    //             confirmButton: "btn fw-bold btn-primary",
                    //         }
                    //     }).then(function () {
                    //         // delete row data from server and re-draw datatable
                    //         window.location.reload();
                    //     });
                    // })
                    // .catch((error) => {
                    //     let errorMessage = error

                    //     if (error.hasOwnProperty('response')) {
                    //         if (error.response.status == 422) {
                    //             errorMessage = 'Data yang dikirim tidak sesuai'
                    //         }
                    //     }

                    //     Swal.fire({
                    //         text: errorMessage,
                    //         icon: "error",
                    //         buttonsStyling: false,
                    //         customClass: {
                    //             confirmButton: "btn btn-primary"
                    //         }
                    //     });

                    //     // Remove loading indication
                    //     // submitButton.removeAttribute('data-kt-indicator');

                    //     // Enable button
                    //     // submitButton.disabled = false;
                    // });
                }
            }
        </script>
    @endpush
</x-app-layout>
