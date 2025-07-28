<!-- Modal -->
<div class="modal fade" id="modalSelect" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <form action="{{ route('bitanic.device.create') }}" method="GET">
          <div class="modal-header">
            <h5 class="modal-title" id="modalSelectTitle">Pilih Tipe dan Kategori Perangkat yang dibuat!</h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
              <div class="row">
                  <div class="col mb-3">
                        <label for="data-input-category" class="form-label">Kategori Perangkat</label>
                      <select
                          class="form-select"
                          id="data-input-category"
                          name="category"
                          onchange="typeChange(this)"
                      >
                          <option value="controller">Controller</option>
                          <option value="tongkat">Tongkat/RSC</option>
                      </select>
                  </div>
              </div>
              <div class="row">
                  <div class="col mb-3">
                        <label for="data-input-select-type" class="form-label">Tipe</label>
                      <select
                          class="form-select"
                          id="data-input-select-type"
                          name="type"
                      >
                          <option value="1">1</option>
                          <option value="2">2</option>
                          <option value="3">3</option>
                      </select>
                  </div>
              </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                Tutup
              </button>
              <button type="submit" class="btn btn-primary">Lanjut</button>
          </div>
        </form>
      </div>
    </div>
</div>
