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
                        <div class="alert alert-info" role="alert">Foto <b>TIDAK WAJIB</b> diisi!</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="data-input-nama" class="form-label">Nama Mitra</label>
                        <input type="text" id="data-input-nama" class="form-control" name="nama" />
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="data-input-kontak" class="form-label">Nomor Kontak</label>
                        <input type="numeric" id="data-input-kontak" class="form-control" name="kontak" />
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="data-input-tipe-kemitraan" class="form-label">Tipe Kemitraan</label>
                        <input type="text" id="data-input-tipe-kemitraan" class="form-control"
                            name="tipe_kemitraan" />
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
                        <label for="data-input-tanggal-bergabung" class="form-label">Tanggal Bergabung</label>
                        <input type="date" id="data-input-tanggal-bergabung" class="form-control"
                            name="tanggal_bergabung" />
                    </div>
                    <div class="col mb-3">
                        <label for="data-input-tanggal-kontrak" class="form-label">Tanggal Kontrak</label>
                        <input type="date" id="data-input-tanggal-kontrak" class="form-control"
                            name="tanggal_kontrak" />
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
