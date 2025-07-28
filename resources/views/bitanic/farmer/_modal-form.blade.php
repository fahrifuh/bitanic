<!-- Modal -->
<div class="modal fade" id="modalForm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFormTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body row">
                <div class="col-md-6">
                    @csrf
                    <input type="hidden" name="id" id="data-input-id">
                    <div class="row d-none" id="alert">
                        <div class="col mb-3">
                            <div class="rounded p-3 bg-info text-white" role="alert">Password dan Foto <b>TIDAK
                                    WAJIB</b> diisi!</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="data-input-type" class="form-label">Tipe Pengguna</label>
                            <select class="form-select" id="data-input-type" name="type"
                                aria-label="Default select example">
                                <option value="1">1</option>
                                <option value="2" selected>2</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="data-input-category" class="form-label">Kategori Pengguna</label>
                            <select class="form-select" id="data-input-category" name="category"
                                aria-label="Default select example">
                                @foreach (farmerCategory() as $key => $farmerCategory)
                                    <option value="{{ $key }}">{{ $farmerCategory }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="data-input-nama" class="form-label">Nama Lengkap</label>
                            <input type="text" id="data-input-nama" class="form-control" name="nama"
                                autocomplete="name" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="data-input-nik" class="form-label">NIK</label>
                            <input type="number" class="form-control" id="data-input-nik" name="nik" />
                        </div>
                        <div class="col mb-3">
                            <label for="data-input-phone-number" class="form-label">Nomor HP</label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon13">+62</span>
                                <input type="number" class="form-control" id="data-input-phone-number"
                                    name="phone_number" placeholder="8xxxxxxxxx" aria-describedby="basic-addon13" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="data-input-password" class="form-label">Password</label>
                            <input type="password" id="data-input-password" class="form-control" name="password"
                                aria-describedby="passwordHelp" />
                            <div id="passwordHelp" class="form-text">*min 8 karakter</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="data-input-password-confirm" class="form-label">Password Konfirmasi</label>
                            <input type="password" id="data-input-password-confirm" class="form-control"
                                name="password_confirm" />
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-0">
                            <label for="data-input-gender" class="form-label">Jenis Kelamin</label>
                            <select class="form-select" id="data-input-gender" name="gender"
                                aria-label="Default select example">
                                <option value="l">Laki-laki</option>
                                <option value="p">Perempuan</option>
                            </select>
                        </div>
                        <div class="col mb-0">
                            <label for="data-input-birth-date" class="form-label">Tanggal Lahir</label>
                            <input type="date" class="form-control" id="data-input-birth-date"
                                name="birth_date" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="data-input-foto" class="form-label">Foto</label>
                            <input class="form-control" type="file" id="data-input-foto" name="foto"
                                accept="image/png, image/jpg, image/jpeg" aria-describedby="pictureHelp" />
                            <div id="pictureHelp" class="form-text">Format gambar JPG, JPEG, PNG. Maks. 2MB</div>
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
                                <a href="{{ route('bitanic.province.index') }}" target="_blank">+ Tambah data
                                    provinsi</a>
                            </div>
                        </div>
                        <div class="col">
                            <label for="data-input-city" class="form-label">Kabupaten/Kota</label>
                            <select class="form-select" id="data-input-city" name="city"
                                aria-label="Default select example" aria-describedby="cityFormControlHelp" disabled>
                            </select>
                            <div id="cityFormControlHelp" class="form-text">
                                <a href="{{ route('bitanic.city.index') }}" target="_blank">+ Tambah data
                                    Kabupaten/Kota</a>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <label for="data-input-district" class="form-label">Kecamatan</label>
                            <select class="form-select" id="data-input-district" name="district"
                                aria-label="Default select example" aria-describedby="districtFormControlHelp"
                                disabled>
                            </select>
                            <div id="districtFormControlHelp" class="form-text">
                                <a href="{{ route('bitanic.district.index') }}" target="_blank">+ Tambah data
                                    Kecamatan</a>
                            </div>
                        </div>
                        <div class="col">
                            <label for="data-input-subdistrict" class="form-label">Desa</label>
                            <select class="form-select" id="data-input-subdistrict" name="subdistrict"
                                aria-label="Default select example" aria-describedby="subdistrictFormControlHelp"
                                disabled>
                            </select>
                            <div id="subdistrictFormControlHelp" class="form-text">
                                <a href="{{ route('bitanic.subdistrict.index') }}" target="_blank">+ Tambah data
                                    Desa</a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="data-input-farmer-group" class="form-label">Kelompok Pengguna</label>
                            <select class="form-select" id="data-input-farmer-group" name="farmer_group"
                                aria-label="Default select example">
                                <option value="">Tidak Memiliki</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="data-input-address" class="form-label">Alamat</label>
                            <textarea class="form-control" id="data-input-address" name="address" rows="2" placeholder="Jl. XXX"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="data-input-product" class="form-label">Produk yang digunakan <span class="ms-2 fst-italic">*Pilih 1 atau lebih</span></label>
                            <select class="form-select" id="data-input-product" name="products[]"
                                aria-label="Default select example" multiple>
                                @forelse ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @empty
                                    <option value="">Belum ada produk yang tersedia.</option>
                                @endforelse
                            </select>
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
