<x-app-layout>
@push('styles')
    <style>
        #previewContainer {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        }

        .previewImage {
        object-fit: cover;
        width: 100%;
        height: 100%;
        aspect-ratio: 3/2;
        }

        .previewBox {
            position: relative;
            width: calc(25% - 100px);
            /* Adjust the width as desired */
            aspect-ratio: 3/2;
            border: 1px solid #9f999975;
        }

        .selected {
            border: 1px solid #ff0000 !important;
            box-shadow: 0px 0px 5px 3px #ff0000;
        }

        .selected::after{
            content: "";
            width: 100%;
            height: 100%;
            background-color: #ff0000;
        }

        .preview-selected {
            position: absolute;
            display: none;
            z-index: 99;
            right: 0;
        }

        i {
        pointer-events: none;
        }

        .preview-selected.active {
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
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Setting / Halaman Utama /</span>&nbsp;<a href="{{ route('bitanic.about-our-startup-setting.index') }}">Tentang Startup</a> / Hapus Foto</h4>
    </x-slot>
    {{-- End Header --}}

    <div class="row">
        <div class="col-md-12">
            <!-- Striped Rows -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('bitanic.about-our-startup-setting.destroy-event-images') }}" method="POST" enctype="multipart/form-data">
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />

                        @csrf
                        @method('DELETE')
                        <div class="row">
                            <div class="col">
                                <label for="fileInput" class="form-label">Foto Event</label>
                                <div id="defaultFormControlHelp" class="form-text">
                                Format: PNG,JPG,JPEG; Ratio 1:1;Size: 10MB;
                                </div>
                                <input class="d-none" type="file" id="fileInput" accept="image/png, image/jpg, image/jpeg" aria-describedby="defaultFormControlHelp" multiple />
                                <input class="d-none" type="file" id="destinationInput" name="picture[]" accept="image/png, image/jpg, image/jpeg" multiple />
                                <br />
                            </div>
                        </div>
                        <div class="row my-3">
                            <div class="col-12 col-md-12 col-lg-12">
                                <div id="previewContainer">
                                    @foreach ($about_our_starup->event_images as $image)
                                        <div class="previewBox">
                                            <button type="button" class="btn btn-icon btn-sm btn-danger preview-selected"
                                                data-status="old" data-index="{{ $loop->index }}"><i class="bx bx-trash"></i>
                                            </button>
                                            <img src="{{ asset($image) }}" class="previewImage" data-img="{{ $image }}" />
                                            <input type="checkbox" name="images[]" class="d-none" value="{{ $image }}">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-danger float-end" id="submit-btn"><i class="bx bx-trash"></i>&nbsp;Hapus</button>
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

    document.addEventListener("DOMContentLoaded", () => {
      console.log("Hello World!");

      let previewContainer = document.getElementById('previewContainer')

      previewContainer.addEventListener('click', function(event) {
          let target = event.target;

          if (target.classList.contains('previewImage')) {
            console.dir(target)
            if (target.nextElementSibling.checked == true) {
                target.previousElementSibling.classList.remove('active')
                target.parentElement.classList.remove('selected')
                target.nextElementSibling.checked = false
            } else if (target.nextElementSibling.checked == false) {
                target.previousElementSibling.classList.add('active')
                target.parentElement.classList.add('selected')
                target.nextElementSibling.checked = true
            }
          }
      });
    })
  </script>
    @endpush
</x-app-layout>
