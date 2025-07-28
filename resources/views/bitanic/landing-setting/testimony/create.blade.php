<x-app-layout>

    @push('styles')
        <style>
            .star-rating {
                display: inline-block;
                font-size: 0;
                unicode-bidi: bidi-override;
                direction: rtl;
            }

            .star-rating input {
                display: none;
            }

            .star-rating label {
                display: inline-block;
                font-size: 40px;
                padding: 0 5px;
                cursor: pointer;
                color: #ccc;
                transition: color 0.2s;
            }

            .star-rating label i {
                font-size: 2rem;
            }

            .star-rating label:hover,
            .star-rating label:hover~label,
            .star-rating input:checked~label {
                color: #ffcc00;
            }

            .preview-image {
                width: 100%;
                /* Adjust the width as desired */
                object-fit: cover;
                aspect-ratio: 1/1;
                border: 1px solid #9f999975;
                border-radius: 100%;
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
                    <a href="{{ route('bitanic.testimony.index') }}">Testimoni</a>
                </li>
                <li class="breadcrumb-item active">Tambah</li>
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
                    <form action="{{ route('bitanic.testimony.store') }}" method="POST" id="form-product"
                        enctype="multipart/form-data">
                        @csrf
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />

                        <div class="row">
                            <div class="col-12">
                                <div class="row mb-3">
                                    <div class="col-12 col-sm-4 mb-3">
                                        <label for="" class="form-label">Preview Foto</label>
                                        <img src="{{ asset('bitanic-landing/default-image.jpg') }}" alt="preview-img"
                                            class="preview-image img-thumbnail">
                                    </div>
                                    <div class="col-12 col-sm-8">
                                        <label for="data-input-image" class="form-label">Foto</label>
                                        <input class="form-control" type="file" id="data-input-image" name="picture"
                                            accept="image/png, image/jpg, image/jpeg, image/svg"
                                            aria-describedby="pictureHelp" />
                                        <div id="pictureHelp" class="form-text">Format gambar JPG, JPEG, PNG, SVG. Maks.
                                            2MB</div>
                                    </div>
                                </div>
                                <div class="row g-2">
                                    <div class="col-12">
                                        <label for="data-input-name" class="form-label">Nama</label>
                                        <input type="text" id="data-input-name" class="form-control" name="name"
                                            value="{{ old('name') }}" required />
                                    </div>
                                    <div class="col-12">
                                        <label for="data-input-stars" class="form-label">Rating</label>
                                        <br>
                                        <div class="star-rating">
                                            <input type="radio" id="star5" name="rating" value="5">
                                            <label for="star5" title="5 stars"><i class='bx bxs-star'></i></label>

                                            <input type="radio" id="star4" name="rating" value="4">
                                            <label for="star4" title="4 stars"><i class='bx bxs-star'></i></label>

                                            <input type="radio" id="star3" name="rating" value="3">
                                            <label for="star3" title="3 stars"><i class='bx bxs-star'></i></label>

                                            <input type="radio" id="star2" name="rating" value="2">
                                            <label for="star2" title="2 stars"><i class='bx bxs-star'></i></label>

                                            <input type="radio" id="star1" name="rating" value="1">
                                            <label for="star1" title="1 star"><i class='bx bxs-star'></i></label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label for="data-input-comment" class="form-label">Komen</label>
                                        <textarea class="form-control" id="data-input-comment" name="comment" rows="5">{{ old('comment') }}</textarea>
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
