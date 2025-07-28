<!-- Modal -->
<div class="modal fade" id="modalFinishHarvest" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFinishHarvestTitle">Buat data hasil panen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @csrf
                <div class="row g-2 mb-3">
                    <div class="col mb-0">
                        <label for="data-input-hasil-panen" class="form-label">Hasil Panen <span class="text-danger">* wajib diisi</span></label>
                        <input type="number" step="0.1" min="0" class="form-control" id="data-input-hasil-panen"
                            name="hasil_panen" />
                    </div>
                    <div class="col mb-0">
                        <label for="data-input-satuan-panen" class="form-label">Satuan <span class="text-danger">* wajib diisi</span></label>
                        <select class="form-select" id="data-input-satuan-panen" name="satuan"
                            aria-label="Default select example">
                            <option value="kuintal">Kuintal</option>
                            <option value="kg">Kg</option>
                            <option value="ton">Ton</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="data-input-catatan" class="form-label">Catatan</label>
                        <textarea class="form-control" id="data-input-catatan" name="catatan" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Tutup
                    </button>
                    <button type="submit" class="btn btn-primary" id="btn-store-panen" data-garden="#">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</div>
