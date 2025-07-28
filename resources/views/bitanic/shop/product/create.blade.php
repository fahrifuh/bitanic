<x-app-layout>

    @push('styles')
        <style>
            #previewContainer {
                display: flex;
                flex-wrap: wrap;
            }
            .preview-image {
                width: 100%;
                /* Adjust the width as desired */
                object-fit: cover;
                aspect-ratio: 1/1;
                border: 1px solid #9f999975;
            }

            .previewImage {
                width: calc(25% - 100px);
                /* Adjust the width as desired */
                margin: 5px;
                object-fit: cover;
                aspect-ratio: 1/1;
                border: 1px solid #9f999975;
            }

            @media (max-width: 600px) {
                .previewImage {
                    width: calc(100% - 10px);
                }
            }
        </style>
    @endpush
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master / <a
                    href="{{ route('bitanic.shop.index') }}">Toko</a> / </span> Tambah Produk</h4>
    </x-slot>
    {{-- End Header --}}

    <div class="row">
        <div class="col-md-12">
            <!-- Striped Rows -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('bitanic.shop.product-store') }}" method="POST" id="form-product"
                        enctype="multipart/form-data">
                        @csrf
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />

                        @if (session()->has('success'))
                            <x-alert-message class="alert-success">{{ session()->get('success') }}</x-alert-message>
                        @endif

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="data-input-crop-for-sale" class="form-label">Pilih tanaman</label>
                                <select class="form-select" id="data-input-crop-for-sale" name="crop_for_sale_id"
                                    aria-label="Default select example">
                                    @foreach ($crop_for_sales as $crop_for_sale)
                                        <option value="{{ $crop_for_sale->id }}">{{ $crop_for_sale->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-12 col-md-3 mb-3">
                                <label for="" class="form-label">Preview Foto</label>
                                <img src="{{ asset('bitanic-landing/default-image.jpg') }}" alt="preview-img"
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
                            <div class="col-12 col-md-6 mb-3">
                                <label for="data-input-name" class="form-label">Nama</label>
                                <input type="text" id="data-input-name" class="form-control" name="name"
                                    value="{{ old('name') }}" />
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="data-input-pemilik" class="form-label">Kategori</label>
                                <div class="d-flex align-items-center">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="category"
                                            id="form-input-buah" value="buah" checked />
                                        <label class="form-check-label" for="form-input-buah">Buah</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="category"
                                            id="form-input-sayur" value="sayur" />
                                        <label class="form-check-label" for="form-input-sayur">Sayur</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-12 col-md-6 mb-3">
                                <label for="data-input-price" class="form-label">Harga</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" min="0" id="data-input-price" class="form-control"
                                        name="price" value="{{ old('price') }}" />
                                    <span class="input-group-text">.00</span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="data-input-discount" class="form-label">Diskon <span class="text-info">*
                                        tidak wajib</span></label>
                                <div class="input-group">
                                    <input type="number" min="0" step=".01" max="100"
                                        id="data-input-discount" class="form-control" name="discount"
                                        value="{{ old('discount') }}" />
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="data-input-alamat" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="data-input-alamat" name="description" rows="2">{{ old('description') }}</textarea>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-12 col-md-6 mb-3">
                                <label for="data-input-weight" class="form-label">Berat</label>
                                <div class="input-group">
                                    <input type="number" min="0" step=".01" id="data-input-weight"
                                        class="form-control" name="weight" value="{{ old('weight') }}" />
                                    <span class="input-group-text">Gram</span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="data-input-stock" class="form-label">Stok</label>
                                <input type="number" min="0" id="data-input-stock" class="form-control"
                                    name="stock" value="{{ old('stock') }}" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary float-end"
                                    id="submit-btn">Simpan</button>
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
            let sentFiles = {}
            let destinationInput = document.getElementById('destinationInput')
            let destinationFile
            let list = new DataTransfer();
            const maxFiles = 5;

            function addImagePreview(file) {
                let reader = new FileReader();
                reader.onload = function(event) {
                    let img = new Image();
                    img.src = event.target.result;
                    img.classList.add('previewImage');

                    let previewContainer = document.getElementById('previewContainer');
                    previewContainer.appendChild(img);

                    if (previewContainer.childElementCount >= maxFiles) {
                        previewContainer.removeChild(previewContainer.firstChild);
                    }
                };

                reader.readAsDataURL(file);
            }

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

                // Handle File upload
                document.querySelector('#data-input-image').addEventListener('change', e => {
                    if (e.target.files.length == 0) {
                        // $('.profile').attr('src', defaultImage);
                    } else {
                        eventFile(e.target);
                    }
                })

                document.getElementById('selectImageButton').addEventListener('click', function() {
                    let fileInput = document.getElementById('fileInput');
                    fileInput.click();
                });

                document.getElementById('fileInput').addEventListener('change', function(event) {
                    let files = event.target.files;

                    for (let i = 0; i < files.length; i++) {
                        const file = files[i];

                        list.items.add(file);

                        if (list.items.length > maxFiles) {
                            list.items.remove(0)
                        }

                        addImagePreview(file);
                    }

                    document.getElementById('destinationInput').files = list.files

                });
            })
        </script>
    @endpush
</x-app-layout>
