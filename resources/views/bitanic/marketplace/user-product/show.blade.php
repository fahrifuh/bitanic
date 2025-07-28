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
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Marketplace / <a href="{{ route('bitanic.user-product.index') }}">Produk Pengguna</a> /</span>
            {{ $product->name }}</h4>
    </x-slot>
    {{-- End Header --}}

    @if (session()->has('success'))
        <x-alert-message class="alert-success">{{ session()->get('success') }}</x-alert-message>
    @endif

    <div class="row">
        <div class="col-12 col-md-4">
            <img class="rounded border d-block product-img"
                src="{{ asset(isset($product->picture[0]) ? $product->picture[0] : $product->crop_for_sale->picture) }}"
                alt="Picture" />
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
                            @switch($product->is_disabled)
                                @case(0)
                                    <button type="button" id="product-delete"
                                        title="Klik untuk mengubah status produk"
                                        class="btn btn-success">Aktif</button>
                                    @break
                                @case(1)
                                    <button type="button" id="product-delete"
                                        title="Klik untuk mengubah status produk"
                                        class="btn btn-danger">Nonaktif</button>
                                    @break

                                @default
                            @endswitch
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
                <div class="card-body border-top border-bottom mx-0">
                    <div class="d-flex justify-content-between adivgn-items-center">
                        <span>Toko: {{ $product->shop->name }}</span>
                        <span>Pengguna: {{ $product->shop->farmer->full_name }}</span>
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

    <form action="{{ route('bitanic.user-product.update-disable', $product->id) }}" method="POST" id="form-delete">
        @csrf
        @method('PUT')
    </form>

    {{-- End Map --}}

    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

                document.getElementById('product-delete').addEventListener('click', async (e) => {
                    e.preventDefault()
                    const isDisabled = {{ $product->is_disabled }};

                    const result = await Swal.fire({
                        text: `Produk pengguna akan ${isDisabled ? 'aktifkan' : 'dinonaktifkan'}. Apa anda yakin?`,
                        icon: "warning",
                        showCancelButton: true,
                        buttonsStyling: false,
                        confirmButtonText: `Ya, ${isDisabled ? 'aktifkan' : 'nonaktifkan'}!`,
                        cancelButtonText: "Tidak, batalkan",
                        customClass: {
                            confirmButton: `btn fw-bold ${isDisabled ? 'btn-success' : 'btn-danger'}`,
                            cancelButton: "btn fw-bold btn-active-light-primary"
                        }
                    })

                    if (!result.value) {
                        return false
                    }

                    document.getElementById('form-delete').submit()
                })
            });
        </script>
    @endpush
</x-app-layout>
