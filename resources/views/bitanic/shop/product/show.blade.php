<x-app-layout>
  @push('styles')
  {{-- Cluster --}}
  <link rel="stylesheet" href="{{ asset('css/MarkerCluster.css') }}">
  <link rel="stylesheet" href="{{ asset('css/MarkerCluster.Default.css') }}">
  <link rel="stylesheet" href="{{ asset('leaflet/leaflet.css') }}">
  <style>
    #shopMap {
      height: 250px;
    }

    .leaflet-legend {
      background-color: #f5f5f9;
      border-radius: 10%;
      padding: 10px;
      color: #3e8f55;
      box-shadow: 4px 3px 5px 5px #8d8989a8;
    }

    .card-hover:hover {
      border: 1px solid #3e8f55;
    }

    .product-img {
        width: 100%;
        object-fit: cover;
        aspect-ratio: 1/1;
        border: 1px solid #9f999975;
    }
  </style>
  @endpush
  {{-- Header --}}
  <x-slot name="header">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master / <a href="{{ route('bitanic.shop.index') }}">Toko {{ $product->shop->name }}</a> / </span> {{ $product->name }}</h4>
  </x-slot>
  {{-- End Header --}}

  {{-- Map --}}

  <div class="row">
    <div class="col-12 col-md-4">
        <img class="rounded border d-block product-img" src="{{ asset(isset($product->picture[0]) ? $product->picture[0] : $product->crop_for_sale->picture) }}" alt="Picture" />
    </div>
    <div class="col-12 col-md-8">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div class="float-start">
              <h5 class="card-title">{{ $product->name }}</h5>
              <h6 class="card-subtitle text-muted">{{ $product->category }}</h6>
            </div>
            <div class="float-end">
              <a href="{{ route('bitanic.shop.product-edit', $product->id) }}" title="Edit data produk" class="btn btn-warning"><i class="bx bx-edit-alt"></i>&nbsp;Edit</a>
              <button type="button" id="product-delete" title="Hapus produk" class="btn btn-danger"><i class="bx bx-trash-alt"></i>&nbsp;Hapus</button>
            </div>
          </div>
        </div>
        <div class="card-body border-top border-bottom mx-0">
          <div class="d-flex justify-content-between adivgn-items-center">
            <span>Harga: Rp&nbsp;{{ number_format($product->price, 0, ',', '.') }}</span>
            <span>Stok: {{ $product->stock }}</span>
            <span>Berat: {{ $product->weight }} Gram</span>
          </div>
        </div>
        <div class="card-body">
            <h6>Deskripsi</h6>
            <p class="card-text">
                {{ $product->description }}
            </p>
        </div>
      </div>
    </div>
  </div>

  <form action="{{ route('bitanic.shop.product-destroy', $product->id) }}" method="POST" id="form-delete">
    @csrf
    @method('DELETE')
  </form>

  {{-- End Map --}}

  @push('scripts')
  <script src="{{ asset('leaflet/leaflet.js') }}"></script>
  <script src="{{ asset('js/leaflet.markercluster-src.js') }}"></script>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      console.log("Hello World!");

      document.getElementById('product-delete').addEventListener('click', (e) => {
        e.preventDefault()
        console.log(e.target);

        document.getElementById('form-delete').submit()
      })
    });
  </script>
  @endpush
</x-app-layout>
