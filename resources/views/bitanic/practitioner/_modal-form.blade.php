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
                <div class="row d-none" id="alert">
                    <div class="col mb-3">
                        <div class="bg-info text-white p-3 rounded" role="alert">Foto <b>TIDAK WAJIB</b> diisi!</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="data-input-nama" class="form-label">Nama Peneliti</label>
                        <input type="text" id="data-input-nama" class="form-control" name="nama" />
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="data-input-bidang-praktisi" class="form-label">Bidang Peneliti</label>
                        <input type="text" id="data-input-bidang-praktisi" class="form-control"
                            name="bidang_praktisi" />
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="data-input-institusi" class="form-label">Institusi</label>
                        <input type="text" id="data-input-institusi" class="form-control" name="institusi" />
                    </div>
                </div>
                <div class="row">
                  <div class="col mb-3">
                        <label for="data-input-foto" class="form-label">Foto</label>
                        <input class="form-control" type="file" id="data-input-foto" name="foto"
                            accept="image/png, image/jpg, image/jpeg" aria-describedby="pictureHelp" />
                        <div id="pictureHelp" class="form-text">Format gambar JPG, JPEG, PNG. Maks.
                            2MB</div>
                  </div>
                </div>
                <div class="row">
                  <div class="col mb-3">
                      <label for="data-input-alamat" class="form-label">Alamat</label>
                      <textarea class="form-control" id="data-input-alamat" name="alamat" rows="2" placeholder="Jl. XXX"></textarea>
                  </div>
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
