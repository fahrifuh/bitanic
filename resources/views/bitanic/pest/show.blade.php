<x-app-layout>
    @push('styles')
        <style>
            #pest-image {
                height: 100%;
                object-fit: cover;
            }

            .preview-image {
                width: 100%;
                /* Adjust the width as desired */
                object-fit: cover;
                aspect-ratio: 4/3;
                border: 1px solid #9f999975;
            }

            @media (max-width: 600px) {
                .preview-image {
                    width: calc(100% - 10px);
                }
            }
        </style>
    @endpush
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master / <a
                    href="{{ route('bitanic.pest.index') }}">Data Hama</a> /</span> {{ $pest->pest_type }}</h4>
    </x-slot>
    {{-- End Header --}}

    @if (session()->has('success'))
        <x-alert-message class="alert-success">{{ session()->get('success') }}</x-alert-message>
    @endif

    <div class="row">
        <div class="col-md-12">
            <!-- Striped Rows -->
            <div class="card">
                <div class="row g-0">
                    <div class="col-md-8">
                        <div class="card-body">
                            <div class="row g-2">
                                @if (auth()->user()->role == 'admin')
                                    <div class="col-12">
                                        <a href="{{ route('bitanic.pest.edit', $pest->id) }}"
                                            class="btn btn-warning btn-sm">
                                            Edit
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm" data-id="{{ $pest->id }}"
                                            data-name="{{ $pest->pest_type }}" onclick="deletePest(this)">
                                            Hapus
                                        </button>
                                    </div>
                                @endif
                                <div class="col-12">
                                    <label for="" class="fw-bold">Nama Hama</label>
                                    <p class="card-text" id="text-view-area">{{ $pest->pest_type }}</p>
                                </div>
                                <div class="col-12">
                                    <label for="" class="fw-bold">Nama Tanaman</label>
                                    <p class="card-text" id="text-view-area">{{ $pest->crop->crop_name }}</p>
                                </div>
                                <div class="col-12">
                                    <label for="" class="fw-bold">Ciri - ciri</label>
                                    <p class="card-text" id="text-view-latlng">{{ $pest->features }}</p>
                                </div>
                                <div class="col-12">
                                    <label for="" class="fw-bold">Gejala pada Tanaman</label>
                                    <p class="card-text" id="text-view-altitude">{{ $pest->symptomatic }}</p>
                                </div>
                                <div class="col-12">
                                    <label for="" class="fw-bold">Pencegahan HPT</label>
                                    <p class="card-text" id="text-view-address">{{ $pest->precautions }}</p>
                                </div>
                                <div class="col-12">
                                    <label for="" class="fw-bold">Penanggulangan HPT</label>
                                    <p class="card-text">{{ $pest->countermeasures }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <img class="card-img card-img-right"
                            src="{{ asset($pest->picture ?? 'theme/img/elements/17.jpg') }}" alt="Card image"
                            id="pest-image" />
                    </div>
                </div>
            </div>
            <!--/ Striped Rows -->
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('theme/js/ui-popover.js') }}"></script>
        <script>
            const deletePest = async e => {
                const result = await Swal.fire({
                    text: "Menghapus data tidak dapat dibatalkan, dan semua data yang berhubungan akan hilang",
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

                showSpinner()

                const settings = {
                    method: 'DELETE',
                    headers: {
                        'x-csrf-token': "{{ csrf_token() }}",
                        'Accept': "application/json",
                    }
                }

                const [data, error] = await yourRequest(
                        "{{ route('bitanic.pest.destroy', 'ID') }}".replace('ID',
                        e.dataset.id), settings
                    )

                if (error) {

                    deleteSpinner()

                    let errorMessage = ''

                    if ("messages" in error) {
                        let element = ``
                        for (const key in error.messages) {
                            if (Object.hasOwnProperty.call(error.messages, key)) {
                                error.messages[key].forEach(message => {
                                    element += `<li>${message}</li>`;
                                });
                            }
                        }

                        errorMessage = `<ul>${element}</ul>`
                    } else {
                        errorMessage = error.message
                    }

                    Swal.fire({
                            html: errorMessage,
                            icon: "error",
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                }

                Swal.fire({
                    text: "Kamu berhasil menghapus data " + name + "!",
                    icon: "success",
                    showConfirmButton: false,
                    allowOutsideClick: false
                })

                window.location = "{{ route('bitanic.pest.index') }}"
            }
            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

            })
        </script>
    @endpush
</x-app-layout>
