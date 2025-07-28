<x-app-layout>

    @push('styles')
        <style>
            .preview-image {
                width: 100%;
                /* Adjust the width as desired */
                object-fit: cover;
                aspect-ratio: 3/1;
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
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master / <a
                    href="{{ route('bitanic.member.index') }}">Member</a> / <a href="{{ route('bitanic.member.show', $member->id) }}">{{ $member->name }}</a> /</span> Edit</h4>
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
                    <form action="{{ route('bitanic.member.update', $member->id) }}" method="POST" id="form-member">
                        @csrf
                        @method('PUT')
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />

                        <div class="row g-2">
                            <div class="col-12 col-md-4 mb-3">
                                <label for="data-input-name" class="form-label">Nama</label>
                                <input type="text" id="data-input-name" class="form-control" name="name"
                                    value="{{ $member->name }}" required />
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label for="data-input-max-comodities" class="form-label">Jumlah Komoditas</label>
                                <input type="number" min="0" id="data-input-max-comodities" class="form-control" name="max_commodities"
                                    value="{{ $member->max_commodities }}" required />
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label for="data-input-fee" class="form-label">Harga</label>
                                <input type="number" min="0" id="data-input-fee" class="form-control" name="fee"
                                    value="{{ $member->fee }}" required />
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
            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!")
            })
        </script>
    @endpush
</x-app-layout>
