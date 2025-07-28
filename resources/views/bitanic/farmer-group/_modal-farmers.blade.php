<!-- Modal -->
<div class="modal fade" id="modalFarmers" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalFarmersTitle">List Petani</h5>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"
          ></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive text-wrap">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="align-middle text-center bg-warning text-white">No</th>
                                    <th class="align-middle text-center bg-warning text-white">Name</th>
                                    <th class="align-middle text-center bg-warning text-white">NIK</th>
                                    <th class="align-middle text-center bg-warning text-white">No Telphone</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0" id="view-spesifik">
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
          <button class="btn btn-danger" data-bs-target="#modalDeleteFarmer" data-bs-toggle="modal" id="btn-delete-farmer">Hapus Petani</button>
          <button class="btn btn-primary" data-bs-target="#modaAddFarmer" data-bs-toggle="modal" id="btn-add-farmer">Tambah Petani</button>
        </div>
      </div>
    </div>
</div>
