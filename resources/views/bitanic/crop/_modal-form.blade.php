<!-- Modal -->
<div class="modal fade" id="modalForm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFormTitle">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @csrf
                <div class="row g-2">
                    <div class="col-12 col-md-4">
                        <input type="hidden" name="id" id="data-input-id">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="data-input-nama" class="form-label">Nama Tanaman</label>
                                <input type="text" id="data-input-nama-tanaman" class="form-control" name="nama_tanaman" />
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col mb-0">
                                <label for="data-input-jenis" class="form-label">Jenis Tanaman</label>
                                <select class="form-select" id="data-input-jenis" name="jenis" aria-label="Default select example">
                                    <option value="sayur">Sayur</option>
                                    <option value="buah">Buah</option>
                                </select>
                            </div>
                            <div class="col mb-0">
                                <label for="data-input-musim" class="form-label">Musim</label>
                                <select class="form-select" id="data-input-musim" name="musim" aria-label="Default select example">
                                    <option value="hujan">Hujan</option>
                                    <option value="kemarau">Kemarau</option>
                                </select>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col mb-0">
                                <label for="data-input-price" class="form-label">Harga pasaran</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" min="0" id="data-input-price" class="form-control" name="price" />
                                    <span class="input-group-text">.00</span>
                                </div>
                            </div>
                            <div class="col mb-0">
                                <label for="data-input-price-description" class="form-label">Penjelasan harga</label>
                                <input type="text" id="data-input-price-description" class="form-control" name="price_description" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="data-input-ketinggian" class="form-label">Ketinggian</label>
                                <div class="input-group">
                                    <input type="number" id="data-input-ketinggian" name="ketinggian"
                                        aria-describedby="basic-altitude" class="form-control" min="0" />
                                    <span class="input-group-text" id="basic-altitude">mdpl</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="data-input-foto" class="form-label">Foto</label>
                                <input class="form-control" type="file" id="data-input-foto" name="foto" accept="image/png, image/jpg, image/jpeg"
                                    aria-describedby="pictureHelp" />
                                <div id="pictureHelp" class="form-text">Format gambar JPG, JPEG, PNG. Maks.
                                    2MB</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="data-input-deskripsi" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="data-input-deskripsi" name="deskripsi" rows="5"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-8">
                        <div class="row g-2 mb-3">
                            <div class="col mb-0">
                                <label for="data-input-suhu-minimum" class="form-label text-info">Suhu Minimum</label>
                                <div class="input-group">
                                    <input type="number" class="form-control text-info" id="data-input-suhu-minimum" name="suhu_minimum" placeholder="0" aria-label="0" aria-describedby="basic-addon13" />
                                    <span class="input-group-text" id="basic-addon13">°C</span>
                                </div>
                            </div>
                            <div class="col mb-0">
                                <label for="data-input-suhu-optimum" class="form-label text-success">Suhu Optimum</label>
                                <div class="input-group">
                                    <input type="number" class="form-control text-success" id="data-input-suhu-optimum" name="suhu_optimum" placeholder="0" aria-label="0" aria-describedby="basic-addon13" />
                                    <span class="input-group-text" id="basic-addon13">°C</span>
                                </div>
                            </div>
                            <div class="col mb-0">
                                <label for="data-input-suhu-maximum" class="form-label text-danger">Suhu Maximum</label>
                                <div class="input-group">
                                    <input type="number" class="form-control text-danger" id="data-input-suhu-maximum" name="suhu_maximum" placeholder="0" aria-label="0" aria-describedby="basic-addon13" />
                                    <span class="input-group-text" id="basic-addon13">°C</span>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col mb-0">
                                <label for="data-input-kelembapan-minimum" class="form-label text-info">Kelembapan Minimum</label>
                                <div class="input-group">
                                    <input type="number" min="0" max="100" class="form-control text-info" id="data-input-kelembapan-minimum" name="kelembapan_minimum" placeholder="0" aria-label="0" aria-describedby="basic-addon13" />
                                    <span class="input-group-text" id="basic-addon13">%</span>
                                </div>
                            </div>
                            <div class="col mb-0">
                                <label for="data-input-kelembapan-optimum" class="form-label text-success">Kelembapan Optimum</label>
                                <div class="input-group">
                                    <input type="number" min="0" max="100" class="form-control text-success" id="data-input-kelembapan-optimum" name="kelembapan_optimum" placeholder="0" aria-label="0" aria-describedby="basic-addon13" />
                                    <span class="input-group-text" id="basic-addon13">%</span>
                                </div>
                            </div>
                            <div class="col mb-0">
                                <label for="data-input-kelembapan-maximum" class="form-label text-danger">Kelembapan Maximum</label>
                                <div class="input-group">
                                    <input type="number" min="0" max="100" class="form-control text-danger" id="data-input-kelembapan-maximum" name="kelembapan_maximum" placeholder="0" aria-label="0" aria-describedby="basic-addon13" />
                                    <span class="input-group-text" id="basic-addon13">%</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                            <label for="data-input-target-ph" class="form-label">Target <span class="text-lowercase">p</span>H</label>
                            <input
                                type="number"
                                id="data-input-target-ph"
                                class="form-control"
                                name="target_ph"
                                required
                            />
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                        <div class="col mb-0">
                            <label for="data-input-target-persen-corganik" class="form-label">Target % Corganik</label>
                            <input
                                type="number"
                                id="data-input-target-persen-corganik"
                                class="form-control"
                                name="target_corganic"
                                required
                            />
                        </div>
                        <div class="col mb-0">
                            <label for="data-input-frekuensi-siram" class="form-label">Frekuensi Siram (Minggu)</label>
                            <input
                                type="number"
                                id="data-input-frekuensi-siram"
                                class="form-control"
                                name="frekuensi_siram"
                                required
                                max="15"
                                min="0"
                            />
                        </div>
                        <div class="col mb-0">
                            <label for="data-input-n-kg-ha" class="form-label">N (<span class="text-lowercase">kg</span>/<span class="text-lowercase">ha</span>)</label>
                            <input
                                type="number"
                                id="data-input-n-kg-ha"
                                class="form-control"
                                name="n_kg_ha"
                                required
                            />
                        </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <b>P<sub>2</sub>O<sub>5</sub></b>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                        <div class="col mb-0">
                            <label for="data-input-sangat-rendah-p2o5" class="form-label">Sangat Rendah</label>
                            <input
                                type="number"
                                id="data-input-sangat-rendah-p2o5"
                                class="form-control"
                                name="sangat_rendah_p2o5"
                                required
                            />
                        </div>
                        <div class="col mb-0">
                            <label for="data-input-rendah-p2o5" class="form-label">Rendah</label>
                            <input
                                type="number"
                                id="data-input-rendah-p2o5"
                                class="form-control"
                                name="rendah_p2o5"
                                required
                            />
                        </div>
                        <div class="col mb-0">
                            <label for="data-input-sedang-p2o5" class="form-label">Sedang</label>
                            <input
                                type="number"
                                id="data-input-sedang-p2o5"
                                class="form-control"
                                name="sedang_p2o5"
                                required
                            />
                        </div>
                        <div class="col mb-0">
                            <label for="data-input-tinggi-p2o5" class="form-label">Tinggi</label>
                            <input
                                type="number"
                                id="data-input-tinggi-p2o5"
                                class="form-control"
                                name="tinggi_p2o5"
                                required
                            />
                        </div>
                        <div class="col mb-0">
                            <label for="data-input-sangat-tinggi-p2o5" class="form-label">Sangat Tinggi</label>
                            <input
                                type="number"
                                id="data-input-sangat-tinggi-p2o5"
                                class="form-control"
                                name="sangat_tinggi_p2o5"
                                required
                            />
                        </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <b>K<sub>2</sub>O</b>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                        <div class="col mb-0">
                            <label for="data-input-sangat-rendah-k2o" class="form-label">Sangat Rendah</label>
                            <input
                                type="number"
                                id="data-input-sangat-rendah-k2o"
                                class="form-control"
                                name="sangat_rendah_k2o"
                                required
                            />
                        </div>
                        <div class="col mb-0">
                            <label for="data-input-rendah-k2o" class="form-label">Rendah</label>
                            <input
                                type="number"
                                id="data-input-rendah-k2o"
                                class="form-control"
                                name="rendah_k2o"
                                required
                            />
                        </div>
                        <div class="col mb-0">
                            <label for="data-input-sedang-k2o" class="form-label">Sedang</label>
                            <input
                                type="number"
                                id="data-input-sedang-k2o"
                                class="form-control"
                                name="sedang_k2o"
                                required
                            />
                        </div>
                        <div class="col mb-0">
                            <label for="data-input-tinggi-k2o" class="form-label">Tinggi</label>
                            <input
                                type="number"
                                id="data-input-tinggi-k2o"
                                class="form-control"
                                name="tinggi_k2o"
                                required
                            />
                        </div>
                        <div class="col mb-0">
                            <label for="data-input-sangat-tinggi-k2o" class="form-label">Sangat Tinggi</label>
                            <input
                                type="number"
                                id="data-input-sangat-tinggi-k2o"
                                class="form-control"
                                name="sangat_tinggi_k2o"
                                required
                            />
                        </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="data-input-catatan" class="form-label">Catatan</label>
                                <textarea
                                id="data-input-catatan"
                                class="form-control"
                                name="catatan"
                                rows="3"></textarea>
                            </div>
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
</div>
