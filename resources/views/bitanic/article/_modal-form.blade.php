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
                        <label for="data-input-tanggal" class="form-label">Tanggal</label>
                        <input type="date" id="data-input-tanggal" class="form-control" name="tanggal" />
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
                        <label for="data-input-tipe" class="form-label">Tipe</label>
                        <select class="form-select" id="data-input-tipe" name="tipe"
                            aria-label="Default select example">
                            <option value="sayuran">Sayuran</option>
                            <option value="buah">Buah</option>
                            <option value="umum">Umum</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="data-input-judul" class="form-label">Judul Artikel</label>
                        <input type="text" id="data-input-judul" class="form-control" name="judul_artikel" />
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="data-input-source" class="form-label">Source</label>
                        <input type="text" id="data-input-source" class="form-control" name="source" />
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="data-input-isi" class="form-label">Isi Artikel</label>
                        <textarea class="form-control" id="data-input-isi" name="isi_artikel" rows="2"></textarea>
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
