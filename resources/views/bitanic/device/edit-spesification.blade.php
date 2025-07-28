<x-app-layout>

    @push('styles')
        {{-- Cluster --}}
        <link rel="stylesheet" href="{{ asset('css/extend.css') }}">
        <style>
            .preview-image {
                width: 100%;
                /* Adjust the width as desired */
                object-fit: cover;
                aspect-ratio: 16/9;
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
                    href="{{ route('bitanic.device.index') }}">Data Bitanic Pro & RSC</a> / {{ $device->device_series }} </span>/
            Edit Spesifikasi </h4>
    </x-slot>
    {{-- End Header --}}

    <div class="row">
        <div class="col-md-12">
            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    {{ session()->get('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <!-- Striped Rows -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('bitanic.device.update-specification', $device->id) }}" method="POST"
                        id="form-product">
                        @csrf
                        @method('PUT')
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />

                        <div class="row mb-3">
                            <div class="col-12 mb-2">
                                <button class="btn btn-info" id="btn-new-spesifik">Tambah Spesifikasi</button>
                            </div>
                            <div class="col-md-12" id="form-spesifik">
                                @foreach ($device->specification as $specification)
                                    <div class="d-flex flex-wrap gap-2">
                                        <div class="flex-fill">
                                            <label class="form-label">Nama Spesifikasi</label>
                                            <input type="text" class="form-control data-input-nama-spesifik"
                                                name="spesifikasi[{{ $loop->index }}][name]"
                                                value="{{ $specification->name }}" />
                                        </div>
                                        <div class="flex-fill">
                                            <label class="form-label">Isi Spesifikasi</label>
                                            <input type="text" class="form-control data-input-value-spesifik"
                                                name="spesifikasi[{{ $loop->index }}][value]"
                                                value="{{ $specification->value }}" />
                                        </div>
                                        <div class="flex-shrink-1 align-self-end">
                                            <button class="btn btn-danger btn-delete-element">X</button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary float-end" id="submit-btn">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!--/ Striped Rows -->
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('theme/js/ui-popover.js') }}"></script>
        <script src="{{ asset('js/extend.js') }}"></script>
        <!-- <script src="{{ asset('js/extra.js') }}"></script> -->
        <script>
            let spesifikasi_count = document.querySelector('#form-spesifik').children.length

            const newSpesifikasi = (formElement) => {
                formElement.insertAdjacentHTML("beforeend", `<div class="d-flex flex-wrap gap-2">
                <div class="flex-fill">
                    <label class="form-label">Nama Spesifikasi</label>
                    <input
                        type="text"
                        class="form-control data-input-nama-spesifik"
                        name="spesifikasi[${spesifikasi_count}][name]"
                        data-id=""
                    />
                </div>
                <div class="flex-fill">
                    <label class="form-label">Isi Spesifikasi</label>
                    <input
                        type="text"
                        class="form-control data-input-value-spesifik"
                        name="spesifikasi[${spesifikasi_count}][value]"
                        data-id=""
                    />
                </div>
                <div class="flex-shrink-1 align-self-end">
                    <button class="btn btn-danger btn-block mt-3 btn-delete-element">X</button>
                </div>
            </div>`)

                spesifikasi_count++
            }

            const deleteElement = (e) => {
                e.preventDefault()

                if (e.target.classList.contains('btn-delete-element')) {
                    e.target.parentNode.parentNode.remove()
                }
            }

            function showAlert(title) {
                Swal.fire({
                    icon: 'warning',
                    title,
                    confirmButtonText: 'Kembali',
                })
            }

            window.onload = function() {
                console.log('Hello world');

                document.querySelector('#btn-new-spesifik').addEventListener('click', e => {
                    e.preventDefault()
                    // newPompa('list_spesifik[]', 'data-input-spesifik', document.querySelector('#form-spesifik'))
                    newSpesifikasi(document.querySelector('#form-spesifik'))
                })
                document.querySelector('#form-spesifik').addEventListener('click', deleteElement)
            }
        </script>
    @endpush
</x-app-layout>
