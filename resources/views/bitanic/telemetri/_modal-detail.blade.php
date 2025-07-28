<!-- Modal -->
<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalDetailTitle">Detail Telemetri</h5>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"
          ></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="table-responsive text-wrap">
                        <table class="table table-bordered">
                            <tbody class="table-border-bottom-0">
                                <tr>
                                    <td class="text-start bg-info text-white">Farmer</td>
                                    <td class="text-start detail-val" id="detail-farmer"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">Device</td>
                                    <td class="text-start detail-val" id="detail-device"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">Garden</td>
                                    <td class="text-start detail-val" id="detail-garden"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">NODE ID</td>
                                    <td class="text-start detail-val" id="detail-node-id"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">HEADER</td>
                                    <td class="text-start detail-val" id="detail-header"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">TEMPERATURE</td>
                                    <td class="text-start detail-val" id="detail-temperature"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">HUMIDITY</td>
                                    <td class="text-start detail-val" id="detail-humidity"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="table-responsive text-wrap">
                        <table class="table table-bordered">
                            <tbody class="table-border-bottom-0">
                                <tr>
                                    <td class="text-start bg-info text-white">pH LEVEL</td>
                                    <td class="text-start detail-val" id="detail-ph-level"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">LIGHT</td>
                                    <td class="text-start detail-val" id="detail-light"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">GAS</td>
                                    <td class="text-start detail-val" id="detail-gas"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">MOTOR</td>
                                    <td class="text-start detail-val" id="detail-motor"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">MODE</td>
                                    <td class="text-start detail-val" id="detail-mode"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">TAIL</td>
                                    <td class="text-start detail-val" id="detail-tail"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="my-backdrop fade d-flex justify-content-center align-items-center" id="my-spinner">
                <div class="spinner-border text-info" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
      </div>
    </div>
</div>
