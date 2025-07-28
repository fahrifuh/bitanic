<!-- Modal -->
<div class="modal fade" id="modalSpesifikasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalSpesifikasiTitle">Details Perangkat</h5>
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
                        <table class="table table-bordered">
                            <tbody class="table-border-bottom-0">
                                <tr>
                                    <td class="text-start bg-info text-white">Tipe</td>
                                    <td class="text-start" id="device-category"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">Seri Perangkat</td>
                                    <td class="text-start" id="device-seri-perangkat"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">Petani</td>
                                    <td class="text-start" id="device-type"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">Versi</td>
                                    <td class="text-start" id="device-versi"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">Tanggal Produksi</td>
                                    <td class="text-start" id="device-tgl-produksi"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">Tanggal Pembelian</td>
                                    <td class="text-start" id="device-tgl-pembelian"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">Tanggal Diaktifkan</td>
                                    <td class="text-start" id="device-tgl-diaktifkan"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">Status</td>
                                    <td class="text-start" id="device-status"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">Pemilik Perangkat</td>
                                    <td class="text-start" id="device-farmer"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="table-responsive text-wrap">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="align-middle text-center bg-primary text-white" colspan="2">Specification</th>
                                </tr>
                                <tr>
                                    <th class="align-middle text-center bg-warning text-white">Name</th>
                                    <th class="align-middle text-center bg-warning text-white">Value</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0" id="view-spesifik">
                                <tr>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
</div>
