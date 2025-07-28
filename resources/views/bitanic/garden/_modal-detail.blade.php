<!-- Modal -->
<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalDetailTitle">Detail Kebun</h5>
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
                    <p>Detail Kebun</p>
                </div>
                <div class="col-md-6">
                    <div class="table-responsive text-wrap">
                        <table class="table table-bordered">
                            <tbody class="table-border-bottom-0">
                                <tr>
                                    <td class="text-start bg-info text-white">Lahan</td>
                                    <td class="text-start detail-modal" id="modal-detail-name"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">Tipe</td>
                                    <td class="text-start detail-modal" id="modal-detail-type"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">Luas</td>
                                    <td class="text-start detail-modal" id="modal-detail-area"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">Latitude</td>
                                    <td class="text-start detail-modal text-break" id="modal-detail-lat"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">longitude</td>
                                    <td class="text-start detail-modal text-break" id="modal-detail-lng"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">Altitude</td>
                                    <td class="text-start detail-modal text-break" id="modal-detail-alt"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">Tanggal Dibuat</td>
                                    <td class="text-start detail-modal" id="modal-detail-date-created"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">Tanaman</td>
                                    <td class="text-start detail-modal" id="modal-detail-crop"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">Perangkat</td>
                                    <td class="text-start detail-modal" id="modal-detail-device"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">Alamat</td>
                                    <td class="text-start detail-modal" id="modal-detail-address"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="table-responsive text-wrap">
                        <table class="table table-bordered">
                            <tbody class="table-border-bottom-0" id="body-hydroponic">
                                <tr>
                                    <td class="text-start bg-info text-white">Jumlah Pipa</td>
                                    <td class="text-start detail-modal text-break" id="modal-detail-levels"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">Jumlah Lubang Per Pipa</td>
                                    <td class="text-start detail-modal" id="modal-detail-holes"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">Total Pod (Pipa x Lubang)</td>
                                    <td class="text-start detail-modal" id="modal-detail-total-pod"></td>
                                </tr>
                            </tbody>
                            <tbody class="table-border-bottom-0" id="body-aquaponic">
                                <tr>
                                    <td class="text-start bg-info text-white">Panjang Kolam</td>
                                    <td class="text-start detail-modal text-break" id="modal-detail-length"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">Lebar Kolam</td>
                                    <td class="text-start detail-modal" id="modal-detail-width"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">Tinggi Kolam</td>
                                    <td class="text-start detail-modal" id="modal-detail-height"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">Volume</td>
                                    <td class="text-start detail-modal" id="modal-detail-volume"></td>
                                </tr>
                                <tr>
                                    <td class="text-start bg-info text-white">Jenis Ikan</td>
                                    <td class="text-start detail-modal" id="modal-detail-fish-type"></td>
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
