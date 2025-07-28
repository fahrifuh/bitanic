<x-app-layout>

  @push('styles')
  @endpush
  {{-- Header --}}
  <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Setting / <a href="{{ route('bitanic.contact-us-message.index') }}">Pesan dari Kontak Kami</a> / </span>Balas Pesan</h4>
  </x-slot>
  {{-- End Header --}}

  <div class="row">
    <div class="col-md-12">
      <!-- Striped Rows -->
      <div class="card">
        <div class="card-body">
          <form action="{{ route('bitanic.contact-us-message.store-message', $contactUsMessage->id) }}" method="POST">
            @csrf
            <!-- Validation Errors -->
            <x-auth-validation-errors class="mb-4" :errors="$errors" />

            <div class="row">
              <div class="col mb-3">
                <h5>Nama: {{ $contactUsMessage->name }}</h5>
                <h5>Email: {{ $contactUsMessage->email }}</h5>
              </div>
            </div>
            <div class="row">
              <div class="col mb-3">
                <div class="bg-info text-white p-3 rounded" role="alert">Maksimal 255 karakter</div>
              </div>
            </div>
            <div class="row">
              <div class="col mb-3">
                <label for="data-input-message" class="form-label">Pesan</label>
                <textarea class="form-control" id="data-input-message" name="message" rows="3" placeholder="Isi pesan...">{{ old('message') }}</textarea>
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
  @endpush
</x-app-layout>
