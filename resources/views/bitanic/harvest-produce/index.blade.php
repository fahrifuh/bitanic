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
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master /</span>
            @if (Auth::user()->role == 'admin')
                <a href="{{ route('bitanic.farmer.index') }}">Data Pengguna Bitanic Pro</a> /
            @endif <a href="#">Data
                Kebun</a> / {{ $garden_name }} / Data Hasil Panen
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
                                <th>No</th>
                                <th>Tanaman</th>
                                <th>Kebun</th>
                                <th>Hasil Panen</th>
                                <th>Tanggal Panen</th>
                                <th>Catatan</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($harvest_produces as $harvest_produce)
                                <tr>
                                    <td>{{ ($harvest_produces->currentPage() - 1) * $harvest_produces->perPage() + $loop->iteration }}
                                    </td>
                                    <td>
                                        <a href="javascript:;" type="button" class="avatar pull-up"
                                            data-bs-toggle="modal" data-bs-target="#modalFoto"
                                            data-foto="{{ asset($harvest_produce->crop->picture) }}"
                                            data-input-id="{{ $harvest_produce->id }}" style="display: inline-block;">
                                            <img src="{{ asset($harvest_produce->crop->picture) }}" alt="Avatar"
                                                class="rounded-circle" />
                                        </a>
                                        {{ $harvest_produce->crop->crop_name }}
                                    </td>
                                    <td>{{ $harvest_produce->garden->name }}</td>
                                    <td>{{ $harvest_produce->value }} {{ $harvest_produce->unit }}</td>
                                    <td>{{ $harvest_produce->date }}</td>
                                    <td>
                                        {{ Str::limit($harvest_produce->note, 20, '...') }}
                                        <i class='bx bx-info-circle' style="cursor: pointer;" data-bs-toggle="popover"
                                            data-bs-offset="0,14" data-bs-placement="left" data-bs-html="true"
                                            data-bs-content="<p>{{ $harvest_produce->note }}</p>" title="Catatan"></i>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-icon btn-danger my-1"
                                            data-id="{{ $harvest_produce->id }}"
                                            data-name="{{ $harvest_produce->city_name }}" href="javascript:void(0);"
                                            title="Hapus Data"
                                            onclick="destroyHarvestProduce({{ $harvest_produce->id }}), '{{ $harvest_produce->name }}'"><i
                                                class="bx bx-trash"></i>
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
                                {{ $harvest_produces->links() }}
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
            <!--/ Striped Rows -->
        </div>
    </div>

    @include('bitanic.farmer-group._modal-form')
    @include('bitanic.harvest-produce._modal-picture')

    @push('scripts')
        <script src="{{ asset('theme/js/ui-popover.js') }}"></script>
        <script src="{{ asset('js/select2.min.js') }}"></script>
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
                                $('.text-tidak-wajib').removeClass('d-none');

                                getProvinces(button.attributes['data-input-province'].nodeValue)
                                getCity(button.attributes['data-input-province'].nodeValue, button.attributes[
                                    'data-input-city'].nodeValue)
                                getDistricts(button.attributes['data-input-city'].nodeValue, button.attributes[
                                    'data-input-district'].nodeValue)
                                getSubdistricts(button.attributes['data-input-district'].nodeValue, button.attributes[
                                    'data-input-subdistrict'].nodeValue)
                                // validator.validate()
                            } else {
                                getProvinces()
                                getCity()
                                getDistricts()
                                getSubdistricts()

                                modalTitle.textContent = 'Tambah'
                                $('.text-tidak-wajib').addClass('d-none');
                            }
                        }
                    }
                }

            })

            // Submit button handler
            const submitButton = document.getElementById('submit-btn');
            submitButton.addEventListener('click', async function(e) {
                showSpinner()
                // Prevent default button action
                e.preventDefault();

                // Show loading indication
                submitButton.setAttribute('data-kt-indicator', 'on');

                // Disable button to avoid multiple click
                submitButton.disabled = true;

                // Simulate form submission. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                let url, formSubmited;
                const editOrAdd = document.getElementById('data-input-id');
                const formData = new FormData();

                let myFoto = document.getElementById('data-input-foto').files[0];

                if (typeof myFoto !== 'undefined') {
                    formData.append("picture", document.getElementById(
                        'data-input-foto').files[0])
                }

                formData.append("name", document.getElementById('data-input-group-name').value)
                formData.append("subdis_id", document.getElementById('data-input-subdistrict').value)
                formData.append("address", document.getElementById('data-input-address').value)

                if (editOrAdd.value != 'add') {
                    url = "{{ route('bitanic.farmer-group.update', 'ID') }}".replace('ID', document.getElementById(
                        'data-input-id').value)
                    formData.append('_method', 'PUT')
                } else {
                    url = "{{ route('bitanic.farmer-group.store') }}"
                }

                const settings = {
                    method: 'POST',
                    headers: {
                        'x-csrf-token': '{{ csrf_token() }}'
                    },
                    body: formData
                }

                const [data, error] = await yourRequest(url, settings)

                myModal.toggle()

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
                modalTitle.textContent = 'Foto'

                for (let index = 0; index < button.attributes.length; index++) {
                    if (button.attributes[index].nodeName.includes('data-foto')) {
                        document.getElementById('iframe').src = button.attributes[index].nodeValue
                    }
                }

            })

            const destroyHarvestProduce = (id, name) => {
                handleDeleteRows(
                    "{{ route('bitanic.harvest-produce.destroy', ['farmer' => $farmer, 'garden' => $garden, 'harvest_produce' => 'ID']) }}"
                    .replace('ID', id), "{{ csrf_token() }}", name)
            }

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

            });
        </script>
    @endpush
</x-app-layout>
