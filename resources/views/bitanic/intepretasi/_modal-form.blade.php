<!-- Modal -->
<div class="modal fade" id="modalForm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalFormTitle">Modal title</h5>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"
          ></button>
        </div>
        <div class="modal-body">
            @csrf
            <input type="hidden" name="id" id="data-input-id">
            <div class="row">
                <div class="col mb-3">
                <label for="data-input-unsur" class="form-label">Unsur</label>
                <input
                    type="text"
                    id="data-input-unsur"
                    class="form-control"
                    name="unsur"
                    readonly
                />
                </div>
            </div>
            <div class="row">
                <div class="col mb-3">
                    <label for="data-input-sangat-rendah" class="form-label">Sangat Rendah*</label>
                    <div class="input-group">
                        <span class="input-group-text"> < </span>
                        <input
                            type="number"
                            step="0.1"
                            id="data-input-sangat-rendah"
                            class="form-control"
                            name="sangat_rendah_first"
                        >
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col mb-3">
                    <label for="data-input-rendah" class="form-label">Rendah</label>
                    <div class="input-group">
                        <span class="input-group-text" id="symbol-rendah-first"> &lt; </span>
                        <input
                            type="number"
                            step="0.1"
                            id="data-input-rendah-first"
                            class="form-control"
                            name="rendah_first"
                        >
                        <span class="input-group-text"> s.d </span>
                        <span class="input-group-text" id="symbol-rendah-second"> s.d </span>
                        <input
                            type="number"
                            step="0.1"
                            id="data-input-rendah-second"
                            class="form-control"
                            name="rendah_second"
                            readonly
                        >
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col mb-3">
                    <label for="data-input-sedang" class="form-label">Sedang</label>
                    <div class="input-group">
                        <span class="input-group-text"> &ge; </span>
                        <input
                            type="number"
                            step="0.1"
                            id="data-input-sedang-first"
                            class="form-control"
                            name="sedang_first"
                            readonly
                        >
                        <span class="input-group-text"> s.d </span>
                        <input
                            type="number"
                            step="0.1"
                            id="data-input-sedang-second"
                            class="form-control"
                            name="sedang_second"
                        >
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col mb-3">
                    <label for="data-input-tinggi" class="form-label">Tinggi</label>
                    <div class="input-group">
                        <span class="input-group-text" id="symbol-tinggi-first"> &gt; </span>
                        <input
                            type="number"
                            step="0.1"
                            id="data-input-tinggi-first"
                            class="form-control"
                            name="tinggi_first"
                            readonly
                        >
                        <span class="input-group-text"> s.d </span>
                        <span class="input-group-text" id="symbol-tinggi-second"> s.d </span>
                        <input
                            type="number"
                            step="0.1"
                            id="data-input-tinggi-second"
                            class="form-control"
                            name="tinggi_second"
                        >
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col mb-3">
                    <label for="data-input-sangat-tinggi" class="form-label">Sangat Tinggi</label>
                    <div class="input-group">
                        <span class="input-group-text"> &gt; </span>
                        <input
                            type="number"
                            step="0.1"
                            id="data-input-sangat-tinggi"
                            class="form-control"
                            name="sangat_tinggi_first"
                            readonly
                        >
                    </div>
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
