<!-- Modal -->
<div class="modal fade" id="modalSchedules" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalSchedulesTitle">Telemetri</h5>
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
                    <table class="table table-bordered">
                        <tbody class="table-border-bottom-0">
                            <tr>
                                <td class="bg-warning fw-bold text-white" style="width: 30%;">PENGIRIMAN JADWAL PEMUPUKAN</td>
                                <td class="text-center telemetri-modal" id="pemupukan-pengiriman-jadwal" colspan="2"></td>
                            </tr>
                            <tr>
                                <td class="bg-warning fw-bold text-white">NAMA TANAMAN</td>
                                <td class="text-center telemetri-modal" id="pemupukan-jenis-tanaman" colspan="2"></td>
                            </tr>
                            <tr>
                                <td class="bg-warning fw-bold text-white">TOTAL PEKAN</td>
                                <td class="text-center telemetri-modal" id="pemupukan-minggu" colspan="2"></td>
                            </tr>
                            <tr>
                                <td class="bg-warning fw-bold text-white">SET HARI</td>
                                <td class="text-center telemetri-modal" id="pemupukan-set-hari" colspan="2"></td>
                            </tr>
                            <tr>
                                <td class="bg-warning fw-bold text-white">SET WAKTU</td>
                                <td class="text-center telemetri-modal" id="pemupukan-set-waktu" colspan="2"></td>
                            </tr>
                            <tr>
                                <td class="bg-warning fw-bold text-white">STATUS POMPA IRIGASI</td>
                                <td class="text-center" id="pemupukan-status-pompo-1"></td>
                                <td width="25px" id="td-pompa-1"><button id="btn-status-pompa-1" class="btn btn-sm btn-secondary"
                                  data-garden="" data-status="">Aktifkan</button></td>
                            </tr>
                            <tr>
                                <td class="bg-warning fw-bold text-white">STATUS POMPA VERTIGASI</td>
                                <td class="text-center" id="pemupukan-status-pompo-2"></td>
                                <td id="td-pompa-2"><button id="btn-status-pompa-2" class="btn btn-sm btn-secondary"
                                  data-perangkat="" data-status="">Aktifkan</button></td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                </div>
                <div class="col-md-4 mb-3 d-none" id="btn-kirim-ulang">
                    <button class="btn btn-warning" id="btn-kirim-setting" data-garden="#" data-id="#">Kirim Ulang Setting untuk Perangkat</button>
                </div>
                <div class="col-md-4 mb-3 d-none" id="col-reset-ulang">
                    <button class="btn btn-danger" id="btn-reset-perangkat" data-garden="#" data-id="#">Hapus Pemupukan & Reset Perangkat</button>
                </div>
                <div class="col-md-4 mb-3 d-none" id="col-pemupukan-berhenti">
                    <button class="btn btn-info" id="btn-pemupukan-berhenti" data-garden="#" data-id="#">Pemupukan dihentikan & Reset Perangkat</button>
                </div>
                <div class="col-md-12 mb-3 d-none" id="col-alert-luar-jadwal">
                    <div class="bg-info text-white p-3 rounded">
                      <i class='bx bx-info-circle'></i>
                      Telemetri dibawah merupakan data penyiraman manual diluar jadwal. Data diambil dari 10 data terbaru!
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="table-responsive text-nowrap">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="bg-info align-middle text-white text-center">Pekan ke-N</th>
                                <th class="bg-info align-middle text-white text-center">Hari Penyiraman</th>
                                <th class="bg-info align-middle text-white text-center">Waktu <br> ( Mulai - Selesai )</th>
                                <th class="bg-info align-middle text-white text-center">Tipe</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0" id="view-schedules">
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
</div>
