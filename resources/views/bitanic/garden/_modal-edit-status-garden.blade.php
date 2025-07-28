<!-- Modal -->
<div class="modal fade" id="modalEditStatusGarden" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditStatusGardenTitle">Edit Status Kebun</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @csrf
                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label">Status</label>

                        <br>

                        @php
                            $days = [
                                'State' => 0,
                                'Sedang Menanam' => 1,
                                'Masa Pemeliharaan' => 2,
                                'Masa Panen' => 3
                            ];
                        @endphp

                        @foreach ($days as $status => $value)
                            <div class="form-check form-check-inline mt-2">
                                <input class="form-check-input data-input-status-kebun" type="radio" value="{{ $value }}" id="status-{{ $value }}" name="status" />
                                <label class="form-check-label" for="status-{{ $value }}"> {{ $status }} </label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Tutup
                    </button>
                    <button type="submit" class="btn btn-primary" id="btn-edit-status-garden" data-id="#">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</div>
