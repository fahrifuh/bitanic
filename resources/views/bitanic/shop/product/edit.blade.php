<x-app-layout>

    @push('styles')
        <style>
            #previewContainer {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
            }

            .preview-image {
                width: 100%;
                /* Adjust the width as desired */
                object-fit: cover;
                aspect-ratio: 1/1;
                border: 1px solid #9f999975;
            }

            .previewImage {
                object-fit: cover;
                width: 100%;
                height: 100%;
                aspect-ratio: 1/1;
            }

            .previewBox {
                position: relative;
                width: calc(25% - 100px);
                /* Adjust the width as desired */
                aspect-ratio: 1/1;
                border: 1px solid #9f999975;
            }

            .preview-delete {
                position: absolute;
                display: none;
                z-index: 99;
                right: 0;
            }

            i {
                pointer-events: none;
            }

            .previewBox:hover>.preview-delete {
                display: block;
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
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master / Toko
                {{ Auth::user()->farmer->shop->name }} / <a
                    href="{{ route('bitanic.shop.product-show', $product->id) }}">{{ $product->name }}</a> / </span>Edit
            Produk</h4>
    </x-slot>
    {{-- End Header --}}

    <div class="row">
        <div class="col-md-12">
            <!-- Striped Rows -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('bitanic.shop.product-update', $product->id) }}" method="POST"
                        id="form-product" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />

                        @if (session()->has('success'))
                            <x-alert-message class="alert-success">{{ session()->get('success') }}</x-alert-message>
                        @endif

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="data-input-crop-for-sale" class="form-label">Pilih Tanaman</label>
                                <select class="form-select" id="data-input-crop-for-sale" name="crop_for_sale_id"
                                    aria-label="Default select example">
                                    @foreach ($crop_for_sales as $crop_for_sale)
                                        <option value="{{ $crop_for_sale->id }}"
                                            @if ($crop_for_sale->id == $product->crop_for_sale_id) selected @endif>{{ $crop_for_sale->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-12 col-md-3 mb-3">
                                <label for="" class="form-label">Preview Foto</label>
                                <img src="{{ asset(isset($product->picture[0]) ? $product->picture[0] : $product->crop_for_sale->picture) }}" alt="preview-img"
                                    class="preview-image img-thumbnail">
                            </div>
                            <div class="col-12 col-md-9 mb-3">
                                <label for="data-input-image" class="form-label">Foto</label>
                                <input class="form-control" type="file" id="data-input-image" name="picture"
                                    accept="image/png, image/jpg, image/jpeg" aria-describedby="pictureHelp" />
                                <div id="pictureHelp" class="form-text">Format gambar JPG, JPEG, PNG. Maks.
                                    2MB</div>
                                <br>
                                <div class="col-12">
                                    <div class="bg-info text-white rounded p-3" role="alert">Foto dapat dikosongkan</div>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-12 col-md-6 mb-3">
                                <label for="data-input-name" class="form-label">Nama</label>
                                <input type="text" id="data-input-name" class="form-control" name="name"
                                    value="{{ $product->name }}" />
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="data-input-pemilik" class="form-label">Kategori</label>
                                <div class="d-flex align-items-center">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="category"
                                            id="form-input-buah" value="buah"
                                            @if ($product->category == 'buah') checked @endif />
                                        <label class="form-check-label" for="form-input-buah">Buah</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="category"
                                            id="form-input-sayur" value="sayur"
                                            @if ($product->category == 'sayur') checked @endif />
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
                                        name="price" value="{{ $product->price }}" />
                                    <span class="input-group-text">.00</span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="data-input-discount" class="form-label">Diskon <span class="text-info">*
                                        tidak wajib</span></label>
                                <div class="input-group">
                                    <input type="number" min="0" step=".01" max="100"
                                        id="data-input-discount" class="form-control" name="discount"
                                        value="{{ $product->discount }}" />
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="data-input-alamat" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="data-input-alamat" name="description" rows="2">{{ $product->description }}</textarea>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-12 col-md-6 mb-3">
                                <label for="data-input-stock" class="form-label">Stok</label>
                                <input type="number" min="0" id="data-input-stock" class="form-control"
                                    name="stock" value="{{ $product->stock }}" />
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="data-input-weight" class="form-label">Berat</label>
                                <div class="input-group">
                                    <input type="number" min="0" step=".01" id="data-input-weight"
                                        class="form-control" name="weight" value="{{ $product->weight }}" />
                                    <span class="input-group-text">Gram</span>
                                </div>
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
            let destinationInput = document.getElementById('destinationInput')
            let destinationFile
            let list = new DataTransfer();
            let oldImages = []
            const maxFiles = 5;

            function addImagePreview(file) {
                let reader = new FileReader();
                reader.onload = function(event) {
                    let box = document.createElement('div');
                    box.className = 'previewBox'

                    let btnDelete = document.createElement('button');
                    btnDelete.setAttribute('type', 'button')
                    btnDelete.setAttribute('data-status', 'new')
                    btnDelete.setAttribute('data-index', (list.files.length - 1))
                    btnDelete.classList.add('btn')
                    btnDelete.classList.add('btn-icon')
                    btnDelete.classList.add('btn-sm')
                    btnDelete.classList.add('btn-danger')
                    btnDelete.classList.add('preview-delete')
                    btnDelete.innerHTML = `<i class="bx bx-x"></i>`

                    let img = new Image();
                    img.src = event.target.result;
                    img.classList.add('previewImage');

                    let previewContainer = document.getElementById('previewContainer');

                    if (previewContainer.childElementCount >= maxFiles) {
                        previewContainer.removeChild(previewContainer.children[0]);
                    }

                    box.appendChild(btnDelete);
                    box.appendChild(img);
                    previewContainer.appendChild(box);

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

                let previewContainer = document.getElementById('previewContainer')

                previewContainer.addEventListener('click', function(event) {
                    let target = event.target;

                    if (target.classList.contains('preview-delete')) {
                        let previewItem = target.parentNode;
                        let index = Array.prototype.indexOf.call(previewContainer.children, previewItem);

                        if (target.dataset.status == 'new') {
                            list.items.remove(target.dataset.index)

                            document.getElementById('destinationInput').files = list.files
                        } else {
                            oldImages.push(target.dataset.index)
                        }

                        // Remove the preview item from the image preview
                        previewItem.remove();
                    }
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
