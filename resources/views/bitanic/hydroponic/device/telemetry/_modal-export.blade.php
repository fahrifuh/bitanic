<!-- Modal -->
<div class="modal fade" id="modalExportExcel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalExportExcelTitle">Export Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('bitanic.hydroponic.device.telemetry.export-excel', $hydroponicDevice->id) }}" method="GET">
                <div class="modal-body">
                    @csrf
                    <div class="row g-2">
                        <div class="col-12 col-md-6 mb-3">
                            <label class="form-label">Tanggal Awal</label>
                            <input type="date" class="form-control" name="from">
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label class="form-label">Tanggal Akhir</label>
                            <input type="date" class="form-control" name="to">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Tutup
                    </button>
                    <button type="submit" class="btn btn-primary" id="btn-edit-status-garden" data-id="#">Export</button>
                </div>
            </form>
        </div>
    </div>
</div>
