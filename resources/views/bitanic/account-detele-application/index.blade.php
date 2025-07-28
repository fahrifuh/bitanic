<x-app-layout>
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Setting /</span> Data Request Hapus Akun</h4>
    </x-slot>
    {{-- End Header --}}
    <div class="row">
        <div class="col-md-12">
            <!-- Striped Rows -->
            <div class="card">
                <div class="row">
                    <div class="col-md-12">
                        <div class="float-start">
                            <h5 class="card-header"></h5>
                        </div>
                        <div class="float-end m-3">
                        </div>
                    </div>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Alasan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($data as $account)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $account->user->name }}</td>
                                    <td>{{ Str::limit($account->reason, 20, '...') }}
                                        <i class='bx bx-info-circle' style="cursor: pointer;" data-bs-toggle="popover"
                                            data-bs-offset="0,14" data-bs-placement="top" data-bs-html="true"
                                            data-bs-content="<p>{{ $account->reason }}</p>" title="Alasan"></i>
                                    </td>
                                    <td>
                                        <button type="button" onclick="AcceptRequest({{ $account }})"
                                            class="btn btn-primary btn-sm">Terima Permintaan</button>
                                        <button type="button" onclick="declineRequest({{ $account }})"
                                            class="btn btn-danger btn-sm">Hapus Permintaan</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data</td>
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

            async function AcceptRequest(data) {
                const result = await Swal.fire({
                    text: "Akun yang dihapus tidak dapat dikembalikan, dan semua data yang berhubungan akan hilang",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Yes, delete!",
                    cancelButtonText: "No, cancel",
                    customClass: {
                        confirmButton: "btn fw-bold btn-danger",
                        cancelButton: "btn fw-bold btn-active-light-primary"
                    }
                })

                if (result.value) {
                    showSpinner()
                    const formData = new FormData();
                    // Simulate delete request -- for demo purpose only
                    const url = "{{ route('bitanic.account-delete-request.accept') }}"

                    formData.append("id", data.id)

                    const settings = {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'x-csrf-token': '{{ csrf_token() }}'
                        },
                        body: formData
                    }

                    const [res, error] = await yourRequest(url, settings)

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
                        text: "Kamu berhasil menghapus akun!.",
                        icon: "success",
                        showConfirmButton: false,
                        allowOutsideClick: false,
                    })
                    window.location.reload();
                }

            }

            async function declineRequest(data) {
                console.log(data);
                const result = await Swal.fire({
                    text: "Request yang dihapus tidak dapat dikembalikan!",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Yes, delete!",
                    cancelButtonText: "No, cancel",
                    customClass: {
                        confirmButton: "btn fw-bold btn-danger",
                        cancelButton: "btn fw-bold btn-active-light-primary"
                    }
                })

                if (result.value) {
                    showSpinner()
                    const formData = new FormData();
                    // Simulate delete request -- for demo purpose only
                    const url = "{{ route('bitanic.account-delete-request.decline') }}"

                    formData.append("id", data.id)

                    const settings = {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'x-csrf-token': '{{ csrf_token() }}'
                        },
                        body: formData
                    }

                    const [res, error] = await yourRequest(url, settings)

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
                        text: "Kamu berhasil request akun!.",
                        icon: "success",
                        showConfirmButton: false,
                        allowOutsideClick: false,
                    })
                    window.location.reload();
                }
            }
        </script>
    @endpush
</x-app-layout>
