<!-- Modal -->
<div class="modal fade" id="modalAddFertilization" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAddFertilizationTitle">Buat pemupukan baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @csrf
                <input type="hidden" name="garden_id" id="data-input-garden-id">
                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label">Nama Tanaman</label>
                        <input type="text" class="form-control" id="data-input-nama-tanaman" name="name_tanaman" />
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="data-input-set-minggu" class="form-label">Set Minggu</label>
                        <input type="number" min="0" max="15" class="form-control" id="data-input-set-minggu" name="set_minggu" />
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label">Set Hari</label>

                        <br>

                        @php
                            $days = ['minggu', 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];
                        @endphp

                        @foreach ($days as $day)
                            <div class="form-check form-check-inline mt-2">
                                <input class="form-check-input data-input-set-hari" type="checkbox" value="{{ $day }}" id="hari-{{ $day }}" />
                                <label class="form-check-label" for="hari-{{ $day }}"> {{ ucwords($day) }} </label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col mb-0">
                        <label for="data-input-set-waktu" class="form-label">Set Waktu</label>
                        <input type="time" class="form-control" id="data-input-set-waktu" name="set_waktu" />
                    </div>
                    <div class="col mb-0">
                        <label for="data-input-set-menit" class="form-label">Set menit</label>
                        <input type="number" min="0" max="60" class="form-control" id="data-input-set-menit" name="set_menit" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-target="#modalFertilizationList" data-bs-toggle="modal">
                        Tutup
                    </button>
                    <button type="submit" class="btn btn-primary" id="btn-add-fertilization">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</div>
