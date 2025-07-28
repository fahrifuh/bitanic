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

        i {
            pointer-events: none;
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
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Setting / Halaman Utama /</span> Tentang Startup</h4>
    </x-slot>
    {{-- End Header --}}

    <div class="row">
        <div class="col-md-12">
            <!-- Striped Rows -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('bitanic.about-our-startup-setting.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col">
                                <a href="{{ route('bitanic.about-our-startup-setting.create-event-images') }}" class="btn btn-secondary"><i class="bx bx-plus"></i>Tambah Foto</a>
                                <a href="{{ route('bitanic.about-our-startup-setting.delete-event-images') }}" class="btn btn-danger"><i class="bx bx-trash"></i>Hapus Foto</a>
                            </div>
                        </div>
                        <div class="row my-3">
                            <div class="col-12 col-md-12 col-lg-12">
                                <div id="previewContainer">
                                    @foreach ($data->event_images as $event_images)
                                        <div class="previewBox">
                                            <img src="{{ asset($event_images) }}" class="previewImage" />
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="data-input-description" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="data-input-description" name="description" rows="2">{{ $data->description }}</textarea>
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
