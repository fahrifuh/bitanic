<x-app-layout>
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master /</span> Data Tanaman</h4>
    </x-slot>
    {{-- End Header --}}


    @push('styles')
        <style>
            .event-none {
                pointer-events: none;
            }
        </style>
    @endpush

    <div class="row">
        <div class="col-md-12">
            <!-- Striped Rows -->
            <div class="card">
                <div class="row">
                    <div class="col-md-12">
                        <div class="float-start">
                            <!-- Search -->
                            <form action="{{ route('bitanic.crop.index') }}" method="GET" id="form-search">
                                <div class="row g-2 p-3 pb-1">
                                    <div class="col-12">
                                        <div class="input-group">
                                            <span class="input-group-text bg-primary text-white"
                                                style="cursor: pointer;"
                                                onclick="document.getElementById('form-search').submit()">
                                                <i class="bx bx-search"></i>
                                            </span>
                                            <input type="text" class="form-control shadow-none"
                                                placeholder="Cari nama..." aria-label="Cari nama..." name="search"
                                                value="{{ request()->query('search') }}" />
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="input-group flex-nowrap">
                                            <span class="input-group-text bg-primary text-white"><small>Jenis</small></span>
                                            <select class="form-select" style="width: 100%;" id="select-jenis" name="jenis"
                                                aria-label="Default select example">
                                                <option value="all" @if(!in_array(request()->query('jenis'), ['sayur', 'buah'])) selected @endif>Semua Jenis</option>
                                                <option value="sayur" @if(request()->query('jenis') == 'sayur') selected @endif>Sayur</option>
                                                <option value="buah" @if(request()->query('jenis') == 'buah') selected @endif>Buah</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="input-group flex-nowrap">
                                            <span class="input-group-text bg-primary text-white"><small>Musim</small></span>
                                            <select class="form-select" style="width: 100%;" id="select-musim" name="musim"
                                                aria-label="Default select example">
                                                <option value="all" @if(!in_array(request()->query('musim'), ['hujan', 'kemarau'])) selected @endif>Semua Musim</option>
                                                <option value="hujan" @if(request()->query('musim') == 'hujan') selected @endif>Hujan</option>
                                                <option value="kemarau" @if(request()->query('musim') == 'kemarau') selected @endif>Kemarau</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <!-- /Search -->
                        </div>
                        <div class="float-end m-3">
                            <button
                                  type="button"
                                  class="btn btn-primary"
                                  data-bs-toggle="modal"
                                  data-bs-target="#modalForm"
                                  data-input-nama-tanaman=""
                                  data-input-jenis="sayur"
                                  data-input-musim="hujan"
                                  data-input-price=""
                                  data-input-price-description=""
                                  data-input-suhu-optimum=""
                                  data-input-suhu-minimum=""
                                  data-input-suhu-maximum=""
                                  data-input-kelembapan-optimum=""
                                  data-input-kelembapan-minimum=""
                                  data-input-kelembapan-maximum=""
                                  data-input-ketinggian=""
                                  data-input-deskripsi=""
                                  data-input-target-ph=""
                                  data-input-target-persen-corganik=""
                                  data-input-frekuensi-siram=""
                                  data-input-n-kg-ha=""
                                  data-input-sangat-rendah-p2o5=""
                                  data-input-rendah-p2o5=""
                                  data-input-sedang-p2o5=""
                                  data-input-tinggi-p2o5=""
                                  data-input-sangat-tinggi-p2o5=""
                                  data-input-sangat-rendah-k2o=""
                                  data-input-rendah-k2o=""
                                  data-input-sedang-k2o=""
                                  data-input-tinggi-k2o=""
                                  data-input-sangat-tinggi-k2o=""
                                  data-input-catatan=""
                                  data-input-id="add"
                                  title="Tambah Tanaman"
                                >
                                <i class="bx bx-plus"></i>&nbsp;Tambah
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive text-wrap">
                  <table class="table table-striped" id="table-crops">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Nama Tanaman</th>
                        <th>Jenis</th>
                        <th>Musim</th>
                        <th>Harga Pasar</th>
                        <th>Suhu <br> (Minimum/Optimum/Maximum)</th>
                        <th>Kelembapan <br> (Minimum/Optimum/Maximum)</th>
                        <th>Deskripsi</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($data as $crop)

                            <tr>
                                <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                                <td>{{ $crop->crop_name }}</td>
                                <td>{{ ucfirst($crop->type) }}</td>
                                <td>{{ ucfirst($crop->season) }}</td>
                                <td>Rp&nbsp;{{ number_format($crop->price, 2, ',', '.') }}
                                    <i class='bx bx-info-circle text-info' style="cursor: pointer;" data-bs-toggle="popover"
                                                data-bs-offset="0,14" data-bs-placement="top" data-bs-html="true"
                                                data-bs-content="<p>{{ $crop->price_description }}</p>" title="Penjelasan harga"></i>
                                </td>
                                <td><span class="text-info">{{ $crop->minimum_temperature }}°C</span>&nbsp;/&nbsp;<span class="text-success">{{ $crop->optimum_temperature }}°C</span>&nbsp;/&nbsp;<span class="text-danger">{{ $crop->maximum_temperature }}°C</span></td>
                                <td><span class="text-info">{{ $crop->moisture->minimum }}%</span>&nbsp;/&nbsp;<span class="text-success">{{ $crop->moisture->optimum }}%</span>&nbsp;/&nbsp;<span class="text-danger">{{ $crop->moisture->maximum }}%</span></td>
                                <td>
                                    {{ Str::limit($crop->description, 15, '...') }}
                                    <span
                                        data-bs-toggle="tooltip"
                                        data-bs-offset="0,4"
                                        data-bs-placement="right"
                                        data-bs-html="true"
                                        title="<i class='bx bx-trending-up bx-xs' ></i> <span>Klik Untuk Lihat Deskripsi</span>">
                                        <i class='bx bx-info-circle' style="cursor: pointer;" data-bs-toggle="popover"
                                            data-bs-offset="0,14" data-bs-placement="top" data-bs-html="true"
                                            data-bs-content="<p>{{ $crop->description }}</p>" title="Deskripsi"></i>
                                    </span>
                                </td>
                                <td>
                                    <button
                                          type="button"
                                          class="btn btn-info btn-icon btn-sm mb-1"
                                          data-bs-toggle="modal"
                                          data-bs-target="#modalDetail"
                                          onclick="cropDetail({{ $crop->id }}, '{{ asset($crop->picture ?? 'bitanic-landing/default-image.jpg') }}')"
                                          title="Detail Tanaman"
                                        >
                                        <i class="bx bx-list-ul"></i>
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-warning btn-icon btn-sm mb-1"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalForm"
                                        data-input-nama-tanaman="{{ $crop->crop_name }}"
                                        data-input-jenis="{{ $crop->type }}"
                                        data-input-musim="{{ $crop->season }}"
                                        data-input-price="{{ $crop->price }}"
                                        data-input-price-description="{{ $crop->price_description }}"
                                        data-input-suhu-optimum="{{ $crop->optimum_temperature }}"
                                        data-input-suhu-minimum="{{ $crop->minimum_temperature }}"
                                        data-input-suhu-maximum="{{ $crop->maximum_temperature }}"
                                        data-input-kelembapan-optimum="{{ optional($crop->moisture)->optimum }}"
                                        data-input-kelembapan-minimum="{{ optional($crop->moisture)->minimum }}"
                                        data-input-kelembapan-maximum="{{ optional($crop->moisture)->maximum }}"
                                        data-input-ketinggian="{{ $crop->altitude }}"
                                        data-input-deskripsi="{{ $crop->description }}"
                                        data-input-target-ph="{{ $crop->target_ph }}"
                                        data-input-target-persen-corganik="{{ $crop->target_persen_corganik }}"
                                        data-input-frekuensi-siram="{{ $crop->frekuensi_siram }}"
                                        data-input-n-kg-ha="{{ $crop->n_kg_ha }}"
                                        data-input-sangat-rendah-p2o5="{{ $crop->sangat_rendah_p2o5 }}"
                                        data-input-rendah-p2o5="{{ $crop->rendah_p2o5 }}"
                                        data-input-sedang-p2o5="{{ $crop->sedang_p2o5 }}"
                                        data-input-tinggi-p2o5="{{ $crop->tinggi_p2o5 }}"
                                        data-input-sangat-tinggi-p2o5="{{ $crop->sangat_tinggi_p2o5 }}"
                                        data-input-sangat-rendah-k2o="{{ $crop->sangat_rendah_k2o }}"
                                        data-input-rendah-k2o="{{ $crop->rendah_k2o }}"
                                        data-input-sedang-k2o="{{ $crop->sedang_k2o }}"
                                        data-input-tinggi-k2o="{{ $crop->tinggi_k2o }}"
                                        data-input-sangat-tinggi-k2o="{{ $crop->sangat_tinggi_k2o }}"
                                        data-input-catatan="{{ $crop->catatan }}"
                                        data-input-id="{{ $crop->id }}"
                                        title="Edit Tanaman"
                                        >
                                        <i class="bx bx-edit-alt"></i>
                                    </button>
                                    <button type="button" data-id="{{ $crop->id }}" data-name="{{ $crop->crop_name }}"
                                        class="btn btn-danger btn-icon btn-sm btn-delete mb-1" title="Hapus Tanaman">
                                        <i class="bx bx-trash event-none"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">Tidak ada data</td>
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

    @include('bitanic.crop._modal-form')
    @include('bitanic.crop._modal-show')

    @push('scripts')
        <script src="{{ asset('theme/js/ui-popover.js') }}"></script>
        <script>
            const myModal = new bootstrap.Modal(document.getElementById("modalForm"), {});
            const modal = document.getElementById('modalForm')
            modal.addEventListener('show.bs.modal', function (event) {
                // Button that triggered the modal
                const button = event.relatedTarget
                // Extract info from data-bs-* attributes
                // const recipient = button.getAttribute('data-bs-whatever')
                const modalTitle = modal.querySelector('.modal-title')

                for (let index = 0; index < button.attributes.length; index++) {
                    if (button.attributes[index].nodeName.includes('data-input')) {
                        document.getElementById(button.attributes[index].nodeName).value = button.attributes[index].nodeValue

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

                if (typeof myFoto !== 'undefined'){
                    formData.append("picture", myFoto)
                }

                formData.append("crop_name", document.getElementById('data-input-nama-tanaman').value)
                formData.append("type", document.getElementById('data-input-jenis').value)
                formData.append("season", document.getElementById('data-input-musim').value)
                formData.append("price", document.getElementById('data-input-price').value)
                formData.append("price_description", document.getElementById('data-input-price-description').value)
                formData.append("optimum_temperature", document.getElementById('data-input-suhu-optimum').value)
                formData.append("minimum_temperature", document.getElementById('data-input-suhu-minimum').value)
                formData.append("maximum_temperature", document.getElementById('data-input-suhu-maximum').value)
                formData.append("optimum_moisture", document.getElementById('data-input-kelembapan-optimum').value)
                formData.append("minimum_moisture", document.getElementById('data-input-kelembapan-minimum').value)
                formData.append("maximum_moisture", document.getElementById('data-input-kelembapan-maximum').value)
                formData.append("altitude", document.getElementById('data-input-ketinggian').value)
                formData.append("description", document.getElementById('data-input-deskripsi').value)
                formData.append("target_ph", document.getElementById('data-input-target-ph').value)
                formData.append("target_persen_corganik", document.getElementById('data-input-target-persen-corganik').value)
                formData.append("frekuensi_siram", document.getElementById('data-input-frekuensi-siram').value)
                formData.append("n_kg_ha", document.getElementById('data-input-n-kg-ha').value)
                formData.append("sangat_rendah_p2o5", document.getElementById('data-input-sangat-rendah-p2o5').value)
                formData.append("rendah_p2o5", document.getElementById('data-input-rendah-p2o5').value)
                formData.append("sedang_p2o5", document.getElementById('data-input-sedang-p2o5').value)
                formData.append("tinggi_p2o5", document.getElementById('data-input-tinggi-p2o5').value)
                formData.append("sangat_tinggi_p2o5", document.getElementById('data-input-sangat-tinggi-p2o5').value)
                formData.append("sangat_rendah_k2o", document.getElementById('data-input-sangat-rendah-k2o').value)
                formData.append("rendah_k2o", document.getElementById('data-input-rendah-k2o').value)
                formData.append("sedang_k2o", document.getElementById('data-input-sedang-k2o').value)
                formData.append("tinggi_k2o", document.getElementById('data-input-tinggi-k2o').value)
                formData.append("sangat_tinggi_k2o", document.getElementById('data-input-sangat-tinggi-k2o').value)
                formData.append("catatan", document.getElementById('data-input-catatan').value)

                if (editOrAdd.value != 'add') {
                    url = "{{ route('bitanic.crop.update', 'ID') }}".replace('ID', document.getElementById('data-input-id').value)
                    formData.append("_method", 'PUT')
                } else {
                    url = "{{ route('bitanic.crop.store') }}"
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

                if (error) {
                    deleteSpinner()
                    // Remove loading indication
                    submitButton.removeAttribute('data-kt-indicator');

                    // Enable button
                    submitButton.disabled = false;

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

                // Remove loading indication
                submitButton.removeAttribute('data-kt-indicator');

                // Enable button
                submitButton.disabled = false;

                Swal.fire({
                    text: "Kamu berhasil menyimpan data!.",
                    icon: "success",
                    buttonsStyling: false,
                    confirmButtonText: "Ok",
                    customClass: {
                        confirmButton: "btn fw-bold btn-primary",
                    }
                })
                window.location.reload();
            });

            function errorMessage(error) {
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
            }

            const cropDetail = async (id, picture) => {
                try {
                    const settings = {
                        method: 'GET',
                        headers: {
                            'x-csrf-token': '{{ csrf_token() }}'
                        },
                    }

                    const [data, error] = await yourRequest("{{ route('bitanic.crop.show', 'ID') }}"
                        .replace('ID', id), settings)

                    if (error) {
                        errorMessage(error)

                        return false
                    }

                    document.getElementById('iframe').src = picture

                    console.log(data);
                    let crop = data

                    document.querySelector('#modal-detail-name').textContent = crop.crop_name
                    document.querySelector('#modal-detail-jenis').textContent = crop.type
                    document.querySelector('#modal-detail-musim').textContent = crop.season
                    document.querySelector('#modal-detail-ketinggian').textContent = crop.altitude
                    document.querySelector('#modal-detail-harga').textContent = new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR'
                    }).format(crop.price)
                    document.querySelector('#modal-detail-harga-penjelasan').textContent = crop.price_description
                    document.querySelector('#modal-detail-deskripsi').textContent = crop.description

                    document.querySelector('#modal-detail-suhu-maximum').textContent = crop.maximum_temperature + " °C"
                    document.querySelector('#modal-detail-suhu-optimum').textContent = crop.optimum_temperature + " °C"
                    document.querySelector('#modal-detail-suhu-minimum').textContent = crop.minimum_temperature + " °C"

                    document.querySelector('#modal-detail-kelembapan-maximum').textContent = crop.moisture.maximum + " %"
                    document.querySelector('#modal-detail-kelembapan-optimum').textContent = crop.moisture.optimum + " %"
                    document.querySelector('#modal-detail-kelembapan-minimum').textContent = crop.moisture.minimum + " %"

                    document.querySelector('#modal-detail-target-ph').textContent = crop.target_ph
                    document.querySelector('#modal-detail-target-corganik').textContent = crop.target_persen_corganik
                    document.querySelector('#modal-detail-frekuensi-siram').textContent = crop.frekuensi_siram
                    document.querySelector('#modal-detail-n').textContent = crop.n_kg_ha

                    document.querySelector('#modal-detail-sangat-rendah-p2o5').textContent = crop.sangat_rendah_p2o5
                    document.querySelector('#modal-detail-rendah-p2o5').textContent = crop.rendah_p2o5
                    document.querySelector('#modal-detail-sedang-p2o5').textContent = crop.sedang_p2o5
                    document.querySelector('#modal-detail-tinggi-p2o5').textContent = crop.tinggi_p2o5
                    document.querySelector('#modal-detail-sangat-tinggi-p2o5').textContent = crop.sangat_tinggi_p2o5

                    document.querySelector('#modal-detail-sangat-rendah-k2o').textContent = crop.sangat_rendah_k2o
                    document.querySelector('#modal-detail-rendah-k2o').textContent = crop.rendah_k2o
                    document.querySelector('#modal-detail-sedang-k2o').textContent = crop.sedang_k2o
                    document.querySelector('#modal-detail-tinggi-k2o').textContent = crop.tinggi_k2o
                    document.querySelector('#modal-detail-sangat-tinggi-k2o').textContent = crop.sangat_tinggi_k2o
                } catch (error) {
                    errorMessage(error)
                    console.log(error);
                    return false
                }
            }

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

                document.querySelector('#table-crops tbody').addEventListener('click', (e) => {
                    if (e.target.classList.contains("btn-delete") && e.target.dataset?.id) {
                        e.preventDefault()
                        handleDeleteRows("{{ route('bitanic.crop.destroy', 'ID') }}".replace('ID', e.target.dataset.id), "{{ csrf_token() }}", e.target.dataset.name)
                    }
                })

                const selectType = document.querySelector('#select-jenis')
                selectType.addEventListener('change', e => {
                    document.getElementById('form-search').submit()
                })
                const selectSeason = document.querySelector('#select-musim')
                selectSeason.addEventListener('change', e => {
                    document.getElementById('form-search').submit()
                })
            });
        </script>
    @endpush
</x-app-layout>
