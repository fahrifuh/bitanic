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
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master / <a href="{{ route('bitanic.lite-device.index') }}">Perangkat Lite</a> / <a href="{{ route('bitanic.lite-device.show', $lite_device->id) }}">{{ $lite_device->full_series }}</a>
            </span>/ Tambah Pompa </h4>
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
                    <form action="{{ route('bitanic.lite-device.lite-device-pump.store', $lite_device->id) }}" method="POST">
                        @csrf
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />

                        <div class="row mb-3 g-2">
                            <div class="col-12">
                                <small class="text-light fw-semibold d-block">Status Pompa</small>
                                <div class="form-check form-check-inline mt-3">
                                    <input class="form-check-input" type="radio" name="is_active" id="inlineRadio1" value="1" checked />
                                    <label class="form-check-label" for="inlineRadio1">Digunakan</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="is_active" id="inlineRadio2" value="0" />
                                    <label class="form-check-label" for="inlineRadio2">Tidak Digunakan</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="data-input-name" class="form-label">Nama <span class="text-danger">*tidak wajib</span></label>
                                <input type="text" class="form-control" name="name" />
                            </div>
                        </div>
                        <div class="row mb-3 g-2 d-none">
                            <div class="col-12">
                                <label for="data-input-morning-time" class="form-label">Setting TDS </label>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="input-group">
                                    <input type="number" step=".01" class="form-control" name="min_tds" />
                                    <span class="input-group-text" id="basic-addon13">Min</span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="input-group">
                                    <input type="number" step=".01" class="form-control" name="max_tds" />
                                    <span class="input-group-text" id="basic-addon13">Max</span>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3 g-2 d-none">
                            <div class="col-12">
                                <label for="data-input-morning-time" class="form-label">Setting pH </label>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="input-group">
                                    <input type="number" step=".01" class="form-control" name="min_ph" />
                                    <span class="input-group-text" id="basic-addon13">Min</span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="input-group">
                                    <input type="number" step=".01" class="form-control" name="max_ph" />
                                    <span class="input-group-text" id="basic-addon13">Max</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary w-100" id="submit-btn">Save</button>
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
