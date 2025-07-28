<x-app-layout>
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Setting / Halaman Utama /</span> Setting Produk</h4>
    </x-slot>
    {{-- End Header --}}

    <div class="row">
        <div class="col-md-12">
            <!-- Striped Rows -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('bitanic.product-setting.update') }}" method="POST" id="'form-product">
                        @csrf
                        @method('PUT')
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />
                        <div class="row">
                            <div class="col mb-3">
                                <label for="data-input-title" class="form-label">Judul</label>
                                <input type="text" id="data-input-title" class="form-control" name="title" value="{{ $data->title }}" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="data-input-sub" class="form-label">Sub Judul</label>
                                <input type="text" id="data-input-sub" class="form-control" name="sub" value="{{ $data->sub }}" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="data-input-description" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="data-input-description" name="description" rows="4">{{ $data->description }}</textarea>
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
            const btnSubmit = document.getElementById('submit-btn')
            btnSubmit.addEventListener('submit', e => {
                // Show loading indication
                btnSubmit.setAttribute('data-kt-indicator', 'on');

                // Disable button to avoid multiple click
                btnSubmit.disabled = true;

                // document.getElementById('form-product').submit()
            })
        </script>
    @endpush
</x-app-layout>
