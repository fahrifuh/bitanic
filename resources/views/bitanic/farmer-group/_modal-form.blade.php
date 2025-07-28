<!-- Modal -->
<div class="modal fade" id="modalForm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFormTitle">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body row">
                <div class="col-md-6">
                    @csrf
                    <input type="hidden" name="id" id="data-input-id">
                    <div class="row d-none" id="alert">
                        <div class="col mb-3">
                            <div class="alert alert-info" role="alert">Password dan Foto <b>TIDAK WAJIB</b> diisi!</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="data-input-nama" class="form-label">Nama Kelompok Tani</label>
                            <input type="text" id="data-input-group-name" class="form-control" name="nama" />
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
                </div>
                <div class="col-md-6">
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <label for="data-input-province" class="form-label">Provinsi</label>
                            <select class="form-select" id="data-input-province" name="province"
                                aria-label="Default select example" aria-describedby="provinceFormControlHelp">
                            </select>
                            <div id="provinceFormControlHelp" class="form-text">
                                <a href="{{ route('bitanic.province.index') }}" target="_blank">+ Tambah data provinsi</a>
                            </div>
                        </div>
                        <div class="col">
                            <label for="data-input-city" class="form-label">Kabupaten/Kota</label>
                            <select class="form-select" id="data-input-city" name="city"
                                aria-label="Default select example" aria-describedby="cityFormControlHelp" disabled>
                            </select>
                            <div id="cityFormControlHelp" class="form-text">
                                <a href="{{ route('bitanic.city.index') }}" target="_blank">+ Tambah data Kabupaten/Kota</a>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <label for="data-input-district" class="form-label">Kecamatan</label>
                            <select class="form-select" id="data-input-district" name="district"
                                aria-label="Default select example" aria-describedby="districtFormControlHelp" disabled>
                            </select>
                            <div id="districtFormControlHelp" class="form-text">
                                <a href="{{ route('bitanic.district.index') }}" target="_blank">+ Tambah data Kecamatan</a>
                            </div>
                        </div>
                        <div class="col">
                            <label for="data-input-subdistrict" class="form-label">Desa</label>
                            <select class="form-select" id="data-input-subdistrict" name="subdistrict"
                                aria-label="Default select example" aria-describedby="subdistrictFormControlHelp" disabled>
                            </select>
                            <div id="subdistrictFormControlHelp" class="form-text">
                                <a href="{{ route('bitanic.subdistrict.index') }}" target="_blank">+ Tambah data Desa</a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="data-input-address" class="form-label">Alamat</label>
                            <textarea class="form-control" id="data-input-address" name="address" rows="2" placeholder="Jl. XXX"></textarea>
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
