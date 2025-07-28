<x-app-layout>
  {{-- Header --}}
  <x-slot name="header">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master @if(Auth::user()->role == 'admin') / <a href="{{ route('bitanic.farmer.index') }}">Data Petani</a> @endif / <a href="{{ route('bitanic.farmer.show', $farmer->user_id) }}">{{ $farmer->full_name }}</a> / <a href="{{ route('bitanic.garden.index', $farmer->id) }}">Data Kebun</a> / {{ $garden->land->name }} / </span> Telemetri</h4>
  </x-slot>
  {{-- End Header --}}

  <div class="row">
    <div class="col-md-12">
      <!-- Striped Rows -->
      <div class="card">
        <div class="row">
          <div class="col-md-12 m-3">
            <!-- Validation Errors -->
            <x-validation-errors class="mb-4" :errors="$errors" />

            <!-- Search -->
            <form action="{{ route('bitanic.telemetri.index', ['farmer' => $farmer->id, 'garden' => $garden->id]) }}" method="GET" id="form-search">
              <div class="row pb-1">
                <div class="col-md-6">
                  <div class="input-group">
                    <span class="input-group-text bg-primary text-white"><small>Tanggal</small></span>
                    <input type="date" class="form-control input-search" name="date_start" title="Search Tanggal awal" value="{{ request()->query('date_start')? now()->parse(request()->query('date_start'))->format('Y-m-d'): null }}" />
                    <span class="input-group-text bg-primary text-white"><small>-</small></span>
                    <input type="date" class="form-control input-search" name="date_end" title="Search Tanggal akhir" value="{{ request()->query('date_end')? now()->parse(request()->query('date_end'))->format('Y-m-d'): null }}" />
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <button class="btn btn-primary" onclick="document.getElementById('form-search').submit()">Filter</button>
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
                <th style="width: 5%;">No</th>
                <th>Device</th>
                <th>Soil 1</th>
                <th>Soil 2</th>
                <th>Temperature</th>
                <th>Humidity</th>
                <th>Heat Index</th>
                <th style="width: 15%;">Timestamp</th>
              </tr>
            </thead>
            <tbody class="table-border-bottom-0">
              @forelse ($data as $telemetri)
              <tr>
                <td>{{ (($data->currentPage() - 1 ) * $data->perPage() ) + $loop->iteration }}</td>
                <td>{{ $telemetri->device?->device_series }}</td>
                <td>{{ $telemetri->soil1 }}</td>
                <td>{{ $telemetri->soil2 }}</td>
                <td>{{ $telemetri->temperature }}</td>
                <td>{{ $telemetri->humidity }}</td>
                <td>{{ $telemetri->heatIndex }}</td>
                <td>{{ $telemetri->datetime }}</td>
              </tr>
              @empty
              <tr>
                <td colspan="8" class="text-center">Data tidak ada</td>
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
</x-app-layout>
