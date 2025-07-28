<x-app-layout>

    @push('styles')
        {{-- Cluster --}}
        <link rel="stylesheet" href="{{ asset('css/MarkerCluster.css') }}">
        <link rel="stylesheet" href="{{ asset('css/MarkerCluster.Default.css') }}">
        <link rel="stylesheet" href="{{ asset('leaflet/leaflet.css') }}">
        <link rel="stylesheet" href="{{ asset('css/extend.css') }}">
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <style>
            #myMap {
                height: 250px;
            }

            .leaflet-legend {
                background-color: #f5f5f9;
                border-radius: 10%;
                padding: 10px;
                color: #3e8f55;
                box-shadow: 4px 3px 5px 5px #8d8989a8;
            }

            #img-land {
                height: 100%;
                object-fit: cover;
            }
        </style>
    @endpush
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master / <a href="{{ route('bitanic.firmware.index') }}">Update Firmware</a> / </span>Tambah firmware </h4>
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
                    <form action="{{ route('bitanic.firmware.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />

                        <div class="row">
                            <div class="col mb-3">
                                <label for="data-input-levels" class="form-label">ID Perangkat</label>
                                <input type="text" id="data-input-levels" class="form-control" name="series"
                                    value="{{ old('series') }}" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="data-input-levels" class="form-label">File Firmware</label>
                                <input type="file" id="data-input-levels" class="form-control" name="firmware_file"
                                    accept="application/octet-stream" required aria-describedby="pictureHelp" />
                                    <div id="pictureHelp" class="form-text">Format file bin. Maks.
                                        500MB</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="data-input-levels" class="form-label">Versi</label>
                                <input type="text" id="data-input-levels" class="form-control" name="version"
                                    value="{{ old('version') }}" required />
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
        <script src="{{ asset('leaflet/leaflet.js') }}"></script>
        <script src="{{ asset('theme/js/ui-popover.js') }}"></script>
        <script src="{{ asset('js/extend.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <!-- <script src="{{ asset('js/extra.js') }}"></script> -->
        <script>
            function errorMessage(error) {
                if ("messages" in error) {
                    let errorMessage = ''

                    let element = ``
                    for (const key in error.messages) {
                        if (Object.hasOwnProperty.call(error.messages, key)) {
                            error.messages[key].forEach(message => {
                                element += `<li>${message}</li>`;
                            });
                        }
                    }

                    errorMessage = `<ul>${element}</ul>`

                    Swal.fire({
                        html: errorMessage,
                        icon: "error",
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                }
            }

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");
            })
        </script>
    @endpush
</x-app-layout>
