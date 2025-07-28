<!-- Modal -->
<div class="modal fade" id="modalResi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ route('bitanic.shop.transaction-shipping-update', $transaction_id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="modalResiTitle">Kirim Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body row">
                    <div class="col-12">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="data-input-resi" class="form-label">Resi Pengiriman</label>
                                <input type="text" id="data-input-resi" class="form-control" name="resi" />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" class="btn btn-primary" id="submit-btn">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
