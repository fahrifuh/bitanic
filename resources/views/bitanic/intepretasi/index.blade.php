<x-app-layout>
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master /</span> Data Interpretasi</h4>
    </x-slot>
    {{-- End Header --}}

    <div class="row">
        <div class="col-md-12">
            <!-- Striped Rows -->
            <div class="card">
                <div class="table-responsive text-nowrap">
                  <table class="table table-striped table-bordered">
                    <thead>
                      <tr>
                        <th rowspan="2" class="align-middle">Unsur</th>
                        <th colspan="5" class="text-center">Parts Per Million (<span class="text-lowercase">ppm</span>)</th>
                        <th rowspan="2" class="align-middle text-center">Actions</th>
                      </tr>
                      <tr>
                        <th>Sangat Rendah</th>
                        <th>Rendah</th>
                        <th>Sedang</th>
                        <th>Tinggi</th>
                        <th>Sangat Tinggi</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($datas as $intepre)
                            @php
                                $rendah = explode("-", $intepre->level_interpretation->rendah);
                                $sedang = explode("-", $intepre->level_interpretation->sedang);
                                $tinggi = explode("-", $intepre->level_interpretation->tinggi);
                            @endphp
                            <tr>
                                <td>{{ $intepre->nama }}</td>
                                <td>{{ $intepre->level_interpretation->sangat_rendah ?? "--" }}</td>
                                <td>{{ (count($rendah) > 1) ? '≥'.$rendah[0]." s.d <".$rendah[1] : "<".$rendah[0] }}</td>
                                <td>&ge;{{ $sedang[0] }} - {{ $sedang[1] }}</td>
                                <td>{{ (count($tinggi) > 1) ? '≥'.$tinggi[0]." s.d <".$tinggi[1] : ">".$tinggi[0] }}</td>
                                <td>{{ $intepre->level_interpretation->sangat_tinggi ?? "--" }}</td>
                                <td>
                                    <button
                                          type="button"
                                          class="btn btn-warning btn-sm"
                                          id="btn-{{ Str::slug($intepre->nama) }}"
                                          data-bs-toggle="modal"
                                          data-bs-target="#modalForm"
                                          data-input-unsur="{{ $intepre->nama }}"
                                          data-input-sangat-rendah="{{ $intepre->level_interpretation->sangat_rendah }}"
                                          data-input-rendah-first="{{ $rendah[0] }}"
                                          data-input-rendah-second="{{ (count($rendah) > 1) ? $rendah[1] : null }}"
                                          data-input-sedang-first="{{ $sedang[0] }}"
                                          data-input-sedang-second="{{ $sedang[1] }}"
                                          data-input-tinggi-first="{{ $tinggi[0] }}"
                                          data-input-tinggi-second="{{ (count($tinggi) > 1) ? $tinggi[1] : null }}"
                                          data-input-sangat-tinggi="{{ $intepre->level_interpretation->sangat_tinggi }}"
                                          data-input-id="{{ $intepre->id }}"
                                        >
                                          Edit
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


        <div class="col-md-9 mt-3">
            <!-- Striped Rows -->
            <div class="card">
                <div class="table-responsive text-nowrap">
                  <table class="table table-striped table-bordered">
                    <thead>
                      <tr>
                        <th class="align-middle">Unsur</th>
                        <th class="text-center">Parts Per Million (<span class="text-lowercase">ppm</span>)</th>
                        <th class="align-middle">Status</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($datas as $intepre)

                            <tr>
                                <td>{{ $intepre->nama }}</td>
                                <td><input class="form-control input-ppm" type="number" step=".1" data-unsur="{{ Str::slug($intepre->nama) }}" /></td>
                                <td></td>
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

    @include('bitanic.intepretasi._modal-form')

    @push('scripts')
    <script src="{{ asset('js/app.js') }}"></script>
        <script>
            function changeStatus(unsur, ppm) {
                const intepretasi = document.getElementById('btn-' + unsur)
                let status = '-'

                if (intepretasi.getAttribute('data-input-sangat-rendah') && ppm <= parseFloat(intepretasi.getAttribute('data-input-sangat-rendah'))) {
                    status = 'Sangat Rendah'
                } else if(!intepretasi.getAttribute('data-input-sangat-rendah') && ppm < parseFloat(intepretasi.getAttribute('data-input-rendah-first'))) {
                    console.log('Rendah');
                    status = 'Rendah'
                } else if (
                    intepretasi.getAttribute('data-input-sangat-rendah')
                    && ppm >= parseFloat(intepretasi.getAttribute('data-input-rendah-first'))
                    && ppm < parseFloat(intepretasi.getAttribute('data-input-rendah-second'))
                ) {
                    status = 'Rendah'

                } else if (
                    ppm >= parseFloat(intepretasi.getAttribute('data-input-sedang-first')) &&
                    ppm <= parseFloat(intepretasi.getAttribute('data-input-sedang-second'))
                ) {
                    status = 'Sedang'

                } else if(!intepretasi.getAttribute('data-input-sangat-tinggi') && ppm > parseFloat(intepretasi.getAttribute('data-input-tinggi-first'))) {
                    status = 'Tinggi'
                } else if (
                    intepretasi.getAttribute('data-input-sangat-tinggi')
                    && ppm >= parseFloat(intepretasi.getAttribute('data-input-tinggi-first'))
                    && ppm < parseFloat(intepretasi.getAttribute('data-input-tinggi-second'))
                ) {
                    status = 'Tinggi'

                } else if (intepretasi.getAttribute('data-input-sangat-tinggi') && ppm >= parseFloat(intepretasi.getAttribute('data-input-sangat-tinggi'))) {
                    status = 'Sangat Tinggi'

                }

                return status
            }

            window.onload = function() {
                console.log('Hello world');

                const inputElements = document.querySelectorAll('.input-ppm')

                inputElements.forEach(input => {
                    input.addEventListener('change', e => {
                        e.preventDefault()

                        e.target.parentElement.parentElement.children[2].textContent = changeStatus(e.target.dataset.unsur, e.target.value)
                    })

                    input.addEventListener('keyup', e => {
                        e.preventDefault()

                        e.target.parentElement.parentElement.children[2].textContent = changeStatus(e.target.dataset.unsur, e.target.value)
                    })
                });
            }

            const myModal = new bootstrap.Modal(document.getElementById("modalForm"), {});
            const modal = document.getElementById('modalForm')
            modal.addEventListener('show.bs.modal', function (event) {
                // Button that triggered the modal
                const button = event.relatedTarget
                // Extract info from data-bs-* attributes
                // const recipient = button.getAttribute('data-bs-whatever')
                const modalTitle = modal.querySelector('.modal-title')

                document.getElementById('symbol-rendah-first').innerHTML = " &lt; ";
                document.getElementById('symbol-rendah-second').innerHTML = " - ";

                document.getElementById('data-input-rendah-first').removeAttribute('readonly')
                document.getElementById('data-input-rendah-second').setAttribute('readonly', 'readonly')

                document.getElementById('symbol-tinggi-first').innerHTML = " &gt; ";
                document.getElementById('symbol-tinggi-second').innerHTML = " - ";

                for (let index = 0; index < button.attributes.length; index++) {
                    if (button.attributes[index].nodeName.includes('data-input')) {
                        document.getElementById(button.attributes[index].nodeName).value = button.attributes[index].nodeValue

                        if (button.attributes[index].nodeName == 'data-input-sangat-rendah' && button.attributes[index].nodeValue) {
                            document.getElementById('symbol-rendah-first').innerHTML = " &ge; ";
                            document.getElementById('symbol-rendah-second').innerHTML = " &lt; ";

                            document.getElementById('data-input-rendah-second').removeAttribute('readonly')
                            document.getElementById('data-input-rendah-first').setAttribute('readonly', 'readonly')
                        }

                        if (button.attributes[index].nodeName == 'data-input-tinggi-second' && button.attributes[index].nodeValue) {
                            document.getElementById('symbol-tinggi-first').innerHTML = " &ge; ";
                            document.getElementById('symbol-tinggi-second').innerHTML = " &lt; ";
                        }

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

                console.log(document.getElementById('data-input-sangat-rendah').value);

                formData.append("_method", "PUT")
                if (document.getElementById('data-input-sangat-rendah').value) {
                    formData.append("sangat_rendah", document.getElementById('data-input-sangat-rendah').value)

                    formData.append("rendah_second", document.getElementById('data-input-rendah-second').value)
                } else {
                    formData.append("rendah_first", document.getElementById('data-input-rendah-first').value)
                }

                formData.append("sedang_second", document.getElementById('data-input-sedang-second').value)

                if (document.getElementById('data-input-tinggi-second').value) {
                    formData.append("tinggi_second", document.getElementById('data-input-tinggi-second').value)
                }

                url = "{{ route('bitanic.interpretation.update', 'ID') }}"

                axios.post(url.replace('ID', document.getElementById('data-input-id').value), formData)
                    .then((response) => {

                        window.location.reload();

                        myModal.toggle()

                        // Remove loading indication
                        submitButton.removeAttribute('data-kt-indicator');

                        // Enable button
                        submitButton.disabled = false;
                    })
                    .catch((error) => {
                        let errorMessage = ""

                        if (error.hasOwnProperty('response')) {
                            if (error.response.status == 422) {
                                errorMessage = 'Data yang dikirim tidak sesuai'
                            } else if (error.response.status == 400) {
                                let element = ``
                                for (const key in error.response.data.messages) {
                                    if (Object.hasOwnProperty.call(error.response.data.messages, key)) {
                                        error.response.data.messages[key].forEach(message => {
                                            element += `<li>${message}</li>`;
                                        });
                                    }
                                }

                                errorMessage = `<ul>${element}</ul>`
                            } else {
                                errorMessage = error
                            }
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
                        submitButton.removeAttribute('data-kt-indicator');

                        myModal.toggle()

                        // Enable button
                        submitButton.disabled = false;
                    });
            });

            function inputSangatRendah() {
                document.getElementById('data-input-sangat-rendah').addEventListener("keyup", function(e) {
                    if (this.value) {
                        document.getElementById('symbol-rendah-first').innerHTML = " &ge; ";
                        document.getElementById('symbol-rendah-second').innerHTML = " &lt; ";

                        document.getElementById('data-input-rendah-second').removeAttribute('disabled')

                        document.getElementById('data-input-rendah-first').setAttribute('disabled', 'disabled')
                        document.getElementById('data-input-rendah-first').value = this.value
                    } else {
                        document.getElementById('symbol-rendah-first').innerHTML = " &lt; ";
                        document.getElementById('symbol-rendah-second').innerHTML = " - ";

                        document.getElementById('data-input-rendah-second').setAttribute('disabled', 'disabled')
                        document.getElementById('data-input-rendah-second').value = null;
                        document.getElementById('data-input-sedang-first').value = null;

                        document.getElementById('data-input-rendah-first').removeAttribute('disabled')
                        document.getElementById('data-input-rendah-first').value = this.value
                    }
                });

                document.getElementById('data-input-sangat-rendah').addEventListener("change", function(e) {
                    if (this.value) {
                        document.getElementById('symbol-rendah-first').innerHTML = " &ge; ";
                        document.getElementById('symbol-rendah-second').innerHTML = " &lt; ";

                        document.getElementById('data-input-rendah-second').removeAttribute('disabled')

                        document.getElementById('data-input-rendah-first').setAttribute('disabled', 'disabled')
                        document.getElementById('data-input-rendah-first').value = this.value
                    }
                });
            }

            function inputRendahFirst() {
                document.getElementById('data-input-rendah-first').addEventListener("keyup", function(e) {
                    if (this.value) {
                        document.getElementById('data-input-sedang-first').setAttribute('disabled', 'disabled')
                        document.getElementById('data-input-sedang-first').value = this.value
                    } else {
                        document.getElementById('data-input-sedang-first').value = this.value
                    }
                });

                document.getElementById('data-input-rendah-first').addEventListener("change", function(e) {
                    if (this.value) {
                        document.getElementById('data-input-sedang-first').value = this.value
                    }
                });
            }

            function inputRendahSecond() {
                document.getElementById('data-input-rendah-second').addEventListener("keyup", function(e) {
                    if (this.value) {
                        document.getElementById('data-input-sedang-first').setAttribute('disabled', 'disabled')
                        document.getElementById('data-input-sedang-first').value = this.value
                    } else {
                        document.getElementById('data-input-sedang-first').removeAttribute('disabled')
                        document.getElementById('data-input-sedang-first').value = this.value
                    }
                });

                document.getElementById('data-input-rendah-second').addEventListener("change", function(e) {
                    if (this.value) {
                        document.getElementById('data-input-sedang-first').setAttribute('disabled', 'disabled')
                        document.getElementById('data-input-sedang-first').value = this.value
                    }
                });
            }

            function inputSedangSecond() {
                document.getElementById('data-input-sedang-second').addEventListener("keyup", function(e) {
                    if (this.value) {
                        document.getElementById('data-input-tinggi-first').setAttribute('disabled', 'disabled')
                        document.getElementById('data-input-tinggi-first').value = this.value
                    } else {
                        document.getElementById('data-input-tinggi-first').value = this.value
                    }
                });

                document.getElementById('data-input-sedang-second').addEventListener("change", function(e) {
                    if (this.value) {
                        document.getElementById('data-input-tinggi-first').value = this.value
                    }
                });
            }

            function inputTinggiSecond() {
                document.getElementById('data-input-tinggi-second').addEventListener("keyup", function(e) {
                    if (this.value) {
                        document.getElementById('symbol-tinggi-first').innerHTML = " &ge; ";
                        document.getElementById('symbol-tinggi-second').innerHTML = " &lt; ";

                        document.getElementById('data-input-sangat-tinggi').setAttribute('disabled', 'disabled')
                        document.getElementById('data-input-sangat-tinggi').value = this.value
                    } else {
                        document.getElementById('symbol-tinggi-first').innerHTML = " &gt; ";
                        document.getElementById('symbol-tinggi-second').innerHTML = " - ";

                        document.getElementById('data-input-sangat-tinggi').value = this.value
                    }
                });

                document.getElementById('data-input-tinggi-second').addEventListener("change", function(e) {
                    if (this.value) {
                        document.getElementById('data-input-sangat-tinggi').setAttribute('disabled', 'disabled')
                        document.getElementById('data-input-sangat-tinggi').value = this.value
                    }
                });
            }

            inputSangatRendah()
            inputRendahFirst()
            inputRendahSecond()
            inputSedangSecond()
            inputTinggiSecond()
        </script>
    @endpush
</x-app-layout>
