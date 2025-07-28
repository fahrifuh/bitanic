<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
        <style>
            .flex-nowrap {
                flex-wrap: nowrap !important;
            }
        </style>
    @endpush

    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">
                Master
                @if (Auth::user()->role == 'admin')
                    / <a href="{{ route('bitanic.farmer.index') }}">Data Pengguna Bitanic Pro</a>
                    / <a
                    href="{{ route('bitanic.farmer.show', $farmer->user_id) }}">{{ $farmer->full_name }}</a>
                @else
                    / {{ $farmer->full_name }}
                @endif
                / <a href="{{ route('bitanic.land.index', $farmer->id) }}">Data Lahan</a>
                / <a href="{{ route('bitanic.land.show', [
                    'farmer' => $farmer->id,
                    'land' => $land->id
                ]) }}">{{ $land->name }}</a>
                / <a href="{{ route('bitanic.garden.index', [
                    'farmer' => $farmer->id,
                    'land' => $land->id
                ]) }}">Data Kebun</a>
                / <a href="{{ route('bitanic.garden.show', [
                    'farmer' => $farmer->id,
                    'land' => $land->id,
                    'garden' => $garden->id
                ]) }}">{{ $garden->name }}</a>
                /
            </span>
            Riwayat Komoditi
        </h4>
    </x-slot>
    {{-- End Header --}}

    <div class="row">
        <div class="col-md-12">
            <!-- Striped Rows -->
            <div class="card">
                <div class="row">
                    <div class="col-md-5">
                        <div class="float-start">
                        </div>
                    </div>
                    <div class="col-md-3"></div>
                    <div class="col-md-4">
                        <div class="float-end m-2">
                        </div>
                    </div>
                </div>
                <div class="table-responsive text-wrap">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Komoditi</th>
                                <th>Hasil Panen</th>
                                <th>Tanggal Panen</th>
                                <th>Catatan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($history_commodities as $commodity)
                                <tr>
                                    <td>{{ ($history_commodities->currentPage() - 1) * $history_commodities->perPage() + $loop->iteration }}
                                    </td>
                                    <td>
                                        <a href="javascript:;" type="button" class="avatar pull-up"
                                            data-bs-toggle="modal" data-bs-target="#modalFoto"
                                            data-foto="{{ asset($commodity->crop->picture ?? 'bitanic-landing/default-profile.png') }}"
                                            data-input-id="{{ $commodity->id }}" style="display: inline-block;">
                                            <img src="{{ asset($commodity->crop->picture ?? 'bitanic-landing/default-profile.png') }}" alt="Avatar"
                                                class="rounded" />
                                        </a>
                                        {{ $commodity->crop->crop_name }}
                                    </td>
                                    <td>{{ $commodity->value }} {{ $commodity->unit }}</td>
                                    <td>{{ $commodity->harvested }}</td>
                                    <td>
                                        {{ Str::limit($commodity->note, 20, '...') }}
                                        <i class='bx bx-info-circle' style="cursor: pointer;" data-bs-toggle="popover"
                                            data-bs-offset="0,14" data-bs-placement="left" data-bs-html="true"
                                            data-bs-content="<p>{{ $commodity->note }}</p>" title="Catatan"></i>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-icon btn-danger my-1"
                                            data-id="{{ $commodity->id }}"
                                            data-name="{{ $commodity->city_name }}" href="javascript:void(0);"
                                            title="Hapus Data"
                                            onclick="destroyHarvestProduce({{ $commodity->id }}), '{{ $commodity->id }}'">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </td>
                                </tr>

                            @empty

                                <tr>
                                    <td colspan="7" class="text-center">Data tidak ada</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                {{ $history_commodities->links() }}
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
            <!--/ Striped Rows -->
        </div>
    </div>

    @include('bitanic.harvest-produce._modal-picture')

    @push('scripts')
        <script src="{{ asset('theme/js/ui-popover.js') }}"></script>
        <script src="{{ asset('js/select2.min.js') }}"></script>
        <script>

            const destroyHarvestProduce = (id, name) => {
                handleDeleteRows(
                    "{{ route('bitanic.commodity.destroy', ['farmer' => $farmer->id, 'land' => $land->id, 'garden' => $garden->id, 'commodity' => 'ID']) }}"
                    .replace('ID', id), "{{ csrf_token() }}",
                    name
                )
            }

            // btn picture
            const myModalPrev = new bootstrap.Modal(document.getElementById("modalFoto"), {});
            const modalFoto = document.getElementById('modalFoto')
            modalFoto.addEventListener('show.bs.modal', function(event) {
                // Button that triggered the modal
                const button = event.relatedTarget
                // Extract info from data-bs-* attributes
                // const recipient = button.getAttribute('data-bs-whatever')
                const modalTitle = modalFoto.querySelector('.modal-title')
                modalTitle.textContent = 'Foto'

                for (let index = 0; index < button.attributes.length; index++) {
                    if (button.attributes[index].nodeName.includes('data-foto')) {
                        document.getElementById('iframe').src = button.attributes[index].nodeValue
                    }
                }

            })

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

            });
        </script>
    @endpush
</x-app-layout>
