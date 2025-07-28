<x-app-layout>
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master /</span> Data Telemetri</h4>
    </x-slot>
    {{-- End Header --}}

    <div class="row">
        <div class="col-md-12">
            <!-- Striped Rows -->
            <div class="card">
                <div class="row">
                    <div class="col-md-12">
                        <!-- Search -->
                        <form action="{{ route('bitanic.log-activity.index') }}" method="GET" id="form-search">
                            <div class="row p-1 mt-2 mx-2">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary text-white" style="cursor: pointer;" onclick="document.getElementById('form-search').submit()">
                                            <i class="bx bx-search"></i>
                                        </span>
                                        <input type="text" class="form-control shadow-none"
                                            placeholder="Search..." aria-label="Search..." name="search"
                                            value="{{ request()->query('search') }}" />
                                    </div>
                                </div>
                            </div>
                        </form>
                        <!-- /Search -->
                    </div>
                </div>
                <div class="table-responsive text-wrap">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Farmer</th>
                                <th>Device</th>
                                <th>Garden</th>
                                <th>node_id</th>
                                <th>PH Level</th>
                                <th>Timestamp</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($data as $telemetri)
                                <tr>
                                    <td>{{ (($data->currentPage() - 1 ) * $data->perPage() ) + $loop->iteration }}</td>
                                    <td>{{ optional($telemetri->farmer)->full_name }}</td>
                                    <td>{{ optional($telemetri->device)->device_series }}</td>
                                    <td>{{ optional($telemetri->garden)->name }}</td>
                                    <td>{{ $telemetri->node_id }}</td>
                                    <td>{{ $telemetri->pH_Level }}</td>
                                    <td>{{ now()->parse($telemetri->created_at)->addHours(7) }}</td>
                                    <td>
                                        <button class="btn btn-info" onclick="telemetriShow({{ $telemetri->id }})">Detail</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">Data tidak ada</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                {{ $data->links() }}
                            </ul>
                          </nav>
                    </div>
                </div>
            </div>
            <!--/ Striped Rows -->
        </div>
    </div>

    @include('bitanic.telemetri._modal-detail')

    @push('scripts')
        <script>
            const modalTelemetri = new bootstrap.Modal(document.getElementById("modalDetail"), {});

            const telemetriShow = async (id) => {
                try {
                    const settings = {
                        method: 'GET',
                        headers: {
                            'x-csrf-token': '{{ csrf_token() }}'
                        },
                    }

                    modalTelemetri.toggle()
                    telemetriSpinner()

                    const [data, error] = await yourRequest(
                        "{{ route('bitanic.telemetri.show', ['telemetri' => 'ID']) }}".replace('ID', id),
                        settings)


                    if (error) {
                        if ("messages" in error) {
                            let errorMessage = ''

                            modalTelemetri.toggle()

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

                        return false
                    }

                    const telemetri = data.data
                    telemetriSpinner()

                    document.getElementById('detail-farmer').innerHTML = telemetri.farmer.full_name
                    document.getElementById('detail-device').innerHTML = telemetri.device.device_series
                    document.getElementById('detail-garden').innerHTML = telemetri.garden.name
                    document.getElementById('detail-node-id').innerHTML = telemetri.node_id
                    document.getElementById('detail-header').innerHTML = telemetri.header
                    document.getElementById('detail-temperature').innerHTML = telemetri.temperature
                    document.getElementById('detail-humidity').innerHTML = telemetri.humidity
                    document.getElementById('detail-ph-level').innerHTML = telemetri.pH_Level
                    document.getElementById('detail-light').innerHTML = telemetri.light
                    document.getElementById('detail-gas').innerHTML = telemetri.gas
                    document.getElementById('detail-motor').innerHTML = telemetri.motor
                    document.getElementById('detail-mode').innerHTML = telemetri.mode
                    document.getElementById('detail-tail').innerHTML = telemetri.tail

                } catch (error) {
                    console.log(error);
                }
            }

            const telemetriSpinner = () => {
                $('#my-spinner').toggleClass('my-show');
                $('#my-spinner').toggleClass('fade');

                const telemetriVal = document.querySelectorAll('#modalDetail .detail-val')
                telemetriVal.forEach(telText => {
                    telText.innerHTML = ``
                });
            }

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

                const inputSearch = document.querySelectorAll('.input-search')
                inputSearch.forEach(eInput => {
                    eInput.addEventListener('change', e => {
                        document.getElementById('form-search').submit()
                    })
                });
            });
        </script>
    @endpush
</x-app-layout>
