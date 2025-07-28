<x-app-layout>
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Mobile /</span> Data Artikel</h4>
    </x-slot>
    {{-- End Header --}}

    <div class="row">
        <div class="col-md-12">
            <!-- Striped Rows -->
            <div class="card">
                <div class="row">
                    <div class="col-md-4">
                        <div class="float-start">
                            <!-- Search -->
                            <form action="{{ route('bitanic.article.index') }}" method="GET" id="form-search">
                                <div class="row p-1">
                                    <div class="col-md-12 m-1">
                                        <div class="input-group">
                                            <span class="input-group-text bg-primary text-white"
                                                style="cursor: pointer;"
                                                onclick="document.getElementById('form-search').submit()">
                                                <i class="bx bx-search"></i>
                                            </span>
                                            <input type="text" class="form-control shadow-none"
                                                placeholder="Cari Judul..." aria-label="Cari Judul..." name="search"
                                                value="{{ request()->query('search') }}" />
                                        </div>
                                    </div>
                                    <div class="col-md-12 m-1">
                                        <div class="input-group">
                                            <span
                                                class="input-group-text bg-primary text-white"><small>Tipe</small></span>
                                            <select class="form-select input-search" id="select-tipe" name="tipe"
                                                aria-label="Default select example">
                                                <option value="all"
                                                    @if (!in_array(request()->query('tipe'), ['sayuran', 'buah', 'umum', 'tentang_kami', 'visi_misi'])) selected @endif>Semua Tipe
                                                </option>
                                                <option value="sayuran"
                                                    @if (request()->query('tipe') == 'sayuran') selected @endif>Sayuran</option>
                                                <option value="buah"
                                                    @if (request()->query('tipe') == 'buah') selected @endif>Buah</option>
                                                <option value="umum"
                                                    @if (request()->query('tipe') == 'umum') selected @endif>Umum</option>
                                                <option value="tentang_kami"
                                                    @if (request()->query('tipe') == 'tentang_kami') selected @endif>Tentang Kami
                                                </option>
                                                <option value="visi_misi"
                                                    @if (request()->query('tipe') == 'visi_misi') selected @endif>Visi Misi</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12 m-1">
                                        <div class="input-group">
                                            <span
                                                class="input-group-text bg-primary text-white"><small>Tanggal</small></span>
                                            <input type="date" id="date-search" class="form-control input-search"
                                                name="tanggal" title="Search Tanggal"
                                                value="{{ request()->query('tanggal')? now()->parse(request()->query('tanggal'))->format('Y-m-d'): null }}" />
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <!-- /Search -->
                        </div>
                    </div>
                    <div class="col-md-4"></div>
                    <div class="col-md-4">
                        <div class="float-end m-3">
                            {{-- <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#modalForm" data-input-judul="" data-input-isi=""
                                data-input-tanggal="" data-input-tipe="sayuran" data-input-source=""
                                data-input-id="add" title="Tambah Artikel">
                                <i class="bx bx-plus"></i>&nbsp;Tambah
                            </button> --}}
                            <a href="{{ route('bitanic.article.create') }}" class="btn btn-primary"
                                title="Tambah Artikel">
                                <i class="bx bx-plus"></i>&nbsp;Tambah
                            </a>
                        </div>
                    </div>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Judul</th>
                                <th>Tipe</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($data as $article)
                                <tr>
                                    <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                                    <td>{{ $article->title }}</td>
                                    <td>{{ $article->type }}</td>
                                    <td>{{ $article->date }}</td>
                                    <td>
                                        <button type="button" class="btn btn-icon btn-info btn-sm"
                                            data-bs-toggle="modal" data-bs-target="#modalFoto"
                                            data-foto="{{ asset($article->picture) }}"
                                            data-input-id="{{ $article->id }}" title="Foto">
                                            <i class="bx bx-image"></i>
                                        </button>
                                        {{-- <button type="button" class="btn btn-icon btn-warning btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#modalForm" data-input-judul="{{ $article->title }}"
                                            data-input-isi="{{ $article->description }}"
                                            data-input-tanggal="{{ now()->parse($article->ads_start)->format('Y-m-d') }}"
                                            data-input-tipe="{{ $article->type }}"
                                            data-input-source="{{ $article->source }}"
                                            data-input-id="{{ $article->id }}"
                                            title="Edit Artikel">
                                            <i class="bx bx-edit-alt"></i>
                                        </button> --}}
                                        <a href="{{ route('bitanic.article.edit', $article->id) }}"
                                            class="btn btn-icon btn-warning btn-sm" title="Edit Artikel">
                                            <i class="bx bx-edit-alt"></i>
                                        </a>
                                        <button type="button" data-id="{{ $article->id }}"
                                            data-name="{{ $article->title }}"
                                            class="btn btn-icon btn-danger btn-sm btn-delete" title="Hapus Artikel">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center" colspan="5">Tidak ada data</td>
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

    @include('bitanic.article._modal-form')
    @include('bitanic.article._modal-foto')

    @push('scripts')
        <script src="{{ asset('ckeditor5-38.1.0/build/ckeditor.js') }}"></script>
        <script src="{{ asset('theme/js/ui-popover.js') }}"></script>
        <script>
            const myModal = new bootstrap.Modal(document.getElementById("modalForm"), {});
            const modal = document.getElementById('modalForm')
            modal.addEventListener('show.bs.modal', function(event) {
                // Button that triggered the modal
                const button = event.relatedTarget
                // Extract info from data-bs-* attributes
                // const recipient = button.getAttribute('data-bs-whatever')
                const modalTitle = modal.querySelector('.modal-title')

                for (let index = 0; index < button.attributes.length; index++) {
                    if (button.attributes[index].nodeName.includes('data-input')) {
                        document.getElementById(button.attributes[index].nodeName).value = button.attributes[index]
                            .nodeValue

                        if (button.attributes[index].nodeName == 'data-input-id') {
                            if (document.getElementById(button.attributes[index].nodeName).value != 'add') {
                                modalTitle.textContent = 'Edit'
                                // validator.validate()
                            } else {
                                modalTitle.textContent = 'Tambah'
                            }
                        }
                    }
                }

            })

            // Submit button handler
            const submitButton = document.getElementById('submit-btn');
            submitButton.addEventListener('click', async function(e) {
                // Prevent default button action
                e.preventDefault();

                showSpinner()

                // Show loading indication
                submitButton.setAttribute('data-kt-indicator', 'on');

                // Disable button to avoid multiple click
                submitButton.disabled = true;

                // Simulate form submission. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                let url, formSubmited;
                const editOrAdd = document.getElementById('data-input-id');
                const formData = new FormData();
                let myFoto = document.getElementById('data-input-foto').files;

                if (myFoto.length > 0) {
                    formData.append("picture", myFoto[0])
                }

                formData.append("title", document.getElementById('data-input-judul').value)
                formData.append("description", document.getElementById('data-input-isi').value)
                formData.append("date", document.getElementById('data-input-tanggal').value)
                formData.append("type", document.getElementById('data-input-tipe').value)
                formData.append("source", document.getElementById('data-input-source').value)

                if (editOrAdd.value != 'add') {
                    url = "{{ route('bitanic.article.update', 'ID') }}".replace('ID', editOrAdd.value)
                    formData.append("_method", 'PUT')
                } else {
                    url = "{{ route('bitanic.article.store') }}"
                }

                myModal.toggle()

                const settings = {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'x-csrf-token': '{{ csrf_token() }}'
                    },
                    body: formData
                }

                const [data, error] = await yourRequest(url, settings)

                // Remove loading indication
                submitButton.removeAttribute('data-kt-indicator');

                // Enable button
                submitButton.disabled = false;

                if (error) {
                    deleteSpinner()

                    if ("messages" in error) {
                        let errorMessage = ''

                        let element = ``
                        for (const key in error.messages) {
                            if (Object.hasOwnProperty.call(error.messages, key)) {
                                error.messages[key].forEach(message => {
                                    element += `<li>${message}</li>`;
                                });
                            }
                        }

                        errorMessage = `<ul>${element}</ul>`

                        Swal.fire({
                            html: errorMessage,
                            icon: "error",
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    }

                    return false
                }

                Swal.fire({
                    text: "Kamu berhasil menyimpan data!.",
                    icon: "success",
                    showConfirmButton: false,
                    allowOutsideClick: false,
                })

                window.location.reload();
            });

            // btn picture
            const myModalPrev = new bootstrap.Modal(document.getElementById("modalFoto"), {});
            const modalFoto = document.getElementById('modalFoto')
            modalFoto.addEventListener('show.bs.modal', function(event) {
                // Button that triggered the modal
                const button = event.relatedTarget
                // Extract info from data-bs-* attributes
                // const recipient = button.getAttribute('data-bs-whatever')
                const modalTitle = modalFoto.querySelector('.modal-title')
                modalTitle.textContent = 'Foto Artikel'

                for (let index = 0; index < button.attributes.length; index++) {
                    if (button.attributes[index].nodeName.includes('data-foto')) {
                        document.getElementById('iframe').src = button.attributes[index].nodeValue
                    }
                }

            })

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

                const btnDelete = document.querySelectorAll('.btn-delete')

                btnDelete.forEach(element => {
                    element.addEventListener('click', e => {
                        console.log(e);
                        handleDeleteRows("{{ route('bitanic.article.destroy', 'ID') }}".replace('ID', e
                            .currentTarget.dataset.id), "{{ csrf_token() }}", e.currentTarget.dataset.name)
                    })
                });

                const inputSearch = document.querySelectorAll('.input-search')
                inputSearch.forEach(eInput => {
                    eInput.addEventListener('change', e => {
                        document.getElementById('form-search').submit()
                    })
                });
            });
        </script>
    @endpush
</x-app-layout>
