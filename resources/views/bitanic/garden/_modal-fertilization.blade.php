<!-- Modal -->
<div class="modal fade" id="modalFertilizationList" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalFertilizationListTitle">Telemetri</h5>
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
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th class="bg-primary align-middle text-white text-center">No</th>
                                    <th class="bg-primary align-middle text-white text-center">Nama Tanaman</th>
                                    <th class="bg-primary align-middle text-white text-center">Total Minggu</th>
                                    <th class="bg-primary align-middle text-white text-center">Hari Penyiraman</th>
                                    <th class="bg-primary align-middle text-white text-center">Pengiriman Jadwal Pemupukan</th>
                                    <th class="bg-primary align-middle text-white text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0" id="fertilization-list">
                                <tr>
                                    <th class="text-center"></th>
                                    <th class="text-center"></th>
                                    <th class="text-center"></th>
                                    <th class="text-center"></th>
                                    <th class="text-center"></th>
                                    <th class="text-center"></th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary" id="btn-modal-add" data-bs-target="#modalAddFertilization" data-bs-toggle="modal">Buat Pemupukan</button>
        </div>
      </div>
    </div>
</div>
