<x-app-layout>

    @push('styles')
        <style>
            .preview-image {
                width: 100%;
                /* Adjust the width as desired */
                object-fit: cover;
                aspect-ratio: 4/3;
                border: 1px solid #9f999975;
            }

            @media (max-width: 600px) {
                .preview-image {
                    width: calc(100% - 10px);
                }
            }

            .ck-editor__main {
                color: #000 !important;
            }
        </style>
    @endpush
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master / <a
                    href="{{ route('bitanic.bitanic-product.index') }}">Produk Bitanic</a> / <a href="{{ route('bitanic.bitanic-product.show', $bitanicProduct->id) }}">{{ $bitanicProduct->name }}</a> /</span> Edit</h4>
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
                    <form action="{{ route('bitanic.bitanic-product.update', $bitanicProduct->id) }}" method="POST" id="form-product"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />

                        <div class="row g-2">
                            <div class="col-12 col-md-3 mb-3">
                                <label for="" class="form-label">Preview Foto</label>
                                <img src="{{ asset($bitanicProduct->picture) }}" alt="preview-img" class="preview-image img-thumbnail">
                            </div>
                            <div class="col-12 col-md-9 mb-3">
                                <label for="data-input-image" class="form-label">Foto</label>
                                <input class="form-control" type="file" id="data-input-image" name="picture"
                                    accept="image/png, image/jpg, image/jpeg" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="data-input-name" class="form-label">Nama Produk</label>
                                <input type="text" id="data-input-name" class="form-control" name="name"
                                    value="{{ $bitanicProduct->name }}" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="data-input-levels" class="form-label">Versi</label>
                                <input type="text" id="data-input-levels" class="form-control" name="version"
                                    value="{{ $bitanicProduct->version }}" required />
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col mb-3">
                                <label for="data-input-category" class="form-label">Kategori Perangkat</label>
                                <select class="form-select" id="data-input-category" name="category"
                                    onchange="typeChange(this)">
                                    <option value="controller" @if($bitanicProduct->category == 'controller') selected @endif>Kontroller Bitanic Pro</option>
                                    <option value="tongkat" @if($bitanicProduct->category == 'tongkat') selected @endif>Tongkat/RSC</option>
                                </select>
                            </div>
                            <div class="col mb-3">
                                <label for="data-input-select-type" class="form-label">Tipe</label>
                                <select class="form-select" id="data-input-select-type" name="type">
                                    @if ($bitanicProduct->category == 'controller')
                                        <option value="1" @if($bitanicProduct->type == 1) selected @endif>1</option>
                                        <option value="2" @if($bitanicProduct->type == 2) selected @endif>2</option>
                                        <option value="3" @if($bitanicProduct->type == 3) selected @endif>3</option>
                                    @elseif ($bitanicProduct->category == 'tongkat')
                                        <option value="1" @if($bitanicProduct->type == 1) selected @endif>1</option>
                                        <option value="2" @if($bitanicProduct->type == 2) selected @endif>2</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3 g-2">
                            <div class="col-12">
                                <label for="data-input-weight" class="form-label">Berat </label>
                                <div class="input-group">
                                    <input type="number" min="0" class="form-control" name="weight"
                                        value="{{ $bitanicProduct->weight }}" autocomplete="weight" />
                                    <span class="input-group-text" id="basic-addon13">Gram</span>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-12 col-md-6 mb-3">
                                <label for="data-input-morning-time" class="form-label">Harga </label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon13">Rp</span>
                                    <input type="number" min="0" class="form-control" name="price"
                                        value="{{ $bitanicProduct->price }}" autocomplete="billing" required />
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="data-input-morning-time" class="form-label">Diskon </label>
                                <div class="input-group">
                                    <input type="number" min="0" max="100" class="form-control" name="discount"
                                        value="{{ $bitanicProduct->discount }}" autocomplete="billing" />
                                    <span class="input-group-text" id="basic-addon13">%</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="data-input-description" class="form-label">Deskripsi </label>
                                <textarea id="editor" name="description" style="color: #292929;">
                                    {!! $bitanicProduct->description !!}
                                </textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <button type="submit" class="btn btn-primary" id="submit-btn">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!--/ Striped Rows -->
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('ckeditor5-38.1.0/build/ckeditor.js') }}"></script>
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

                ClassicEditor
                    .create(document.querySelector('#editor'))
                    .catch(error => {
                        console.error(error);
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
