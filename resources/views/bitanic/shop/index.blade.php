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
                outline: 1px solid #3e8f55;
            }
        </style>
    @endpush
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master /</span> Toko</h4>
    </x-slot>
    {{-- End Header --}}

    {{-- Map --}}

    <div class="row">
        <div class="col-12 col-md-6 col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="float-start">
                        <h5 class="card-title">Toko {{ $shop->name }}</h5>
                    </div>
                    <div class="float-end">
                        <a href="{{ route('bitanic.shop.balance-withdraw.index') }}"
                            title="Klik untuk detail balance">
                            <h6>Balance Rp {{ number_format($shop->balance) }}</h6>
                        </a>
                    </div>
                </div>
                <img class="img-fluid" src="{{ asset($shop->picture) }}" alt="Card image cap" />
                <div id="shopMap"></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <label for="" class="fw-bold">No Rekening</label>
                            <p class="card-text">
                                {{ $shop->bank_type }}&nbsp;
                                <span id="resi-text">{{ Str::mask($shop->bank_account, '*', 0, -3) }}
                                    <span class="cursor-pointer" id="copy-element" data-bs-toggle="tooltip"
                                        data-bs-offset="0,4" data-bs-placement="top" data-bs-html="false" title="Copy"
                                        onclick="copyResi('{{ $shop->bank_account }}')">
                                        <i class='bx bx-copy-alt'></i>
                                    </span>
                                </span>
                            </p>
                        </div>
                        <div class="col-12">
                            <label for="" class="fw-bold">Alamat</label>
                            <p class="card-text">
                                {{ $shop->address }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('bitanic.shop.edit') }}" title="Edit data toko"
                        class="btn btn-sm btn-icon btn-warning"><i class="bx bx-edit-alt"></i></a>
                    <!-- <a href="javascript:void(0);" class="btn btn-icon btn-outline-danger"><i class="bx bx-trash"></i></a> -->
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-6">
            <div class="row g-3">
                <div class="col-12 col-md-12 col-lg-12">
                    <a href="{{ route('bitanic.shop.transaction-index') }}"
                        title="Klik untuk lihat list transaksi toko">
                        <div class="card">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div class="float-start">
                                    <h5 class="m-0">Transaksi</h5>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-12 col-md-12 col-lg-12">
                    <div class="card">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div class="float-start">
                                <h5 class="m-0">Produk</h5>
                            </div>
                            <div class="float-end">
                                <a href="{{ route('bitanic.shop.product-create') }}" class="btn btn-primary">Tambah</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-12 col-lg-12">
                    <form action="" method="GET" id="form-search">
                        <div class="input-group">
                            <span class="input-group-text bg-primary text-white"
                                style="cursor: pointer;"
                                onclick="document.getElementById('form-search').submit()">
                                <i class="bx bx-search"></i>
                            </span>
                            <input type="text" class="form-control shadow-none"
                                placeholder="Cari produk..." aria-label="Cari produk..." name="search"
                                value="{{ request()->query('search') }}" />
                        </div>
                    </form>
                </div>
                @forelse ($products as $product)
                    <div class="col-12 col-md-6 col-xl-4">
                        <a href="{{ route('bitanic.shop.product-show', ['product' => $product->id]) }}"
                            title="Klik untuk melihat detail produk.">
                            <div class="card card-hover">
                                <img class="card-img-top" src="{{ asset(isset($product->picture[0]) ? $product->picture[0] : $product->crop_for_sale->picture) }}"
                                    alt="Card image cap" />
                                <div class="card-body">
                                    <h6 class="card-title">{{ Str::limit($product->name, 50, '...') }}</h6>
                                    <p class="card-text">
                                        Stok:&nbsp;{{ $product->stock }}
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title m-0">Tidak Ada Produk</h5>
                        </div>
                    </div>
                </div>
                @endforelse

                <div class="col-12">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            {{ $products->links() }}
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    {{-- End Map --}}

    <div class="row">
    </div>

    @push('scripts')
        <script src="{{ asset('leaflet/leaflet.js') }}"></script>
        <script src="{{ asset('js/leaflet.markercluster-src.js') }}"></script>
        <script>
            const modal = document.getElementById('modalForm')
            let latlngs = [];
            let gardensMarker = [];
            let gardensPolygon = [];
            let marker, polygon;
            let defaultCoordinate = [parseFloat("{{ $shop->latitude }}"), parseFloat("{{ $shop->longitude }}")];
            const days = ['minggu', 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];

            const randomNumber = (min, max) => {
                return Math.floor(Math.random() * (max - min) + min)
            }

            function getColor(d) {
                return d > 1000 ? '#800026' :
                    d > 500 ? '#BD0026' :
                    d > 200 ? '#E31A1C' :
                    d > 100 ? '#FC4E2A' :
                    d > 50 ? '#FD8D3C' :
                    d > 20 ? '#FEB24C' :
                    d > 10 ? '#FED976' :
                    '#FFEDA0';
            }

            // Layer MAP
            let googleStreets = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                maxZoom: 20,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
            });
            let googleStreetsSecond = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                maxZoom: 20,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
            });
            let googleStreetsThird = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                maxZoom: 20,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
            });

            // Layer MAP
            const shopMap = L.map('shopMap', {
                preferCanvas: true,
                layers: [googleStreetsThird],
                zoomControl: true
            }).setView(defaultCoordinate, 12);

            var legend = L.control({
                position: 'bottomleft'
            });
            legend.onAdd = function(map) {

                var div = L.DomUtil.create('div', 'info legend');

                div.classList.add('leaflet-legend')

                div.innerHTML = `<i class="bx bxs-map" style="color:#3e92cf;"></i> Marker Toko`
                return div;
            };

            legend.addTo(shopMap);


            var markerGroup = L.markerClusterGroup();

            function copyResi(copiedText = '') {
                // Get the text content from the span
                const spanText = copiedText

                // Create a temporary textarea element
                const tempTextarea = document.createElement("textarea");
                tempTextarea.value = spanText;

                // Append the textarea to the DOM
                document.body.appendChild(tempTextarea);

                // Select the text in the textarea
                tempTextarea.select();
                tempTextarea.setSelectionRange(0, 99999); // For mobile devices

                // Execute the copy command
                document.execCommand("copy");

                // Clean up: remove the temporary textarea
                document.body.removeChild(tempTextarea);

                document.querySelector('.tooltip-inner').textContent = "Copied"

                setTimeout(() => {
                    document.querySelector('.tooltip-inner').textContent = "Copy"
                }, 1000);
            }

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

                shopMap.doubleClickZoom.disable();

                // markerGroup.on('clusterclick', function (a) {
                //     console.log(a.layer.getBounds());
                // })

                shopMap.invalidateSize()

                marker = L.marker(defaultCoordinate).bindPopup(JSON.stringify(defaultCoordinate)).addTo(shopMap)
                    .openPopup()
            });
        </script>
    @endpush
</x-app-layout>
