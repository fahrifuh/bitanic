<x-app-layout>

    @push('styles')
        <style>
            .preview-image {
                width: 100%;
                /* Adjust the width as desired */
                object-fit: cover;
                aspect-ratio: 3/1;
                border: 1px solid #9f999975;
            }

            .event-none {
                pointer-events: none;
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
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master / <a
                    href="{{ route('bitanic.bank.index') }}">Bank</a> / <a href="{{ route('bitanic.bank.show', $bank->id) }}">{{ $bank->name }}</a> /</span> Edit bank {{ $bank->name }}</h4>
    </x-slot>
    {{-- End Header --}}

    @if (session()->has('success'))
        <x-alert-message class="alert-success">{{ session()->get('success') }}</x-alert-message>
    @endif

    <div class="row">
        <div class="col-md-12">
            <!-- Striped Rows -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('bitanic.bank.update', $bank->id) }}" method="POST" id="form-product"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />

                        <div class="row g-2">
                            <div class="col-12 col-md-3 mb-3">
                                <label for="" class="form-label">Preview Foto</label>
                                <img src="{{ asset($bank->picture ?? 'bitanic-landing/default-image.jpg') }}" alt="preview-img"
                                    class="preview-image img-thumbnail">
                            </div>
                            <div class="col-12 col-md-9 mb-3">
                                <label for="data-input-image" class="form-label">Foto</label>
                                <input class="form-control" type="file" id="data-input-image" name="picture"
                                    accept="image/png, image/jpg, image/jpeg" aria-describedby="pictureHelp" />
                                <div id="pictureHelp" class="form-text">Format gambar JPG, JPEG, PNG. Maks.
                                    2MB</div>
                            </div>
                        </div>

                        <div class="row g-2">
                            <div class="col mb-3">
                                <label for="data-input-name" class="form-label">Nama</label>
                                <input type="text" id="data-input-name" class="form-control" name="name"
                                    value="{{ $bank->name }}" required />
                            </div>
                            <div class="col mb-3">
                                <label for="data-input-code" class="form-label">Kode Bank</label>
                                <input type="text" id="data-input-code" class="form-control" name="code"
                                    value="{{ $bank->code }}" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="data-input-description" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="data-input-description" name="description" rows="5"
                                    >{{ $bank->description }}</textarea>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12 mb-2">
                                <button class="btn btn-info" id="btn-new-fees">Tambah Biaya</button>
                            </div>
                            <div class="col-md-12" id="form-fees">
                                @if ($bank->fees)
                                    @foreach ($bank->fees as $key => $fee)
                                        <div class="d-flex flex-wrap gap-2">
                                            <div class="flex-fill">
                                                <label class="form-label">Tipe</label>
                                                <select
                                                    class="form-select data-input-fees-type"
                                                    name="fees[{{ $key }}][type]"
                                                    data-id="{{ $key }}"
                                                >
                                                    <option value="0" @if($fee['type'] == 0) selected @endif>Rupiah</option>
                                                    <option value="1" @if($fee['type'] == 1) selected @endif>Persentase</option>
                                                </select>
                                            </div>
                                            <div class="flex-fill">
                                                <label class="form-label">Biaya</label>
                                                <div class="input-group">
                                                    @switch($fee['type'])
                                                        @case(0)
                                                            <span class="input-group-text" id="basic-addon13">Rp</span>
                                                            <input
                                                                type="number" min="0" value="{{ $fee['fee'] }}"
                                                                class="form-control data-input-fees-amount"
                                                                name="fees[{{ $key }}][fee]"
                                                            />
                                                            @break
                                                        @case(1)
                                                            <input
                                                                type="number" step=".1"
                                                                min="0" max="100" value="{{ $fee['fee'] }}"
                                                                class="form-control data-input-fees-amount"
                                                                name="fees[{{ $key }}][fee]"
                                                            />
                                                            <span class="input-group-text" id="basic-addon13">%</span>
                                                            @break

                                                        @default

                                                    @endswitch
                                                </div>
                                            </div>
                                            <div class="flex-shrink-1 align-self-end">
                                                <button class="btn btn-icon btn-danger btn-delete-element" title="Hapus Biaya"><i class="bx bx-trash event-none"></i></button>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <button type="submit" class="btn btn-primary float-end" id="submit-btn">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!--/ Striped Rows -->
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('theme/js/ui-popover.js') }}"></script>
        <script>
            function eventFile(input) {
                // Validate
                if (input.files && input.files[0]) {
                    let fileSize = input.files[0].size / 1024 / 1024; //MB Format
                    let fileType = input.files[0].type;

                    // validate size
                    if (fileSize > 10) {
                        showAlert('Ukuran File tidak boleh lebih dari 2mb !');
                        input.value = '';
                        return false;
                    }

                    // validate type
                    if (["image/jpeg", "image/jpg", "image/png"].indexOf(fileType) < 0) {
                        showAlert('Format File tidak valid !');
                        input.value = '';
                        return false;
                    }

                    let reader = new FileReader();

                    reader.onload = function(e) {
                        document.querySelector('.preview-image').setAttribute('src', e.target.result)
                    }

                    reader.readAsDataURL(input.files[0]); // convert to base64 string
                }
            }

            const newFees = (formElement, feesCount = 0) => {
                formElement.insertAdjacentHTML("beforeend", `<div class="row g-2 mb-3 custom-fees">
                    <div class="col-5 mb-0">
                        <label class="form-label">Tipe</label>
                        <select
                            class="form-select data-input-fees-type"
                            name="fees[${feesCount}][type]"
                            data-id="${feesCount}"
                        >
                            <option value="0">Rupiah</option>
                            <option value="1">Persentase</option>
                        </select>
                    </div>
                    <div class="col-5 mb-0">
                        <label class="form-label">Biaya</label>
                        <div class="input-group">
                            <span class="input-group-text" id="basic-addon13">Rp</span>
                            <input
                                type="number"
                                class="form-control data-input-fees-amount"
                                name="fees[${feesCount}][fee]"
                            />
                        </div>
                    </div>
                    <div class="col-2 mb-0 d-grid gap-2">
                        <button class="btn btn-danger btn-block mt-3 btn-delete-element">Hapus</button>
                    </div>
                </div>`)

                return feesCount + 1
            }

            const deleteElement = (e) => {
                e.preventDefault()

                if (e.target.classList.contains('btn-delete-element')) {
                    e.target.parentNode.parentNode.remove()
                }
            }

            const changeFeesType = (e) => {
                e.preventDefault()

                if (e.target.classList.contains('data-input-fees-type')) {
                    switch (e.target.value) {
                        case '0':
                            e.target.parentNode.nextElementSibling.children[1].innerHTML = `
                                <span class="input-group-text" id="basic-addon13">Rp</span>
                                <input
                                    type="number"
                                    min="0"
                                    class="form-control data-input-fees-amount"
                                    name="fees[${e.target.dataset.id}][fee]"
                                />
                            `
                            break;
                        case '1':
                            e.target.parentNode.nextElementSibling.children[1].innerHTML = `
                                <input
                                    type="number"
                                    step=".1"
                                    min="0"
                                    max="100"
                                    class="form-control data-input-fees-amount"
                                    name="fees[${e.target.dataset.id}][fee]"
                                />
                                <span class="input-group-text" id="basic-addon13">%</span>
                            `
                            break;

                        default:
                            break;
                    }
                }
            }

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");
                let feesCount = document.querySelectorAll('.custom-fees').length

                document.querySelector('#btn-new-fees').addEventListener('click', e => {
                    e.preventDefault()
                    feesCount = newFees(document.querySelector('#form-fees'), feesCount)
                })
                document.querySelector('#form-fees').addEventListener('click', deleteElement)
                document.querySelector('#form-fees').addEventListener('change', changeFeesType)

                // Handle File upload
                document.querySelector('#data-input-image').addEventListener('change', e => {
                    if (e.target.files.length == 0) {
                        // $('.profile').attr('src', defaultImage);
                    } else {
                        eventFile(e.target);
                    }
                })

            })
        </script>
    @endpush
</x-app-layout>
