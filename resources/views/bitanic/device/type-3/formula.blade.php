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
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Menu / <a href="{{ route('bitanic.device.index') }}">Perangkat</a> / <a href="{{ route('bitanic.v3-device.show', $device->id) }}">{{ $device->device_series }}</a> /</span> List Formula</h4>
    </x-slot>
    {{-- End Header --}}

    @if (session()->has('success'))
    <x-alert-message class="alert-success">{{ session()->get('success') }}</x-alert-message>
    @endif

    <div class="row d-flex justify-content-center">
        @if ($device->type == 3)
        <div class="col-12">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <h3 class="card-title p-3">List Formula yang dibuat</h3>
                        <div class="text-wrap table-change-scroll flipped mb-3">
                            <table class="table table-striped content">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Timestamp</th>
                                        <th>Jenis Tanaman</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0" id="formula-data">
                                    @forelse ($formulas as $formula)
                                    <tr>
                                        <td>{{ ($loop->iteration + (($formulas->currentPage() - 1) * 10)) }}</td>
                                        <td>{{ $formula->created_at }}</td>
                                        <td>{{ $formula->crop->crop_name }}</td>
                                        <td>
                                            @if (!$device->fertilization)
                                                <a href="{{ route('bitanic.v3-device.output', ['device' => $device->id, 'formula' => $formula->id]) }}"
                                                        class="btn btn-primary" title="Hasil Kalkulasi Formula">Hasil Kalkulasi</a>
                                            @endif
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

                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-start ps-3">
                                {{ $formulas->links() }}
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    @push('scripts')
    <script>

        const deleteData = async (eButton, perangkatId, formulaId) => {
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
                    let url = "";
                    const formData = new FormData();

                    url = url.replace('PID', perangkatId)
                    url = url.replace('FID', formulaId)

                    const formSubmited = await axios.delete(url, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })

                    const response = formSubmited.data.message

                    // Remove loading indication
                    eButton.removeAttribute('data-kt-indicator');

                    // Enable button
                    eButton.disabled = false;

                    Swal.fire({
                        text: response,
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
                    deleteData(e.target, e.target.dataset.perangkatId, e.target.dataset.formulaId)
                }
            })
        }
    </script>
    @endpush
</x-app-layout>
