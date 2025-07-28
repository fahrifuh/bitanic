<x-app-layout>

    @push('styles')
    <style>
        .table-change-scroll {
            width: 100%;
            overflow-y: auto;
        }

        .flipped,
        .flipped .content {
            transform: rotateX(180deg);
            -ms-transform: rotateX(180deg);
            /* IE 9 */
            -webkit-transform: rotateX(180deg);
            /* Safari and Chrome */
        }
    </style>
    @endpush
    {{-- Header --}}
    <x-slot name="header">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master / </span> Formula</h4>
    </x-slot>
    {{-- End Header --}}

    @if (session()->has('success'))
    <x-alert-message class="alert-success">{{ session()->get('success') }}</x-alert-message>
    @endif

    <div class="row d-flex justify-content-center">
        <div class="col-12">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="row">
                            <div class="col-12">
                                <div class="float-start">
                                    <!-- Search -->
                                    <form action="" method="GET" id="form-search">
                                        <div class="row g-2 p-3 pb-1">
                                            <div class="col-12">
                                                <div class="input-group">
                                                    <span class="input-group-text bg-primary text-white"
                                                        style="cursor: pointer;"
                                                        onclick="document.getElementById('form-search').submit()">
                                                        <i class="bx bx-search"></i>
                                                    </span>
                                                    <input type="text" class="form-control shadow-none"
                                                        placeholder="Cari jenis tanaman..." aria-label="Cari jenis tanaman..." name="search"
                                                        value="{{ request()->query('search') }}" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <!-- /Search -->
                                </div>
                                <div class="float-end p-3">
                                    <a href="{{ route('bitanic.formula.create') }}"
                                        class="btn btn-primary"><i class="bx bx-plus"
                                        title="Tambah Formula"></i>&nbsp;Tambah</a>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="text-wrap table-change-scroll flipped mb-3">
                                    <table class="table table-striped content">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%;">#</th>
                                                <th style="width: 15%;">Timestamp</th>
                                                <th>Jenis Tanaman</th>
                                                <th style="width: 5%;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0" id="formula-data">
                                            @forelse ($formulas as $formula)
                                            <tr>
                                                <td>{{ ($loop->iteration + (($formulas->currentPage() - 1) * 10)) }}</td>
                                                <td>{{ $formula->created_at }}</td>
                                                <td>{{ $formula->crop->crop_name }}</td>
                                                <td>
                                                    <div class="d-flex flex-row gap-2">
                                                        <a href="{{ route('bitanic.formula.show', $formula->id) }}"
                                                            class="btn btn-icon btn-primary"
                                                            title="Hasil Formulasi">
                                                            <i class="bx bx-list-ul"></i>
                                                        </a>
                                                        <button type="button"
                                                            class="btn btn-icon btn-danger btn-delete-formula"
                                                            data-formula-id="{{ $formula->id }}"
                                                            title="Hapus Formula">
                                                            <i class="bx bx-trash event-none"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="7" class="text-center">Tidak ada data</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-12">
                                <nav aria-label="Page navigation">
                                    <ul class="pagination justify-content-start ps-3">
                                        {{ $formulas->links() }}
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>

        const deleteData = async (eButton, formulaId) => {
            try {
                // Show loading indication
                eButton.setAttribute('data-kt-indicator', 'on');

                // Disable button to avoid multiple click
                eButton.disabled = true;

                const result = await Swal.fire({
                    text: "Data formula akan dihapus dan tidak dapat dikembalikan. Apakah anda yakin?",
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

                if (result.value) {
                    // Simulate form submission. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                    let url = "{{ route('bitanic.formula.destroy', 'FID') }}";
                    const formData = new FormData();

                    url = url.replace('FID', formulaId)

                    const settings = {
                        method: 'DELETE',
                        headers: {
                            'x-csrf-token': "{{ csrf_token() }}",
                            'Accept': "application/json",
                        }
                    }

                    const [data, error] = await yourRequest(
                        url, settings
                    )

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

                    // Remove loading indication
                    eButton.removeAttribute('data-kt-indicator');

                    // Enable button
                    eButton.disabled = false;

                    Swal.fire({
                        text: 'Berhasil dihapus',
                        icon: "success",
                    });

                    window.location.reload();
                } else {
                    // Remove loading indication
                    eButton.removeAttribute('data-kt-indicator');

                    // Enable button
                    eButton.disabled = false;

                    Swal.fire({
                        text: 'Penghapusan tidak dilaksanakan.',
                        icon: "failed",
                    });
                }

            } catch (error) {
                console.error(error);

                // Remove loading indication
                eButton.removeAttribute('data-kt-indicator');

                // Enable button
                eButton.disabled = false;
            }
        }

        window.onload = () => {
            document.querySelector('#formula-data').addEventListener('click', e => {
                if (e.target.type === "button" && e.target.classList.contains("btn-delete-formula")) {
                    // console.dir(e.target.dataset)
                    deleteData(e.target, e.target.dataset.formulaId)
                }
            })
        }
    </script>
    @endpush
</x-app-layout>
