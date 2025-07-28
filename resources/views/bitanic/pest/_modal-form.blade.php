<!-- Modal -->
<div class="modal fade" id="modalForm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
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
                <label for="data-input-name" class="form-label">Nama Hama</label>
                <input
                    type="text"
                    id="data-input-name"
                    class="form-control"
                    name="name"
                    autocomplete="off"
                    required
                />
                </div>
            </div>
            <div class="row">
                <div class="col mb-3">
                <label for="data-input-type" class="form-label">Jenis Hama</label>
                <input
                    type="text"
                    id="data-input-type"
                    class="form-control"
                    name="pest_type"
                    autocomplete="off"
                    required
                />
                </div>
            </div>
            <div class="row">
                <div class="col mb-3">
                    <label for="data-input-foto" class="form-label">Foto</label>
                    <input class="form-control" type="file" id="data-input-foto" name="foto" accept="image/png, image/jpg, image/jpeg" />
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            Close
            </button>
            <button type="button" class="btn btn-primary" id="submit-btn">Save</button>
        </div>
      </div>
    </div>
</div>
