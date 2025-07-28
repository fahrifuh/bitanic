<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
        <style>
          .table-change-scroll
          {
              width: 100%;
              overflow-y: auto;
          }

          .flipped, .flipped .content
          {
              transform:rotateX(180deg);
              -ms-transform:rotateX(180deg); /* IE 9 */
              -webkit-transform:rotateX(180deg); /* Safari and Chrome */
          }
        </style>
    @endpush
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Utama /</span> Data Log Halter</h4>
    </x-slot>
    {{-- End Header --}}

    <div class="row">
        <div class="col-md-12">
            <!-- Striped Rows -->
            <div class="card">
                <div class="text-wrap table-change-scroll flipped">
                    <table class="table table-striped content">
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>No Device</th>
                                <th>Header</th>
                                <th>AccX</th>
                                <th>AccY</th>
                                <th>AccZ</th>
                                <th>GyroX</th>
                                <th>GyroY</th>
                                <th>GyroZ</th>
                                <th>Vbatt</th>
                                <th>HR</th>
                                <th>SPO2</th>
                                <th>Suhu</th>
                                <th>Tail</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($logs as $log)
                                <tr>
                                    <td>{{ now()->parse($log->created_at) }}</td>
                                    <td>{{ $log->No_Device }}</td>
                                    <td>{{ $log->Header }}</td>
                                    <td>{{ $log->AccX }}</td>
                                    <td>{{ $log->AccY }}</td>
                                    <td>{{ $log->AccZ }}</td>
                                    <td>{{ $log->GyroX }}</td>
                                    <td>{{ $log->GyroY }}</td>
                                    <td>{{ $log->GyroZ }}</td>
                                    <td>{{ $log->Vbatt }}</td>
                                    <td>{{ $log->HR }}</td>
                                    <td>{{ $log->SPO2 }}</td>
                                    <td>{{ $log->Suhu }}</td>
                                    <td>{{ $log->Tail }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="14">Data tidak ada</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                {{ $logs->links() }}
                            </ul>
                          </nav>
                    </div>
                </div>
            </div>
            <!--/ Striped Rows -->
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");
            });
        </script>
    @endpush
</x-app-layout>
