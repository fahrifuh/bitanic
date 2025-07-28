<x-app-layout>

    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
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
    </style>
    @endpush
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master / <a href="{{ route('bitanic.pest.index') }}">Data Hama</a> /</span> Buat data baru</h4>
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
                    <form action="{{ route('bitanic.pest.store') }}" method="POST"
                        id="form-product" enctype="multipart/form-data">
                        @csrf
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />

                        <div class="row">
                            <div class="col-12 col-md-3 mb-3">
                                <label for="" class="form-label">Preview Foto</label>
                                <img src="{{ asset('bitanic-landing/default-image.jpg') }}" alt="preview-img" class="preview-image img-thumbnail">
                            </div>
                            <div class="col-12 col-md-9 mb-3">
                                <label for="data-input-image" class="form-label">Foto</label>
                                <input class="form-control" type="file" id="data-input-image" name="image"
                                    accept="image/png, image/jpg, image/jpeg" aria-describedby="pictureHelp" />
                                <div id="pictureHelp" class="form-text">Format gambar JPG, JPEG, PNG. Maks.
                                    2MB</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="data-input-tanaman-id" class="form-label">Tanaman</label>
                                <select class="form-select" id="data-input-tanaman-id" name="crop_id"
                                    aria-label="Default select example">
                                    <option value="">-- Pilih Tanaman --</option>
                                    @forelse ($crops as $id => $crop)
                                        <option value="{{ $id }}">{{ $crop }}</option>
                                    @empty
                                        <option disabled>Tidak ada tanaman</option>
                                    @endforelse
                                </select>
                            </div>
                            <div class="col mb-3">
                                <label for="data-input-type" class="form-label">Nama Hama</label>
                                <input type="text" id="data-input-type" class="form-control" name="pest_type"
                                    value="{{ old('pest_type') }}" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="data-input-features" class="form-label">Ciri-ciri Hama</label>
                                <textarea class="form-control" id="data-input-features" name="features" rows="2">{{ old('features') }}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="data-input-symptomatic" class="form-label">Gejala pada Tanaman</label>
                                <textarea class="form-control" id="data-input-symptomatic" name="symptomatic" rows="2">{{ old('symptomatic') }}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="data-input-precautions" class="form-label">Pencegahan HPT</label>
                                <textarea class="form-control" id="data-input-precautions" name="precautions" rows="2">{{ old('precautions') }}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="data-input-countermeasures" class="form-label">Penanggulangan HPT</label>
                                <textarea class="form-control" id="data-input-countermeasures" name="countermeasures" rows="2">{{ old('countermeasures') }}</textarea>
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
        <script src="{{ asset('theme/js/ui-popover.js') }}"></script>
        <script src="{{ asset('js/select2.min.js') }}"></script>
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

                $('#data-input-tanaman-id').select2({
                    placeholder: 'Pilih Tanaman',
                    allowClear: true
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
