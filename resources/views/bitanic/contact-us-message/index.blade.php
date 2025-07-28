<x-app-layout>
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Setting /</span> Pesan dari Kontak Kami</h4>
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
                            <form action="{{ route('bitanic.contact-us-message.index') }}" method="GET" id="form-search">
                                <div class="row p-1">
                                    <div class="col-md-12 m-1">
                                        <div class="input-group">
                                            <span class="input-group-text bg-primary text-white" style="cursor: pointer;" onclick="document.getElementById('form-search').submit()">
                                                <i class="bx bx-search"></i>
                                            </span>
                                            <input type="text" class="form-control shadow-none"
                                                placeholder="Cari nama..." aria-label="Cari nama..." name="search"
                                                value="{{ request()->query('search') }}" />
                                        </div>
                                    </div>
                                    <div class="col-md-12 m-1">
                                        <div class="input-group">
                                            <span class="input-group-text bg-primary text-white"><small>Status</small></span>
                                            <select class="form-select input-search" id="select-status" name="status" aria-label="Default select example">
                                                <option value="all" @if(!in_array(request()->query('status'), ['sudah','belum'])) selected @endif>Semua Status</option>
                                                <option value="sudah" @if(request()->query('status') == 'sudah') selected @endif>Sudah Dibalas</option>
                                                <option value="belum" @if(request()->query('status') == 'belum') selected @endif>Belum Dibalas</option>
                                            </select>
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
                            <button class="btn btn-danger" onclick="document.getElementById('form-delete-all').submit()">Hapus Semua</button>
                            <form action="{{ route('bitanic.contact-us-message.destroy-all') }}" id="form-delete-all" method="POST">
                                @method('DELETE')
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
                <div class="table-responsive text-wrap">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tanggal</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Pesan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($data as $message)
                                <tr>
                                    <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                                    <td>{{ carbon_format_id_flex(now()->parse($message->created_at)->format('d-m-Y'), '-', '/') }}</td>
                                    <td>{{ $message->name }}</td>
                                    <td>{{ $message->email }}</td>
                                    <td>{{ Str::limit($message->message, 20, '...') }}
                                        <span
                                            data-bs-toggle="tooltip"
                                            data-bs-offset="0,4"
                                            data-bs-placement="right"
                                            data-bs-html="true"
                                            title="<i class='bx bx-trending-up bx-xs' ></i> <span>Klik Untuk Lihat Pesan</span>">
                                            <i class='bx bx-info-circle' style="cursor: pointer;" data-bs-toggle="popover"
                                                data-bs-offset="0,14" data-bs-placement="top" data-bs-html="true"
                                                data-bs-content="<p>{{ $message->message }}</p>" title="Pesan"></i>
                                        </span>
                                    </td>
                                    <td>
                                        <span @class([
                                            'badge',
                                            'bg-label-danger' => $message->status == 0,
                                            'bg-label-success' => $message->status == 1,
                                        ])>@if($message->status == 0) Belum Dibalas @else Sudah Dibalas @endif</span>
                                    </td>
                                    <td>
                                        @if ($message->status == 0)
                                            <a
                                                href="{{ route('bitanic.contact-us-message.create-message', $message->id) }}"
                                                class="btn btn-icon btn-sm btn-primary" title="Balas Pesan">
                                                <i class="bx bx-message-edit"></i>
                                            </a>
                                        @endif
                                        <button type="button" class="btn btn-icon btn-danger btn-sm btn-delete"
                                            data-id="{{ $message->id }}" data-name="{{ $message->name }}"
                                            title="Hapus Pesan">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center" colspan="7">Tidak ada data</td>
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

    @push('scripts')
        <script src="{{ asset('theme/js/ui-popover.js') }}"></script>
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

                const btnDelete = document.querySelectorAll('.btn-delete')

                btnDelete.forEach(element => {
                    element.addEventListener('click', e => {
                        handleDeleteRows("{{ route('bitanic.contact-us-message.destroy', 'ID') }}".replace('ID', e.currentTarget.dataset.id), "{{ csrf_token() }}", e.currentTarget.dataset.name)
                    })
                });
            });

            const changeStatus = () => {
                const buttonStatus = document.querySelectorAll('.btn-change-status')

                buttonStatus.forEach(element => {
                    element.addEventListener('click', async event => {
                        try {
                            const id = event.target.dataset.id

                            const result = await Swal.fire({
                                text: "Apakah anda yakin ingin mengubah status?",
                                icon: "warning",
                                showCancelButton: true,
                                buttonsStyling: false,
                                confirmButtonText: "Yes, change!",
                                cancelButtonText: "No, cancel",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-success",
                                    cancelButton: "btn fw-bold btn-active-light-primary"
                                }
                            })

                            if (result.value) {
                                Swal.fire({
                                    html: '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span class=""> Loading...</span>',
                                    showConfirmButton: false,
                                    allowOutsideClick: false,
                                });
                                // Simulate delete request -- for demo purpose only
                                const url = "{{ route('bitanic.contact-us-message.change-status', 'ID') }}"
                                let newUrl = url.replace('ID', id)
                                const formData = {}

                                let response = await fetch(newUrl, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json;charset=utf-8',
                                        'x-csrf-token': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify(formData)
                                })

                                let result = await response.json()

                                Swal.fire({
                                    text: "Kamu berhasil mengubah status!.",
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok",
                                    customClass: {
                                        confirmButton: "btn fw-bold btn-primary",
                                    }
                                }).then(function() {
                                    // delete row data from server and re-draw datatable

                                    if (result.status == 1) {
                                        event.target.textContent = 'Sudah Dibalas'
                                        $(event.target).removeClass('btn-danger');
                                        $(event.target).addClass('btn-success');
                                    } else {
                                        event.target.textContent = 'Belum Dibalas'
                                        $(event.target).removeClass('btn-success');
                                        $(event.target).addClass('btn-danger');
                                    }
                                });
                            }
                        } catch (error) {
                            let errorMessage = error

                            if (error.hasOwnProperty('response')) {
                                if (error.response.status == 422) {
                                    errorMessage = 'Data yang dikirim tidak sesuai'
                                }
                            }

                            Swal.fire({
                                text: errorMessage,
                                icon: "error",
                                buttonsStyling: false,
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            });
                        }
                    })
                });
            }

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");
                changeStatus()

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
