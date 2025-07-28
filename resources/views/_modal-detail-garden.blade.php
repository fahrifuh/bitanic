<!-- Modal -->
<div class="modal fade" id="modalDetailGarden" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalDetailGardenTitle">Detail</h5>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"
          ></button>
        </div>
        <div class="modal-body">
            <div class="row g-3">
                <div class="col-12">
                    <div id="modal-map"></div>
                </div>
                <div class="col-md-12">
                    <div class="card">
                        <div class="row g-0">
                            <div class="col-md-8">
                                <div class="card-body">
                                    <div class="row g-2">
                                        <div class="col-12">
                                            <h5 class="card-title">Detail Lahan</h5>
                                        </div>
                                        <div class="col-12">
                                            <label for="" class="fw-bold">Nama User</label>
                                            <p class="card-text detail-modal" id="text-view-user-name"></p>
                                        </div>
                                        <div class="col-12">
                                            <label for="" class="fw-bold">Nama Lahan</label>
                                            <p class="card-text detail-modal" id="text-view-land-name"></p>
                                        </div>
                                        <div class="col-12">
                                            <label for="" class="fw-bold">Luas</label>
                                            <p class="card-text detail-modal" id="text-view-land-area"></p>
                                        </div>
                                        <div class="col-12">
                                            <label for="" class="fw-bold">Latitude, Longitude</label>
                                            <p class="card-text detail-modal" id="text-view-land-latlng"></p>
                                        </div>
                                        <div class="col-12">
                                            <label for="" class="fw-bold">Altitude</label>
                                            <p class="card-text detail-modal" id="text-view-land-altitude"></p>
                                        </div>
                                        <div class="col-12">
                                            <label for="" class="fw-bold">Alamat</label>
                                            <p class="card-text detail-modal" id="text-view-land-address"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <img class="card-img card-img-right" src="{{ asset('theme/img/elements/17.jpg') }}" alt="Card image" id="img-land" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive text-wrap">
                        <table class="table caption-top table-bordered">
                            <caption>List Kebun</caption>
                            <thead class="table-border-bottom-0">
                                <tr>
                                    <th class="bg-warning text-center text-white" style="width: 5%;">#</th>
                                    <th class="bg-warning text-center text-white">Nama</th>
                                    <th class="bg-warning text-center text-white">Tanaman</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0" id="gardens-list">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
</div>
