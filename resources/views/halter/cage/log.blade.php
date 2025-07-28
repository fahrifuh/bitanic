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
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Utama /</span> Data Log Kandang</h4>
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
                                <th>Suhu</th>
                                <th>Kelembapan</th>
                                <th>Intensitas Cahaya</th>
                                <th>Gas</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($logs as $log)
                                <tr>
                                    <td>{{ now()->parse($log->created_at) }}</td>
                                    <td>{{ $log->no_device }}</td>
                                    <td>{{ $log->header }}</td>
                                    <td>{{ $log->temperature }}</td>
                                    <td>{{ $log->humidity }}</td>
                                    <td>{{ $log->light_intensity }}</td>
                                    <td>{{ $log->gas }}</td>
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
