<x-app-layout>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
        <style>
            .preview-image {
                width: 100%;
                /* Adjust the width as desired */
                object-fit: cover;
                aspect-ratio: 1/1;
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
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">
                Master
                @if (Auth::user()->role == 'admin')
                    / <a href="{{ route('bitanic.farmer.index') }}">Data Pengguna Bitanic Pro</a>
                    / <a
                    href="{{ route('bitanic.farmer.show', $farmer->user_id) }}">{{ $farmer->full_name }}</a>
                @else
                    / {{ $farmer->full_name }}
                @endif
                / <a href="{{ route('bitanic.land.index', $farmer->id) }}">Data Lahan</a>
                / <a href="{{ route('bitanic.land.show', [
                    'farmer' => $farmer->id,
                    'land' => $land->id
                ]) }}">{{ $land->name }}</a>
                / <a href="{{ route('bitanic.garden.index', [
                    'farmer' => $farmer->id,
                    'land' => $land->id
                ]) }}">Data Kebun</a>
                / <a href="{{ route('bitanic.garden.show', [
                    'farmer' => $farmer->id,
                    'land' => $land->id,
                    'garden' => $garden->id
                ]) }}">{{ $garden->name }}</a>
                /
            </span>
            Tambah Hasil Panen
        </h4>
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
                    <form action="{{ route('bitanic.commodity.update-yield', [
                            'farmer' => $farmer->id,
                            'garden' => $garden->id,
                            'land' => $land->id,
                            'commodity' => $commodity->id,
                        ]) }}"
                        method="POST">
                        @csrf
                        @method('PUT')
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />

                        <div class="row g-2 mb-3">
                            <div class="col-12 col-md-6">
                                <label for="data-input-hasil-panen" class="form-label">Hasil Panen <span class="text-danger">* wajib diisi</span></label>
                                <input type="number" step="0.1" min="0" class="form-control" id="data-input-hasil-panen"
                                    name="hasil_panen" />
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="data-input-satuan-panen" class="form-label">Satuan <span class="text-danger">* wajib diisi</span></label>
                                <select class="form-select" id="data-input-satuan-panen" name="satuan"
                                    aria-label="Default select example">
                                    <option value="kuintal">Kuintal</option>
                                    <option value="kg">Kg</option>
                                    <option value="ton">Ton</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="data-input-catatan" class="form-label">Catatan</label>
                                <textarea class="form-control" id="data-input-catatan" name="catatan" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary float-end">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!--/ Striped Rows -->
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/select2.min.js') }}"></script>
        <script src="{{ asset('theme/js/ui-popover.js') }}"></script>
        <script>

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");
            })
        </script>
    @endpush
</x-app-layout>
