<!-- Modal -->
<div class="modal fade" id="modalSelisihKebutuhan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalSelisihKebutuhanTitle">Modal title</h5>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"
          ></button>
        </div>
        <div class="modal-body">
            @csrf
            <input type="hidden" name="id" id="data-input-id-selisih">
            <div class="row g-2 mb-3">
              <div class="col mb-0">
                <label for="data-input-selisih-ph" class="form-label">Selisih PH</label>
                <input
                    type="number"
                    step="0.1"
                    id="data-input-selisih-ph"
                    class="form-control"
                    name="selisih_ph"
                    required
                />
              </div>
              <div class="col mb-0">
                <label for="data-input-kebutuhan-dolomit" class="form-label">Kebutuhan dolomit (<span class="text-lowercase">ton</span>/<span class="text-lowercase">ha</span>)</label>
                <input
                    type="number"
                    step="0.1"
                    id="data-input-kebutuhan-dolomit"
                    class="form-control"
                    name="fekuensi_siram"
                    required
                />
              </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            Tutup
            </button>
            <button type="button" class="btn btn-primary" id="submit-btn-selisih">Simpan</button>
        </div>
      </div>
    </div>
</div>
