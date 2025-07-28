<x-app-layout>
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Setting /</span> Data Log Activity</h4>
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
                                            placeholder="Cari User..." aria-label="Cari User..." name="search"
                                            value="{{ request()->query('search') }}" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    @php
                                    @endphp
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary text-white"><small>Even</small></span>
                                        <select class="form-select input-search" id="select-event" name="event" aria-label="Default select example">
                                            <option value="all" @if(!in_array(request()->query('event'), $events)) selected @endif>Semua Even</option>
                                            @foreach ($events as $event)
                                                <option value="{{ $event }}" @if(request()->query('event') == $event) selected @endif>{{ ucfirst($event) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary text-white"><small>Bulan</small></span>
                                        <input type="month" id="date-search" class="form-control input-search"
                                            name="month" title="Search Bulan"
                                            value="{{ request()->query('month')? now()->parse(request()->query('month'))->format('Y-m'): null }}" />
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
                                <th>#</th>
                                <th>Timestamp</th>
                                <th>Deskripsi</th>
                                <th>Subyek</th>
                                <th>Even</th>
                                <th>User</th>
                                <th>Properti</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($data as $log)
                                @php
                                    $class = 'primary';

                                    if ($log->event == 'deleted' || $log->event == 'logout') {
                                        $class = 'danger';
                                    } elseif ($log->event == 'updated') {
                                        $class = 'warning';
                                    } elseif ($log->event == 'created') {
                                        $class = 'success';
                                    }
                                @endphp
                                <tr>
                                    <td>{{ (($data->currentPage() - 1 ) * $data->perPage() ) + $loop->iteration }}</td>
                                    <td>{{ now()->parse($log->created_at) }}</td>
                                    <td>{{ ucwords($log->description) }}</td>
                                    <td>{{ $log->subject_type ? str_replace("App\\Models\\", '', $log->subject_type) : '-' }}</td>
                                    <td>
                                        <span class="badge rounded-pill bg-{{ $class }}">{{ ucfirst($log->event) }}</span>
                                    </td>
                                    <td>{{ optional($log->causer_type::find($log->causer_id))->name }}</td>
                                    <td>{{ $log->properties }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data</td>
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

    @push('scripts')
        <script>
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
