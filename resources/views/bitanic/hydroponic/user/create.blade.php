<x-app-layout>

    @push('styles')
        <style>
            .preview-image {
                width: 100%;
                /* Adjust the width as desired */
                object-fit: cover;
                aspect-ratio: 1/1;
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
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb fs-4">
                <li class="breadcrumb-item">
                    <a href="javascript:void(0);">Hidroponik</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('bitanic.hydroponic.user.index') }}">User</a>
                </li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </nav>
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
                    <form action="{{ route('bitanic.hydroponic.user.store') }}" method="POST" id="form-product"
                        enctype="multipart/form-data">
                        @csrf
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="row g-2">
                                    <div class="col-12">
                                        <label for="data-input-name" class="form-label">Nama</label>
                                        <input type="text" id="data-input-name" class="form-control"
                                            name="name" value="{{ old('name') }}" required />
                                    </div>
                                    <div class="col-12">
                                        <label for="data-input-email" class="form-label">Email</label>
                                        <input type="email" id="data-input-email" class="form-control"
                                            name="email" value="{{ old('email') }}" required />
                                    </div>
                                    <div class="col-12">
                                        <label for="data-input-username" class="form-label">Username</label>
                                        <input type="text" id="data-input-username" class="form-control"
                                            name="username" value="{{ old('username') }}" required />
                                    </div>
                                    <div class="col-12">
                                        <label for="data-input-gender" class="form-label">Jenis Kelamin</label>
                                        <select class="form-select" id="data-input-gender" name="gender"
                                            aria-label="Default select example">
                                            @foreach ($userGenders as $userGender)
                                                <option value="{{ $userGender->value }}">{{ $userGender->getLabelText() }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label for="data-input-phone-number" class="form-label">Nomor HP</label>
                                        <div class="input-group">
                                            <span class="input-group-text" id="basic-addon13">+62</span>
                                            <input type="text" pattern="8[0-9]{8,}" class="form-control" id="data-input-phone-number" name="phone_number"
                                                placeholder="8xxxxxxxxx" aria-describedby="basic-addon13" />
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label for="data-input-address" class="form-label">Alamat</label>
                                        <textarea class="form-control" id="data-input-address"
                                            name="address" rows="5">{{ old('address') }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="row g-2">
                                    <div class="col-12 col-md-6">
                                        <label for="" class="form-label">Preview Foto</label>
                                        <img src="{{ asset('bitanic-landing/default-profile.png') }}" alt="preview-img" class="preview-image img-thumbnail">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="data-input-foto" class="form-label">Foto</label>
                                        <input class="form-control" type="file" id="data-input-foto" name="picture"
                                            accept="image/png, image/jpg, image/jpeg" aria-describedby="pictureHelp" />
                                        <div id="pictureHelp" class="form-text">Format gambar JPG, JPEG, PNG. Maks.
                                            2MB</div>
                                    </div>
                                    <div class="col-12">
                                        <label for="data-input-password" class="form-label">Password</label>
                                        <input type="password" id="data-input-password" class="form-control"
                                            name="password" value="{{ old('password') }}" required />
                                    </div>
                                    <div class="col-12">
                                        <label for="data-input-password-confirmation" class="form-label">Konfirmasi Password</label>
                                        <input type="password" id="data-input-password-confirmation" class="form-control"
                                            name="password_confirmation" value="{{ old('password_confirmation') }}" required />
                                    </div>
                                </div>
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
                formElement.insertAdjacentHTML("beforeend", `<div class="d-flex flex-wrap gap-2">
                    <div class="flex-fill">
                        <input
                            type="text"
                            class="form-control data-input-fees-amount"
                            name="requirements[]"
                        />
                    </div>
                    <div class="flex-shrink-1 align-self-end">
                        <button class="btn btn-icon btn-danger btn-delete-element" title="Hapus Biaya"><i class="bx bx-trash event-none"></i></button>
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

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

                // Handle File upload
                document.querySelector('#data-input-foto').addEventListener('change', e => {
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
