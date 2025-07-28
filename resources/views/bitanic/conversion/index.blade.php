<x-app-layout>
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master /</span> EXTRACTANT CONVERSION TO MEHLICH I
        </h4>
    </x-slot>
    {{-- End Header --}}

    <!-- Session Status -->
    <x-auth-session-status class="mb-3" :status="session('status')" />

    <!-- Validation Errors -->
    <x-auth-validation-errors class="mb-3 text-danger" :errors="$errors" />

    <div class="row">
        <div class="col-md-3">
            <!-- Striped Rows -->
            <div class="card">
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped table-bordered">
                        <thead class="bg-mechli-1">
                            <tr>
                                <th colspan="3" class="align-middle text-white text-center">MEHLICH I</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <tr>
                                <td class="text-center">P</td>
                                <td class="text-center" id="hasil-conversi-p"></td>
                                <td class="text-center">ppm</td>
                            </tr>
                            <tr>
                                <td class="text-center">K</td>
                                <td class="text-center" id="hasil-conversi-k"></td>
                                <td class="text-center">ppm</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!--/ Striped Rows -->
        </div>


        <div class="col-md-9">
            <!-- Striped Rows -->
            <div class="card">
                <div class="row">
                    <div class="col-md-12">
                        <div class="float-start">
                            <h5 class="card-header"></h5>
                        </div>
                        <div class="float-end m-3">
                            Hasil Analisis Tanah
                        </div>
                    </div>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered table-border-black">
                        <thead>
                            <tr class="bg-conversi">
                                <th class="align-middle text-center text-dark fw-bold">EAXTRACTANT</th>
                                <th class="align-middle text-center text-dark fw-bold">P FACTOR</th>
                                <th class="align-middle text-center text-dark fw-bold">K FACTOR</th>
                                <td colspan="2" class="align-middle text-center text-danger fw-bold bg-white">
                                    EXTRACTANT P</td>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0" id="tabel-hasil-lab">
                            <tr class="bg-conversi text-dark">
                                <td>{{ $datas['BRAY']->name }}</td>
                                <td>{{ $datas['BRAY']->p_factor }}</td>
                                <td>{{ $datas['BRAY']->k_factor }}</td>
                                <td class="p-0 bg-white">
                                    <select class="form-select step-two" disabled id="step-two-p"
                                        aria-label="Default select example">
                                        @foreach ($datas as $key => $val)
                                            <option value="{{ $val->p_factor }}" data-name="{{ $key }}">
                                                {{ $key }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="p-0 bg-white" style="width: 20%;"><input type="number" step="0.01"
                                        class="form-control" id="step-one-p" placeholder="0"
                                        data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="bottom"
                                        data-bs-html="true" title="<span>Masukan hasil lab P</span>"></td>
                            </tr>

                            <tr class="bg-conversi text-dark">
                                <td>{{ $datas['MORGAN']->name }}</td>
                                <td>{{ $datas['MORGAN']->p_factor }}</td>
                                <td>{{ $datas['MORGAN']->k_factor }}</td>
                                <td colspan="2" class="text-center text-danger bg-white fw-bold">EXTRACTANT K</td>
                            </tr>

                            <tr class="bg-conversi text-dark">
                                <td>{{ $datas['HCL']->name }}</td>
                                <td>{{ $datas['HCL']->p_factor }}</td>
                                <td>{{ $datas['HCL']->k_factor }}</td>
                                <td class="p-0 bg-white">
                                    <select class="form-select step-two" disabled id="step-two-k"
                                        aria-label="Default select example">
                                        @foreach ($datas as $key => $val)
                                            <option value="{{ $val->k_factor }}" data-name="{{ $key }}">
                                                {{ $key }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="p-0 bg-white" style="width: 20%;"><input type="number" step="0.01"
                                        class="form-control" id="step-one-k" placeholder="0"
                                        data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="bottom"
                                        data-bs-html="true" title="<span>Masukan hasil lab K</span>"></td>
                            </tr>

                            <tr class="bg-conversi text-dark">
                                <td>{{ $datas['NH4AC']->name }}</td>
                                <td>{{ $datas['NH4AC']->p_factor }}</td>
                                <td>{{ $datas['NH4AC']->k_factor }}</td>
                                <td colspan="2" class="bg-white"></td>
                            </tr>

                            <tr class="bg-conversi text-dark">
                                <td>{{ $datas['MECHLIH-3']->name }}</td>
                                <td>{{ $datas['MECHLIH-3']->p_factor }}</td>
                                <td>{{ $datas['MECHLIH-3']->k_factor }}</td>
                                <td colspan="2" class="bg-white"></td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
            <!--/ Striped Rows -->
        </div>

        <div class="col-md-12 mt-3">
            <span class="badge bg-primary text-wrap align-middle fst-italic text-white px-3 py-2"
                style="font-size: 12px !important;
                text-transform: none !important;">
                Developed by Prof. Dr. Ir. Anas Dinurrohman Susila, MSi, Plant Production Division, Dept. of Agronomy
                and Horticulture , Fac. of Agriculture , IPB University, November 2019
            </span>
        </div>
    </div>

    @push('scripts')
        <script>
            const getSelectedOption = (options) => {
                for (let i = 0; i < options.length; i++) {
                    if (options[i].selected == true) {
                        return options[i];
                    }
                }

                return false;
            };

            function conversion(pelarut, hasilLab, nilai, factor) {
                let y = 0

                if (!hasilLab) {
                    // alert("Harap masukan hasil Lab "+factor.toUpperCase())
                    let div = document.getElementById('toasts')
                    div.innerHTML = `
                        <div class="toast fade show bs-toast align-items-center bg-danger" role="alert" aria-live="assertive" aria-atomic="true">
                            <div class="d-flex">
                                <div class="toast-body text-white">
                                    Harap masukan hasil Lab ${factor.toUpperCase()}
                                </div>
                                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                            </div>
                        </div>`
                    document.getElementById('hasil-conversi-' + factor).innerHTML = ""
                    return false
                }

                switch (pelarut) {
                    case "BRAY":
                        y = (factor == 'p') ? (nilai * hasilLab) - 15.31 : (nilai * hasilLab) + 150.05
                        break;
                    case "MORGAN":
                        y = (factor == 'p') ? (nilai * hasilLab) + 30.85 : (nilai * hasilLab) + 162.55
                        break;
                    case "HCL":
                        y = (factor == 'p') ? (nilai * hasilLab) + 30.52 : (nilai * hasilLab) + 79.46
                        break;
                    case "NH4AC":
                        y = (factor == 'p') ? (nilai * hasilLab) - 8.08 : (nilai * hasilLab) + 92.37
                        break;
                    case "MECHLIH-3":
                        y = (factor == 'p') ? (nilai * hasilLab) : (nilai * hasilLab)
                        break;
                    default:
                        alert("Pelarut yang dipilih tidak dikenal!\nHarap pilih pelarut yang disediakan!")
                        return false;
                        break;
                }

                let size = y.toString().split(".")[1] ? y.toString().split(".")[1].length : 0;

                y = (size > 4) ? y.toFixed(4) : y.toFixed(size)

                document.getElementById('hasil-conversi-' + factor).innerHTML = y
            }

            const jenisFactor = ['p', 'k']


            jenisFactor.forEach(factor => {
                const stepOne = document.getElementById('step-one-' + factor)
                const stepTwo = document.getElementById('step-two-' + factor)
                let selectedOption, inputHasilLab, selectPelarut, nilaiFactorPelarut

                stepTwo.addEventListener("change", function(e) {
                    selectedOption = e.target.options[e.target.selectedIndex]
                    inputHasilLab = !stepOne.value ? false : parseFloat(stepOne.value.replace(",", "."))
                    selectPelarut = selectedOption.dataset.name
                    nilaiFactorPelarut = parseFloat(selectedOption.value)

                    conversion(selectPelarut, inputHasilLab, nilaiFactorPelarut, factor)
                })

                stepOne.addEventListener("keyup", function(e) {
                    selectedOption = getSelectedOption(document.querySelectorAll("#step-two-" + factor +
                        " option"))
                    inputHasilLab = !this.value ? false : parseFloat(this.value.replace(",", "."))
                    selectPelarut = selectedOption.dataset.name
                    nilaiFactorPelarut = parseFloat(selectedOption.value)

                    stepTwo.removeAttribute('disabled')

                    if (!this.value) {
                        stepTwo.setAttribute('disabled', 'disabled')
                    }

                    conversion(selectPelarut, inputHasilLab, nilaiFactorPelarut, factor)
                })

                stepOne.addEventListener("change", function(e) {
                    selectedOption = getSelectedOption(document.querySelectorAll("#step-two-" + factor +
                        " option"))
                    inputHasilLab = !this.value ? false : parseFloat(this.value.replace(",", "."))
                    selectPelarut = selectedOption.dataset.name
                    nilaiFactorPelarut = parseFloat(selectedOption.value)

                    conversion(selectPelarut, inputHasilLab, nilaiFactorPelarut, factor)
                })
            });
        </script>
    @endpush
</x-app-layout>
