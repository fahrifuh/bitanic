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

        .preview-delete {
        position: absolute;
        display: none;
        z-index: 99;
        right: 0;
        }

        i {
        pointer-events: none;
        }

        .previewBox:hover > .preview-delete {
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
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Setting / Halaman Utama /</span>&nbsp;<a href="{{ route('bitanic.about-our-startup-setting.index') }}">Tentang Startup</a> / Tambah Foto</h4>
    </x-slot>
    {{-- End Header --}}

    <div class="row">
        <div class="col-md-12">
            <!-- Striped Rows -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('bitanic.about-our-startup-setting.store-event-images') }}" method="POST" enctype="multipart/form-data">
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />

                        @csrf
                        <div class="row">
                            <div class="col">
                                <label for="fileInput" class="form-label">Foto Even</label>
                                <div id="defaultFormControlHelp" class="form-text">
                                Format: PNG,JPG,JPEG; Ratio 1:1;Size: 10MB;
                                </div>
                                <input class="d-none" type="file" id="fileInput" accept="image/png, image/jpg, image/jpeg" aria-describedby="defaultFormControlHelp" multiple />
                                <input class="d-none" type="file" id="destinationInput" name="picture[]" accept="image/png, image/jpg, image/jpeg" multiple />
                                <br />
                                <button type="button" id="selectImageButton" class="btn btn-icon btn-secondary"><i class="bx bx-plus"></i></button>
                            </div>
                        </div>
                        <div class="row my-3">
                            <div class="col-12 col-md-12 col-lg-12">
                                <div id="previewContainer">
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

    document.addEventListener("DOMContentLoaded", () => {
      console.log("Hello World!");

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
                document.getElementById('fileInput').files = list.files
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
