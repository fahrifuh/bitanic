<!-- Modal -->
<div class="modal fade" id="modalForm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFormTitle">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @csrf
                <input type="hidden" name="id" id="data-input-id">
                <div class="row">
                    <div class="col mb-3">
                        <label for="data-input-subdis-name" class="form-label">Nama Desa</label>
                        <input type="text" id="data-input-subdis-name" class="form-control" name="subdis_name"
                            required />
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="data-input-dis-id" class="form-label">Kecamatan</label>
                        <br>
                        <select class="form-select" id="data-input-dis-id" name="dis_id"
                            aria-label="Default select example">
                            @forelse ($districts as $id => $dis_name)
                                <option value="{{ $id }}">{{ $dis_name }}</option>
                            @empty
                                <option value="" disabled>Tidak ada data</option>
                            @endforelse
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Tutup
                </button>
                <button type="button" class="btn btn-primary" id="submit-btn">Simpan</button>
            </div>
        </div>
    </div>
</div>
