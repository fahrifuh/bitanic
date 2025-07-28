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
                        <div class="bg-info text-white p-3 rounded" role="alert">
                            <ul>
                                <li class="d-none" id="alert">Foto <b>TIDAK WAJIB</b> diisi!</li>
                                <li>Jika hama yang anda cari tidak ada, silahkan isi field nama hama dan pastikan anda tidak memilih hama yang tersedia.</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="data-input-pest-id" class="form-label">Pilih Hama</label>
                        <br>
                        <select class="form-select" id="data-input-pest-id" name="pest_id"
                            aria-label="Default select example">
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="data-input-pest-name" class="form-label">Nama Hama</label>
                        <input type="text" id="data-input-pest-name" class="form-control" name="pest_name" />
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="data-input-garden-id" class="form-label">Pilih Kebun</label>
                        <select class="form-select" id="data-input-garden-id" name="garden_id"
                            aria-label="Default select example">
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="data-input-invected-date" class="form-label">Tanggal Terkena Hama</label>
                        <input type="date" id="data-input-invected-date" class="form-control"
                            name="invected_date" />
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
