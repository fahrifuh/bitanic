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
                        <label for="data-input-nama" class="form-label">Nama Investor</label>
                        <input type="text" id="data-input-nama" class="form-control" name="nama" />
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="data-input-nama-investasi" class="form-label">Nama Investasi</label>
                        <input type="text" id="data-input-nama-investasi" class="form-control" name="nama_investasi" />
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="data-input-nomor-perjanjian" class="form-label">Nomor Perjanjian</label>
                        <input type="text" id="data-input-nomor-perjanjian" class="form-control" name="nomor_perjanjian" />
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="data-input-tanggal-perjanjian" class="form-label">Tanggal Perjanjian</label>
                        <input type="date" id="data-input-tanggal-perjanjian" class="form-control"
                            name="tanggal_perjanjian" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Tutup
                    </button>
                    <button type="submit" class="btn btn-primary" id="submit-btn">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</div>
