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

    @if (session()->has('success'))
        <x-alert-message class="alert-success">{{ session()->get('success') }}</x-alert-message>
    @endif
    @if (session()->has('failed'))
        <x-alert-message class="alert-danger">{{ session()->get('failed') }}</x-alert-message>
    @endif

    <div class="row g-2 d-flex justify-content-center">
        <div class="col-12 col-md-6 col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <p class="card-text">
                                @if ($shop->is_ktp_validated === null)
                                    Toko kamu sedang diverifikasi. Harap tunggu untuk dapat mengakses menu toko.
                                @else
                                    Verifikasi kamu DITOLAK. Coba upload kembali KTP untuk diverifikasi ulang. Pastikan
                                    foto KTP yang kamu kirim jelas.
                                @endif
                            </p>
                        </div>
                        <div class="col-12">
                            <div class="d-flex gap-2 align-items-center">
                                @if (!$shop->is_ktp_uploaded || $shop->is_ktp_validated === 0)
                                    <a href="{{ route('bitanic.shop.edit-ktp') }}"
                                        class="btn btn-outline-primary">Upload KTP</a>
                                @endif
                                <a href="javascript:void(0);" id="shop-delete" class="btn btn-outline-danger"
                                    title=" Klik untuk membatalkan pembuatan toko anda!">Batalkan</a>
                                <a href="{{ route('bitanic.shop.show-ktp') }}" target="__blank">Lihat KTP</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('bitanic.shop.destroy') }}" method="POST" id="form-delete">
        @csrf
        @method('DELETE')
    </form>

    @push('scripts')
        <script src="{{ asset('leaflet/leaflet.js') }}"></script>
        <script src="{{ asset('js/leaflet.markercluster-src.js') }}"></script>
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

                document.getElementById('shop-delete').addEventListener('click', async (e) => {
                    e.preventDefault()

                    const result = await Swal.fire({
                        text: "Menghapus Toko. Apa anda yakin?",
                        icon: "warning",
                        showCancelButton: true,
                        buttonsStyling: false,
                        confirmButtonText: "Ya, hapus!",
                        cancelButtonText: "Tidak, batalkan",
                        customClass: {
                            confirmButton: "btn fw-bold btn-danger",
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
