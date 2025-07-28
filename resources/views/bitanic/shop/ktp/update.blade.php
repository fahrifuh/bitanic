<x-app-layout>

    @push('styles')
    {{-- Cluster --}}
    <style>
    </style>
    @endpush
    {{-- Header --}}
    <x-slot name="header">
      <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master / Toko / Upload KTP</h4>
    </x-slot>
    {{-- End Header --}}

    <div class="row">
      <div class="col-md-12">
        <!-- Striped Rows -->
        <div class="card">
          <div class="card-body">
            <form action="{{ route('bitanic.shop.update-ktp') }}" method="POST" id="form-product" enctype="multipart/form-data">
              @csrf
              @method('PUT')
              <!-- Validation Errors -->
              <x-auth-validation-errors class="mb-4" :errors="$errors" />

              <div class="row mb-3">
                <div class="col">
                  <label for="ktpFile" class="form-label">Foto KTP</label>
                  <input class="form-control" type="file" name="ktp" id="ktpFile" accept="image/png, image/jpg, image/jpeg"
                    aria-describedby="ktpFileHelp"/>
                  <div id="ktpFileHelp" class="form-text">
                    Format: PNG,JPG,JPEG; Size: 10MB;
                  </div>
                </div>
              </div>
              <div class="row">
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

      document.addEventListener("DOMContentLoaded", () => {
        console.log("Hello World!");
      })
    </script>
    @endpush
  </x-app-layout>
