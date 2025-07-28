<!-- Modal -->
<div class="modal fade" id="modalForm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
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
                        <label for="data-input-name" class="form-label">Nama Kebun</label>
                        <input type="text" id="data-input-name" class="form-control" name="name" />
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="data-input-pemilik" class="form-label">Pemilik Kebun</label>
                        <input type="text" class="form-control" id="data-input-pemilik" name="pemilik" />
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="data-input-kategori" class="form-label">kategori</label>
                        <select class="form-select" id="data-input-kategori" name="kategori"
                            aria-label="Default select example">
                            <option value="urban">Urban</option>
                            <option value="rural">Rural</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="data-input-tipe" class="form-label">Tipe</label>
                        <select class="form-select" id="data-input-tipe" name="tipe"
                            aria-label="Default select example">
                            <option value="hidroponik">Hidroponik</option>
                            <option value="aquaponik">Aquaponik</option>
                            <option value="vertical">Vertical</option>
                            <option value="tradisional">Tradisional</option>
                        </select>
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col mb-0">
                        <label for="data-input-luas" class="form-label">Luas</label>
                        <input type="number" step="0.1" min="0" class="form-control" id="data-input-luas"
                            name="luas" />
                    </div>
                    <div class="col mb-0">
                        <label for="data-input-satuan" class="form-label">Satuan</label>
                        <select class="form-select" id="data-input-satuan" name="satuan"
                            aria-label="Default select example">
                            <option value="m">M2</option>
                            <option value="h">HEKTAR</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="data-input-lat" class="form-label">Latitude</label>
                        <input type="number" class="form-control" id="data-input-lat" name="lat" />
                    </div>
                    <div class="col mb-3">
                        <label for="data-input-lng" class="form-label">Longitude</label>
                        <input type="number" class="form-control" id="data-input-lng" name="lng" />
                    </div>
                    <div class="col mb-3">
                        <label for="data-input-alt" class="form-label">Altitude</label>
                        <input type="number" class="form-control" id="data-input-alt" name="alt" />
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <div class="alert alert-info" role="alert">Klik 2 kali pada peta untuk set marker kebun. Ketika <b>MODE POLYGON</b> aktif, klik 2 pada map untuk membuat area polygon nya.</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" class="form-check-input" type="checkbox" id="polygon-switch">
                            <label for="polygon-switch" class="form-check-label">Mode Polygon</label>
                        </div>
                    </div>
                    <div class="col mb-3">
                        <div class="float-end">
                            <button class="btn btn-sm btn-danger" id="btn-delete-polygon"><i class='bx bx-x'></i>&nbsp;Delete Polygon</button>
                            <button class="btn btn-sm btn-secondary" id="btn-reverse-polygon"><i class='bx bx-revision'></i></button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <div id="myMap"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="data-input-tanaman-id" class="form-label">Tanaman</label>
                        <select class="form-select" id="data-input-tanaman-id" name="tanaman_id"
                            aria-label="Default select example">
                            <option value="">-- Pilih Tanaman --</option>
                            @forelse ($crops as $crop)
                                <option value="{{ $crop->id }}">{{ $crop->crop_name }}</option>
                            @empty
                                <option disabled>Tidak ada tanaman</option>
                            @endforelse
                        </select>
                    </div>
                    <div class="col mb-3">
                        <label for="data-input-perangkat-id" class="form-label">Perangkat</label>
                        <select class="form-select" id="data-input-perangkat-id" name="perangkat_id"
                            aria-label="Default select example">
                            <option value="">-- Pilih Perangkat --</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="data-input-tgl-dibuat" class="form-label">Tanggal Dibuat</label>
                        <input type="date" class="form-control" id="data-input-tgl-dibuat" name="tgl_dibuat" />
                    </div>
                    <div class="col mb-3">
                        <label for="data-input-estimasi-panen" class="form-label">Estimasi Panen</label>
                        <input type="date" class="form-control" id="data-input-estimasi-panen"
                            name="estimasi_panen" />
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="data-input-foto" class="form-label">Foto</label>
                        <input class="form-control" type="file" id="data-input-foto" name="foto"
                            accept="image/png, image/jpg, image/jpeg" />
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="data-input-alamat" class="form-label">Alamat</label>
                        <textarea class="form-control" id="data-input-alamat" name="alamat" rows="2" placeholder="Jl. XXX"></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="alert alert-info" role="alert">Untuk pengisian nutrisi lahan dapat dikosongkan!</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <p>Nutrisi Per-Lahan</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="data-input-temperature" class="form-label">Suhu</label>
                        <div class="input-group">
                          <input
                            type="number"
                            class="form-control"
                            id="data-input-temperature"
                            name="temperature"
                            placeholder="0"
                            aria-label="0"
                            aria-describedby="basic-addon13"
                          />
                          <span class="input-group-text" id="basic-addon13">°C</span>
                        </div>
                    </div>
                    <div class="col mb-3">
                        <label for="data-input-moisture" class="form-label">Kelembapan</label>
                        <input type="number" class="form-control" id="data-input-moisture"
                            name="moisture" />
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="data-input-nitrogen" class="form-label">Nitrogen</label>
                        <input type="number" class="form-control" id="data-input-nitrogen"
                            name="nitrogen" />
                    </div>
                    <div class="col mb-3">
                        <label for="data-input-phosphor" class="form-label">Phosphor</label>
                        <input type="number" class="form-control" id="data-input-phosphor"
                            name="phosphor" />
                    </div>
                    <div class="col mb-3">
                        <label for="data-input-kalium" class="form-label">Kalium</label>
                        <input type="number" class="form-control" id="data-input-kalium"
                            name="kalium" />
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
