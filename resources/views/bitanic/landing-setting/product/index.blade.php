<x-app-layout>

    @push('styles')
        <style>
            .bank-avatar {
                width: 100px;
            }

            .bank-avatar img {
                width: 100%;
                height: 100%;
            }

            .event-none {
                pointer-events: none;
            }

            .product-img {
                width: 100%;
                /* Adjust the width as desired */
                object-fit: cover;
                aspect-ratio: 16/9;
                border: 1px solid #9f999975;
                border-radius: 16px;
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
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb fs-4">
                <li class="breadcrumb-item">
                    <a href="javascript:void(0);">Pengaturan</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="javascript:void(0);">Halaman Utama</a>
                </li>
                <li class="breadcrumb-item active">Produk</li>
            </ol>
        </nav>
    </x-slot>
    {{-- End Header --}}

    @if (session()->has('success'))
        <x-alert-message class="alert-success">{{ session()->get('success') }}</x-alert-message>
    @endif

    <div class="row g-3">
        <div class="col-12 col-md-12">
            <!-- Striped Rows -->
            <div class="card">
                <div class="row">
                    <div class="col-md-12">
                        <div class="float-start m-3">
                        </div>
                        <div class="float-end m-3">
                            <a href="{{ route('bitanic.product.create') }}" class="btn btn-icon btn-primary"
                                title="Tambah Layanan">
                                <i class="bx bx-plus"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @if ($landingProducts->hasPages())
                    <div class="row">
                        <div class="col-md-12">
                            <nav aria-label="Page navigation">
                                <ul class="pagination justify-content-center">
                                    {{ $landingProducts->links() }}
                                </ul>
                            </nav>
                        </div>
                    </div>
                @endif
            </div>
            <!--/ Striped Rows -->
        </div>
        <div class="col-12">
            <div class="row">
                @foreach ($landingProducts as $landingProduct)
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="card h-100">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div>
                                    <div>
                                        <img src="{{ asset($landingProduct->image ?? 'bitanic-landing/default-image.jpg') }}"
                                            alt="Test" class="product-img">
                                    </div>
                                    <h5 class="card-title mt-3">{{ $landingProduct->title }}</h5>
                                    <p class="card-text mb-3">
                                        {{ Str::limit($landingProduct->description, 200, '...') }}
                                    </p>
                                </div>
                                <div>
                                    <div class="d-flex justify-content-between mb-3">
                                        <span>Rp {{ number_format_1($landingProduct->price, 0) }}</span>
                                        <span>
                                            <a href="{{ route('bitanic.product.show', $landingProduct->id) }}"
                                                class="btn btn-info btn-icon btn-sm" title="Detail Layanan">
                                                <i class="bx bx-list-ul"></i>
                                            </a>
                                            <a href="{{ route('bitanic.product.edit', $landingProduct->id) }}"
                                                class="btn btn-warning btn-icon btn-sm" title="Edit Layanan">
                                                <i class="bx bx-edit"></i>
                                            </a>
                                            <button type="button" onclick="deletePest(this)"
                                                data-id="{{ $landingProduct->id }}" data-name="{{ $landingProduct->title }}"
                                                class="btn btn-danger btn-sm btn-icon" title="Hapus Layanan">
                                                <i class="bx bx-trash event-none"></i>
                                            </button>
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        @foreach ($landingProduct->tags as $tag)
                                            <span><img src="{{ asset('bitanic-landing/circle-check.svg') }}"
                                                    alt="circle-check">&nbsp;{{ $tag }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @include('bitanic.lite.farmer._modal-picture')

    @push('scripts')
        <script>
            // btn picture
            const myModalPrev = new bootstrap.Modal(document.getElementById("modalFoto"), {});
            const modalFoto = document.getElementById('modalFoto')
            modalFoto.addEventListener('show.bs.modal', function(event) {
                // Button that triggered the modal
                const button = event.relatedTarget
                // Extract info from data-bs-* attributes
                // const recipient = button.getAttribute('data-bs-whatever')
                const modalTitle = modalFoto.querySelector('.modal-title')
                modalTitle.textContent = 'Foto Petani'

                for (let index = 0; index < button.attributes.length; index++) {
                    if (button.attributes[index].nodeName.includes('data-foto')) {
                        document.getElementById('iframe').src = button.attributes[index].nodeValue
                    }
                }

            })

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
                    "{{ route('bitanic.product.destroy', 'ID') }}".replace('ID',
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

                window.location = "{{ route('bitanic.product.index') }}"
            }

            const changeStatus = async e => {
                const result = await Swal.fire({
                    title: "Ubah status?",
                    text: "Perubahan ini akan mempengaruhi data yang tampil di halaman utama.",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Ya",
                    cancelButtonText: "Batalkan",
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
                    method: 'PUT',
                    headers: {
                        'x-csrf-token': "{{ csrf_token() }}",
                        'Accept': "application/json",
                    }
                }

                const [data, error] = await yourRequest(
                    "{{ route('bitanic.career.change-status', 'ID') }}".replace('ID',
                        e.dataset.id), settings
                )

                deleteSpinner()
                if (error) {

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
                    text: "Kamu berhasil mengubag data " + name + "!",
                    icon: "success",
                    showConfirmButton: true,
                    allowOutsideClick: false
                })

                window.location = "{{ route('bitanic.product.index') }}"
            }

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

                // const btnDelete = document.querySelectorAll('.btn-delete')

                // btnDelete.forEach(element => {
                //     element.addEventListener('click', e => {
                //         handleDeleteRows("{{ route('bitanic.pest.destroy', 'ID') }}".replace('ID', e.target.dataset.id), "{{ csrf_token() }}", e.target.dataset.name)
                //     })
                // });
            });
        </script>
    @endpush
</x-app-layout>
