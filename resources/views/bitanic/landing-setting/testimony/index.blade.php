<x-app-layout>

    @push('styles')
        <style>
            .star-rating {
                display: inline-block;
                font-size: 0;
                unicode-bidi: bidi-override;
                direction: rtl;
            }

            .star-rating input {
                display: none;
            }

            .star-rating label {
                display: inline-block;
                font-size: 40px;
                padding: 0 5px;
                cursor: pointer;
                color: #ccc;
                transition: color 0.2s;
            }

            .star-rating label i {
                font-size: 2rem;
            }

            .star-rating label i.checked {
                color: #ffcc00;
            }

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
                aspect-ratio: 1/1;
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
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb fs-4">
                <li class="breadcrumb-item">
                    <a href="javascript:void(0);">Pengaturan</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="javascript:void(0);">Halaman Utama</a>
                </li>
                <li class="breadcrumb-item active">Testimoni</li>
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
                            <a href="{{ route('bitanic.testimony.create') }}" class="btn btn-icon btn-primary"
                                title="Tambah Testimoni">
                                <i class="bx bx-plus"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @if ($testimonies->hasPages())
                    <div class="row">
                        <div class="col-md-12">
                            <nav aria-label="Page navigation">
                                <ul class="pagination justify-content-center">
                                    {{ $testimonies->links() }}
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
                @foreach ($testimonies as $testimony)
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="card h-100">
                            <div class="card-body d-flex flex-column justify-content-between gap-3">
                                <div class="row g-3">
                                    <div class="col-12 col-md-3">
                                        <img src="{{ asset($testimony->picture ?? 'bitanic-landing/default-image.jpg') }}"
                                            alt="Test" class="product-img rounded-circle">
                                    </div>
                                    <div class="col-12 col-md-9 d-flex align-items-center">
                                        <div>
                                            <h5 class="card-title">{{ $testimony->name }}</h5>
                                            <div class="star-rating">
                                                @for ($i = 5; $i >= 1; $i--)
                                                    <label title="{{ $i }} stars"><i class='bx bxs-star @if($i <= $testimony->rating) checked @endif'></i></label>
                                                @endfor
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <p>{{ $testimony->comment }}</p>
                                    </div>
                                </div>
                                <div>
                                    <div class="d-flex justify-content-end mb-3">
                                        <span>
                                            <a href="{{ route('bitanic.testimony.show', $testimony->id) }}"
                                                class="btn btn-info btn-icon btn-sm" title="Detail Layanan">
                                                <i class="bx bx-list-ul"></i>
                                            </a>
                                            <a href="{{ route('bitanic.testimony.edit', $testimony->id) }}"
                                                class="btn btn-warning btn-icon btn-sm" title="Edit Layanan">
                                                <i class="bx bx-edit"></i>
                                            </a>
                                            <button type="button" onclick="deletePest(this)"
                                                data-id="{{ $testimony->id }}" data-name="{{ $testimony->title }}"
                                                class="btn btn-danger btn-sm btn-icon" title="Hapus Layanan">
                                                <i class="bx bx-trash event-none"></i>
                                            </button>
                                        </span>
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
                    "{{ route('bitanic.testimony.destroy', 'ID') }}".replace('ID',
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

                window.location = "{{ route('bitanic.testimony.index') }}"
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

                window.location = "{{ route('bitanic.testimony.index') }}"
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
