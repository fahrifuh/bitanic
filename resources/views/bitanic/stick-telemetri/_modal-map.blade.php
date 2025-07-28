<!-- Modal -->
<div class="modal fade" id="modalMap" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalMapTitle">Detail RSC</h5>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"
          ></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-12 col-md-6">
                    <div id="modal-map"></div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="table-responsive text-wrap" id="table-npk">
                        <table class="table table-bordered">
                            <tbody class="table-border-bottom-0">
                                <tr>
                                    <td class="text-start bg-info text-white">Latitude</td>
                                    <td class="text-start detail-modal" id="telemetri-latitude"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">Longitude</td>
                                    <td class="text-start detail-modal" id="telemetri-longitude"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">ID Perangkat</td>
                                    <td class="text-start detail-modal" id="telemetri-id-perangkat"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">ID Pengukuran</td>
                                    <td class="text-start detail-modal" id="telemetri-id-pengukuran"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">Luas</td>
                                    <td class="text-start detail-modal" id="telemetri-area"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">Suhu</td>
                                    <td class="text-start detail-modal" id="telemetri-temperature"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">Kelembapan</td>
                                    <td class="text-start detail-modal" id="telemetri-moisture"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">N</td>
                                    <td class="text-start detail-modal" id="telemetri-n"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">P</td>
                                    <td class="text-start detail-modal" id="telemetri-p"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">K</td>
                                    <td class="text-start detail-modal" id="telemetri-k"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">CO2</td>
                                    <td class="text-start detail-modal" id="telemetri-co2"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">NO2</td>
                                    <td class="text-start detail-modal" id="telemetri-no2"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">N2O</td>
                                    <td class="text-start detail-modal" id="telemetri-n2o"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="table-responsive text-wrap d-none" id="table-luas">
                        <table class="table table-bordered">
                            <tbody class="table-border-bottom-0">
                                <tr>
                                    <td class="text-start bg-info text-white">Luas</td>
                                    <td class="text-start" id="telemetri-area"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                Close
            </button>
            <button type="button" class="btn btn-danger" id="btn-delete-data" data-id="" title="Klik untuk hapus data">Hapus</button>
        </div>
      </div>
    </div>
</div>
