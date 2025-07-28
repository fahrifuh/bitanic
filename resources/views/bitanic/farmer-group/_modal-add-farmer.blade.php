<!-- Modal -->
<div class="modal fade" id="modaAddFarmer" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modaAddFarmerTitle">Pilih petani yang ingin ditambahkan</h5>
          <button
            type="button"
            class="btn-close"
            id="btn-close-add-farmer"
          ></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive text-wrap">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="align-middle text-center bg-primary text-white">No</th>
                                    <th class="align-middle text-center bg-primary text-white">Name</th>
                                    <th class="align-middle text-center bg-primary text-white">NIK</th>
                                    <th class="align-middle text-center bg-primary text-white">No Telphone</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0" id="list-petani">
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
          <button class="btn btn-secondary" id="btn-back-farmer">Kembali</button>
          <button class="btn btn-primary" id="btn-submit-farmers">Tambah Petani</button>
        </div>
      </div>
    </div>
</div>
