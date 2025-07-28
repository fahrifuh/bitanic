<x-app-layout>
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Setting / Halaman Utama /</span> Kontak Kami</h4>
    </x-slot>
    {{-- End Header --}}

    <div class="row">
        <div class="col-md-12">
            <!-- Striped Rows -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('bitanic.contact-us-setting.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col mb-3">
                                <label for="data-input-email" class="form-label">Email</label>
                                <input type="email" id="data-input-email" class="form-control" name="email" value="{{ $data->email }}" />
                            </div>
                            <div class="col mb-3">
                                <label for="data-input-phone-number" class="form-label">Nomor Telepon</label>
                                <input type="text" id="data-input-phone-number" class="form-control" name="phone_number" value="{{ $data->phone_number }}" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="data-input-alamat" class="form-label">Alamat</label>
                                <textarea class="form-control" id="data-input-alamat" name="alamat" rows="2">{{ $data->address }}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="data-input-link-linkedin" class="form-label">Link Linkedin</label>
                                <input type="text" id="data-input-link-linkedin" class="form-control" name="linkedin_link" value="{{ $data->linkedin_link }}" />
                            </div>
                            <div class="col mb-3">
                                <label for="data-input-link-ig" class="form-label">Link Instagram</label>
                                <input type="text" id="data-input-link-ig" class="form-control" name="ig_link" value="{{ $data->ig_link }}" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="data-input-link-facebook" class="form-label">Link Facebook</label>
                                <input type="text" id="data-input-link-facebook" class="form-control" name="facebook_link" value="{{ $data->facebook_link }}" />
                            </div>
                            <div class="col mb-3">
                                <label for="data-input-link-mitra" class="form-label">Link Mitra</label>
                                <input type="text" id="data-input-link-mitra" class="form-control" name="mitra_link" value="{{ $data->mitra_link }}" />
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
        </script>
    @endpush
</x-app-layout>
