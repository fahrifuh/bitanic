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
                        <label for="data-input-category" class="form-label">Tipe</label>
                        <select class="form-select" id="data-input-category" name="category"
                            aria-label="Default select example">
                            @php
                                $categories = [
                                    'controller',
                                    'tongkat'
                                ];
                            @endphp
                            @foreach ($categories as $category)
                                <option value="{{ $category }}">{{ ucwords($category) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="data-input-seri-perangkat" class="form-label">Seri Perangkat</label>
                        <input type="text" id="data-input-seri-perangkat" class="form-control" name="seri_perangkat"
                            required />
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="data-input-type" class="form-label">Petani</label>
                        <select class="form-select" id="data-input-type" name="type"
                            aria-label="Default select example">
                                <option value="1">1</option>
                                <option value="2">2</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="data-input-versi" class="form-label">Versi</label>
                        <input type="number" min="0" id="data-input-versi" class="form-control" name="versi"
                            required />
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="data-input-tgl-produksi" class="form-label">Tanggal Produksi</label>
                        <input type="date" class="form-control" id="data-input-tgl-produksi" name="tgl_produksi"
                            placeholder="0" aria-label="0" aria-describedby="basic-addon13" />
                    </div>
                    <div class="col mb-3">
                        <label for="data-input-tgl-pembelian" class="form-label">Tanggal Pembelian</label>
                        <input type="date" class="form-control" id="data-input-tgl-pembelian" name="tgl_pembelian"
                            placeholder="0" aria-label="0" aria-describedby="basic-addon13" />
                    </div>
                    <div class="col mb-3">
                        <label for="data-input-tgl-aktifkan" class="form-label">Tanggal Diaktifkan <span class="text-danger">*tidak wajib</span></label>
                        <input type="date" class="form-control" id="data-input-tgl-aktifkan" name="tgl_aktifkan"
                            placeholder="0" aria-label="0" aria-describedby="basic-addon13" />
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="data-input-petani-id" class="form-label">Petani</label>
                        <select class="form-select" id="data-input-petani-id" name="petani_id"
                            aria-label="Default select example">
                            @if (auth()->user()->role != 'farmer')
                                <option value="">Tanpa Petani</option>
                            @endif
                            @forelse ($farmers as $farmer)
                                <option value="{{ $farmer->id }}">{{ $farmer->full_name }} |
                                    {{ $farmer->user->phone_number }}</option>
                            @empty
                                <option value="" disabled>Tidak ada data</option>
                            @endforelse
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="data-input-foto" class="form-label">Foto <span class="text-danger text-tidak-wajib">*tidak wajib</span></label>
                        <input class="form-control" type="file" id="data-input-foto" name="foto"
                            accept="image/png, image/jpg, image/jpeg" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 mb-2">
                        <button class="btn btn-info" id="btn-new-spesifik">New Spesifikasi</button>
                    </div>
                    <div class="col-md-12" id="form-spesifik">
                        {{-- spesifikasi --}}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Close
                </button>
                <button type="button" class="btn btn-primary" id="submit-btn">Save</button>
            </div>
        </div>
    </div>
</div>
