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
                    href="{{ route('bitanic.lite-user.index') }}">Petani Lite</a> / <a href="{{ route('bitanic.lite-user.show', $liteUser->id) }}">{{ $liteUser->name }}</a> /</span> Edit Profile</h4>
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
                    <form action="{{ route('bitanic.lite-user.update', $liteUser->id) }}" method="POST" id="form-product"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />

                        <div class="row g-2">
                            <div class="col-12 col-md-3 mb-3">
                                <label for="" class="form-label">Preview Foto</label>
                                <img src="{{ asset($liteUser->picture) }}" alt="preview-img"
                                    class="preview-image img-thumbnail">
                            </div>
                            <div class="col-12 col-md-9 mb-3">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="bg-info text-white rounded p-3" role="alert">Foto dapat dikosongkan</div>
                                    </div>
                                    <div class="col-12">
                                        <label for="data-input-image" class="form-label">Foto</label>
                                        <input class="form-control" type="file" id="data-input-image" name="picture"
                                            accept="image/png, image/jpg, image/jpeg" aria-describedby="pictureHelp" />
                                            <div id="pictureHelp" class="form-text">Format gambar JPG, JPEG, PNG. Maks.
                                                2MB</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-2">
                            <div class="col-12 col-md-6">
                                <div class="row g-2">
                                    <div class="col mb-3">
                                        <label for="data-input-name" class="form-label">Nama</label>
                                        <input type="text" id="data-input-name" class="form-control" name="name"
                                            value="{{ $liteUser->name }}" required />
                                    </div>
                                    <div class="col mb-3">
                                        <label for="data-input-tanaman-id" class="form-label">Jenis Kelamin</label>
                                        <select class="form-select" id="data-input-tanaman-id" name="gender"
                                            aria-label="Default select example">
                                            <option value="male" @if('male' == $liteUser->gender) selected @endif>{{ __('Laki - laki') }}</option>
                                            <option value="female" @if('female' == $liteUser->gender) selected @endif>{{ __('Perempuan') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-2">
                                    <div class="col mb-3">
                                        <label for="data-input-phone-number" class="form-label">Nomor Handphone</label>
                                        <div class="input-group">
                                            <span class="input-group-text" id="basic-addon13">+62</span>
                                            <input type="text" class="form-control" id="data-input-phone-number"
                                                name="phone_number" value="{{ substr($liteUser->phone_number, 2) }}"
                                                placeholder="8XXXXX" aria-label="0" aria-describedby="basic-addon13" />
                                        </div>
                                    </div>
                                    <div class="col mb-3">
                                        <label for="data-input-nik" class="form-label">NIK</label>
                                        <input type="text" class="form-control" id="data-input-nik" name="nik"
                                            value="{{ $liteUser->nik }}" placeholder="0" aria-label="0"
                                            aria-describedby="basic-addon13" />
                                    </div>
                                </div>
                                <div class="row g-2">
                                    <div class="col mb-3">
                                        <label for="data-input-birth-date" class="form-label">Tanggal Lahir</label>
                                        <input type="date" class="form-control" id="data-input-birth-date"
                                            name="birth_date" value="{{ $liteUser->birth_date }}" placeholder="0"
                                            aria-label="0" aria-describedby="basic-addon13" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="row">
                                    <div class="col mb-3">
                                        <label for="data-input-address" class="form-label">Alamat</label>
                                        <textarea class="form-control" id="data-input-address" name="address" rows="5" placeholder="Jl. XXX"
                                            required>{{ $liteUser->address }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
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

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

                document.getElementById("data-input-nik").addEventListener("keypress", function(event) {
                    const key = event.keyCode;
                    // Only allow numbers (key codes 48 to 57)
                    if (key < 48 || key > 57) {
                        event.preventDefault();
                    }
                });

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
