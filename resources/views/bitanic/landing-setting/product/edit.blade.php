<x-app-layout>

    @push('styles')
        <style>
            .preview-image {
                width: 100%;
                /* Adjust the width as desired */
                object-fit: cover;
                aspect-ratio: 16/9;
                border: 1px solid #9f999975;
                border-radius: 16px;
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
                    <a href="javascript:void(0);">Pengaturan</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="javascript:void(0);">Halaman Utama</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('bitanic.product.index') }}">Produk</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('bitanic.product.show', $landingProduct->id) }}">{{ $landingProduct->title }}</a>
                </li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </x-slot>
    {{-- End Header --}}

    @if (session()->has('success'))
        <x-alert-message class="alert-success">{{ session()->get('success') }}</x-alert-message>
    @endif

    <div class="row d-flex justify-content-center">
        <div class="col-12 col-md-6">
            <!-- Striped Rows -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('bitanic.product.update', $landingProduct->id) }}" method="POST" id="form-product"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />

                        <div class="row">
                            <div class="col-12">
                                <div class="row mb-3">
                                    <div class="col-12 mb-3">
                                        <label for="" class="form-label">Preview Foto</label>
                                        <img src="{{ asset($landingProduct->image ?? 'bitanic-landing/default-image.jpg') }}"
                                            alt="preview-img" class="preview-image img-thumbnail">
                                    </div>
                                    <div class="col-12">
                                        <label for="data-input-image" class="form-label">Foto</label>
                                        <input class="form-control" type="file" id="data-input-image" name="picture"
                                            accept="image/png, image/jpg, image/jpeg"
                                            aria-describedby="pictureHelp" />
                                        <div id="pictureHelp" class="form-text">Format gambar JPG, JPEG, PNG. Maks.
                                            2MB</div>
                                    </div>
                                </div>
                                <div class="row g-2">
                                    <div class="col-12">
                                        <label for="data-input-title" class="form-label">Nama Produk</label>
                                        <input type="text" id="data-input-title" class="form-control" name="title"
                                            value="{{ $landingProduct->title }}" required />
                                    </div>
                                    <div class="col-12">
                                        <label for="data-input-price" class="form-label">Harga</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" min="0" id="data-input-price"
                                                class="form-control" name="price" value="{{ $landingProduct->price }}" />
                                            <span class="input-group-text">.00</span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label for="data-input-description" class="form-label">Deskripsi</label>
                                        <textarea class="form-control" id="data-input-description" name="description" rows="5">{{ $landingProduct->description }}</textarea>
                                    </div>
                                    <div class="col-12">
                                        <label for="data-input-tags" class="form-label">Tag</label>
                                        <input type="text" id="data-input-tags" class="form-control" name="tags"
                                            value="{{ collect($landingProduct->tags)->join(', ') }}" placeholder="tag 1, tag 2, tag n..." aria-describedby="tagsHelp" required />
                                        <div id="tagsHelp" class="form-text">Gunakan koma "," untuk memisahkan tag</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
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

            const deleteElement = (e) => {
                e.preventDefault()

                if (e.target.classList.contains('btn-delete-element')) {
                    e.target.parentNode.parentNode.remove()
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

            })
        </script>
    @endpush
</x-app-layout>
