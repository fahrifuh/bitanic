<!-- Modal -->
<div class="modal fade" id="modalDeleteFarmer" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalDeleteFarmerTitle">Pilih petani yang ingin dihapus</h5>
          <button
            type="button"
            class="btn-close"
            id="btn-close-delete-farmer"
          ></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive text-wrap">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="align-middle text-center bg-danger text-white">No</th>
                                    <th class="align-middle text-center bg-danger text-white">Name</th>
                                    <th class="align-middle text-center bg-danger text-white">NIK</th>
                                    <th class="align-middle text-center bg-danger text-white">No Telphone</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0" id="list-petani-for-delete">
                                <tr>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" id="btn-back-delete-farmer">Kembali</button>
          <button class="btn btn-danger" id="btn-submit-delete-farmers">Hapus Petani</button>
        </div>
      </div>
    </div>
</div>
