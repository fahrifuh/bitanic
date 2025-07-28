<!-- Modal -->
<div class="modal fade" id="modalProcess" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="#" method="POST" id="form-update-withdraw-status">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="modalProcessTitle">Proses Penarikan Saldo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body row">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label for="data-input-bank-account" class="form-label">No Rekening</label>
                                <input type="text" id="data-input-bank-account" class="form-control"
                                    name="bank_account" disabled />
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="data-input-bank-type" class="form-label">Bank</label>
                                <input type="text" id="data-input-bank-type" class="form-control" name="bank_type"
                                    disabled />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="data-input-receipt" class="form-label">Status Transfer</label>
                                <br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="is_accepted"
                                        id="inlineRadio1" value="1">
                                    <label class="form-check-label" for="inlineRadio1">Berhasil</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="is_accepted"
                                        id="inlineRadio2" value="0">
                                    <label class="form-check-label" for="inlineRadio2">Gagal</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary" id="submit-btn">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
