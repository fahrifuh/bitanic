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
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Mobile / <a
                    href="{{ route('bitanic.article.index') }}">Data Artikel</a> /</span> Buat data baru</h4>
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
                    <form action="{{ route('bitanic.article.store') }}" method="POST" id="form-product"
                        enctype="multipart/form-data">
                        @csrf
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />

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
                            <div class="col-12 col-md-6">
                                <label for="data-input-tanggal" class="form-label">Tanggal</label>
                                <input type="date" id="data-input-tanggal" class="form-control" name="date" />
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="data-input-tipe" class="form-label">Tipe</label>
                                <select class="form-select" id="data-input-tipe" name="type"
                                    aria-label="Default select example">
                                    <option value="sayuran">Sayuran</option>
                                    <option value="buah">Buah</option>
                                    <option value="umum">Umum</option>
                                    <option value="tentang_kami">Tentang Kami</option>
                                    <option value="visi_misi">Visi & Misi</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="data-input-judul" class="form-label">Judul Artikel</label>
                                <input type="text" id="data-input-judul" class="form-control" name="title" />
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="data-input-source" class="form-label">Source</label>
                                <input type="text" id="data-input-source" class="form-control" name="source" />
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="data-input-writer" class="form-label">Penulis</label>
                                <input type="text" name="writer" id="data-input-writer" class="form-control">
                            </div>
                            <div class="col-12">
                                <label for="data-input-description" class="form-label">Deskripsi </label>
                                <textarea id="editor" name="description" style="color: #292929;">
                                </textarea>
                            </div>
                        </div>
                        <div class="row mt-3">
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
