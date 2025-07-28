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
            Tambah Komoditi
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
                    <form action="{{ route('bitanic.commodity.store', [
                            'farmer' => $farmer->id,
                            'garden' => $garden->id,
                            'land' => $land->id
                        ]) }}"
                        method="POST" id="form-product"
                        enctype="multipart/form-data">
                        @csrf
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />

                        <div class="row g-2 mb-3">
                            <div class="col-12 col-md-6">
                                <label for="data-input-tanaman-id" class="form-label">Tanaman</label>
                                <select class="form-select" id="data-input-tanaman-id" name="crop_id"
                                    aria-label="Default select example">
                                    <option value="">-- Pilih Tanaman --</option>
                                    @forelse ($crops as $crop)
                                        <option value="{{ $crop->id }}"
                                            data-week="{{ $crop->frekuensi_siram }}"
                                            {{ $crop->id == $garden->crop_id ? 'selected' : '' }}>
                                            {{ $crop->crop_name }}</option>
                                    @empty
                                        <option disabled>Tidak ada tanaman</option>
                                    @endforelse
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="data-input-total" class="form-label">Jumlah Ditanam</label>
                                <input type="number" min="1" id="data-input-total" class="form-control" name="total"
                                    value="{{ old('total') }}" required />
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="data-input-planting-date" class="form-label">Tanggal Ditanam</label>
                                <input type="date" id="data-input-planting-date" class="form-control" name="planting_dates"
                                    value="{{ old('planting_dates') }}" required />
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="data-input-estimated-harvest" class="form-label">Estimasi Panen</label>
                                <input type="date" id="data-input-estimated-harvest" class="form-control" name="estimated_harvest"
                                    value="{{ old('estimated_harvest') }}" disabled />
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
        <script src="{{ asset('js/select2.min.js') }}"></script>
        <script src="{{ asset('theme/js/ui-popover.js') }}"></script>
        <script>

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

                $('#data-input-tanaman-id').select2({
                    placeholder: "Pilih Tanaman",
                });

                $('#data-input-tanaman-id').on('select2:select', function(e) {
                    let data = e.params.data;
                    console.log(data.element.dataset.week);
                });

                document.querySelector('#data-input-planting-date').addEventListener('change', e => {
                    const numWeeks = document.querySelector('#data-input-tanaman-id option:checked')?.dataset?.week ?? 0;
                    const now = new Date(e.target.value);
                    now.setDate(now.getDate() + numWeeks * 7);

                    const formatedDate = now.toISOString().split("T")[0]

                    document.querySelector('#data-input-estimated-harvest').value = formatedDate
                    console.log(formatedDate);
                })
            })
        </script>
    @endpush
</x-app-layout>
