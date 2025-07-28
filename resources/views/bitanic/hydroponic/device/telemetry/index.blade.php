<x-app-layout>

    @push('styles')
        <style>
            .bank-avatar {
                width: 100px;
            }

            .bank-avatar img {
                width: 100%;
                height: 100%;
            }

            .event-none {
                pointer-events: none;
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
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb fs-4">
                <li class="breadcrumb-item">
                    <a href="javascript:void(0);">Hidroponik</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('bitanic.hydroponic.device.index') }}">Perangkat</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('bitanic.hydroponic.device.show', $hydroponicDevice->id) }}">{{ $hydroponicDevice->series }}</a>
                </li>
                <li class="breadcrumb-item active">Telemetri</li>
            </ol>
        </nav>
    </x-slot>
    {{-- End Header --}}

    @if (session()->has('success'))
        <x-alert-message class="alert-success">{{ session()->get('success') }}</x-alert-message>
    @endif
    @if (session()->has('failed'))
        <x-alert-message class="alert-danger">{{ session()->get('failed') }}</x-alert-message>
    @endif

    <!-- Validation Errors -->
    <x-auth-validation-errors class="mb-4" :errors="$errors" />

    <div class="row">
        <div class="col-md-12">
            <!-- Striped Rows -->
            <div class="card">
                <div class="row">
                    <div class="col-md-12">
                        <div class="float-start m-3">
                            <h3>Telemetry Perangkat Hidroponik</h3>
                        </div>
                        <div class="float-end m-3">
                            <button class="btn btn-icon btn-success"
                                data-bs-toggle="modal" data-bs-target="#modalExportExcel"
                                title="Klik untuk export excel"><i
                                    class="bx bxs-file-export"></i>
                        </div>
                    </div>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Timestamp</th>
                                <th>Suhu</th>
                                <th>Kelembapan</th>
                                <th>TDM/PPM</th>
                                <th>pH</th>
                                <th>Volume Air</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($hydroponicDeviceTelemetries as $hydroponicDeviceTelemetry)
                                <tr>
                                    <td>{{ ($hydroponicDeviceTelemetries->currentPage() - 1) * $hydroponicDeviceTelemetries->perPage() + $loop->iteration }}
                                    </td>
                                    <td>{{ $hydroponicDeviceTelemetry->created_at }}</td>
                                    <td>{{ $hydroponicDeviceTelemetry->sensors->temperature }}</td>
                                    <td>{{ $hydroponicDeviceTelemetry->sensors->humidity }}</td>
                                    <td>{{ $hydroponicDeviceTelemetry->sensors->tdm }}</td>
                                    <td>{{ $hydroponicDeviceTelemetry->sensors->ph }}</td>
                                    <td>{{ $hydroponicDeviceTelemetry->sensors->water_volume }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($hydroponicDeviceTelemetries->hasPages())
                    <div class="row">
                        <div class="col-md-12">
                            <nav aria-label="Page navigation">
                                <ul class="pagination justify-content-center">
                                    {{ $hydroponicDeviceTelemetries->links() }}
                                </ul>
                            </nav>
                        </div>
                    </div>
                @endif
            </div>
            <!--/ Striped Rows -->
        </div>
    </div>

    @include('bitanic.hydroponic.device.telemetry._modal-export')

    @push('scripts')
        <script>

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");
            });
        </script>
    @endpush
</x-app-layout>
