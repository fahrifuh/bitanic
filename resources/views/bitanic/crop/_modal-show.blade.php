<!-- Modal -->
<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetailTitle">Detail Tanaman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="row mb-3">
                            <div class="col-12">
                                <img scrolling="no" id="iframe" src="" class="img-fluid" frameborder="0" style="width: 100%;">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <label>Detail</label>
                                <div class="table-responsive text-wrap">
                                    <table class="table table-bordered">
                                        <tbody class="table-border-bottom-0">
                                            <tr>
                                                <td class="text-start bg-primary text-white">Nama Tanaman</td>
                                                <td class="text-start detail-modal" id="modal-detail-name"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-start bg-primary text-white">Jenis</td>
                                                <td class="text-start detail-modal" id="modal-detail-jenis"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-start bg-primary text-white">Musim Tanam</td>
                                                <td class="text-start detail-modal" id="modal-detail-musim"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-start bg-primary text-white">Ketinggian Tanam</td>
                                                <td class="text-start detail-modal" id="modal-detail-ketinggian"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-start bg-primary text-white">Harga Pasar</td>
                                                <td class="text-start detail-modal text-break" id="modal-detail-harga"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-start bg-primary text-white">Penjelasan Harga</td>
                                                <td class="text-start detail-modal text-break" id="modal-detail-harga-penjelasan"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-start bg-primary text-white">Deskripsi</td>
                                                <td class="text-start detail-modal text-break" id="modal-detail-deskripsi"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="row">
                            <div class="col-12">
                                <label>Suhu</label>
                                <div class="table-responsive text-wrap">
                                    <table class="table table-bordered">
                                        <tbody class="table-border-bottom-0">
                                            <tr>
                                                <td class="text-start bg-danger text-white">Suhu Maximum</td>
                                                <td style="width: 20%" class="text-start detail-modal" id="modal-detail-suhu-maximum"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-start bg-success text-white">Suhu Optimum</td>
                                                <td class="text-start detail-modal" id="modal-detail-suhu-optimum"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-start bg-info text-white">Suhu Minimum</td>
                                                <td class="text-start detail-modal" id="modal-detail-suhu-minimum"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-12">
                                <label>Kelembapan</label>
                                <div class="table-responsive text-wrap">
                                    <table class="table table-bordered">
                                        <tbody class="table-border-bottom-0">
                                            <tr>
                                                <td class="text-start bg-danger text-white">Kelembapan Maximum</td>
                                                <td style="width: 20%" class="text-start detail-modal" id="modal-detail-kelembapan-maximum"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-start bg-success text-white">Kelembapan Optimum</td>
                                                <td class="text-start detail-modal" id="modal-detail-kelembapan-optimum"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-start bg-info text-white">Kelembapan Minimum</td>
                                                <td class="text-start detail-modal" id="modal-detail-kelembapan-minimum"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-12">
                                <label>Kebutuhan Pupuk</label>
                                <div class="table-responsive text-wrap">
                                    <table class="table table-bordered">
                                        <tbody class="table-border-bottom-0">
                                            <tr>
                                                <td class="text-start bg-primary text-white">Target pH</td>
                                                <td style="width: 20%" class="text-start detail-modal" id="modal-detail-target-ph"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-start bg-primary text-white">Target % Corganik</td>
                                                <td class="text-start detail-modal" id="modal-detail-target-corganik"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-start bg-primary text-white">Frekuensi Siram</td>
                                                <td class="text-start detail-modal" id="modal-detail-frekuensi-siram"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-start bg-primary text-white">N (KG/HA)</td>
                                                <td class="text-start detail-modal" id="modal-detail-n"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-12">
                                <label>P<sub>2</sub>O<sub>5</sub></label>
                                <div class="table-responsive text-wrap">
                                    <table class="table table-bordered">
                                        <tbody class="table-border-bottom-0">
                                            <tr>
                                                <td class="text-start bg-primary text-white">Sangat Rendah</td>
                                                <td style="width: 20%" class="text-start detail-modal" id="modal-detail-sangat-rendah-p2o5"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-start bg-primary text-white">Rendah</td>
                                                <td class="text-start detail-modal" id="modal-detail-rendah-p2o5"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-start bg-primary text-white">Sedang</td>
                                                <td class="text-start detail-modal" id="modal-detail-sedang-p2o5"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-start bg-primary text-white">Tinggi</td>
                                                <td class="text-start detail-modal" id="modal-detail-tinggi-p2o5"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-start bg-primary text-white">Sangat Tinggi</td>
                                                <td class="text-start detail-modal" id="modal-detail-sangat-tinggi-p2o5"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-12">
                                <label>K<sub>2</sub>O</label>
                                <div class="table-responsive text-wrap">
                                    <table class="table table-bordered">
                                        <tbody class="table-border-bottom-0">
                                            <tr>
                                                <td class="text-start bg-primary text-white">Sangat Rendah</td>
                                                <td style="width: 20%" class="text-start detail-modal" id="modal-detail-sangat-rendah-k2o"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-start bg-primary text-white">Rendah</td>
                                                <td class="text-start detail-modal" id="modal-detail-rendah-k2o"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-start bg-primary text-white">Sedang</td>
                                                <td class="text-start detail-modal" id="modal-detail-sedang-k2o"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-start bg-primary text-white">Tinggi</td>
                                                <td class="text-start detail-modal" id="modal-detail-tinggi-k2o"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-start bg-primary text-white">Sangat Tinggi</td>
                                                <td class="text-start detail-modal" id="modal-detail-sangat-tinggi-k2o"></td>
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
    </div>
</div>
