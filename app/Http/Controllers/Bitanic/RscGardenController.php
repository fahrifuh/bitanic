<?php

namespace App\Http\Controllers\Bitanic;

use App\Exports\RscGardenTelemetryExport;
use App\Http\Controllers\Controller;
use App\Models\Farmer;
use App\Models\Garden;
use App\Models\Land;
use App\Models\RscGarden;
use App\Models\RscGardenTelemetry;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class RscGardenController extends Controller
{
    public function getRscGarden(
        Farmer $farmer,
        Land $land,
        Garden $garden,
        RscGarden $rscGarden
    ) : JsonResponse
    {
        if (
            auth()->user()->role != 'admin' &&
            $farmer->user_id != auth()->user()->id
        ) {
            return abort(403);
        }
        if ($farmer->id != $land->farmer_id) {
            return back()->with('failed', 'Lahan tidak sama dengan yang ada di petani');
        }
        if (
            $farmer->id != $garden->land->farmer_id
        ) {
            return abort(403);
        }
        if ($garden->id != $rscGarden->garden_id) {
            return back()->with('failed', 'RSC bukan yang ada dalam Kebun');
        }

        $rscGarden
            ->load('rscGardenTelemetries')
            ->loadAvg('rscGardenTelemetries as avg_n', 'samples->n')
            ->loadAvg('rscGardenTelemetries as avg_p', 'samples->p')
            ->loadAvg('rscGardenTelemetries as avg_k', 'samples->k');

        return response()->json([
            'message' => 'Data RSC Kebun',
            'rsc_garden' => $rscGarden
        ]);
    }

    public function history(
        Farmer $farmer,
        Land $land,
        Garden $garden
    ) : View
    {
        if (
            auth()->user()->role != 'admin' &&
            $farmer->user_id != auth()->user()->id
        ) {
            return abort(403);
        }
        if ($farmer->id != $land->farmer_id) {
            return back()->with('failed', 'Lahan tidak sama dengan yang ada di petani');
        }
        if (
            $farmer->id != $garden->land->farmer_id
        ) {
            return abort(403);
        }

        $rscGardens = RscGarden::query()
            ->with('device:id,device_series')
            ->where('garden_id', $garden->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('bitanic.garden.rsc.history', compact(
            'farmer',
            'land',
            'garden',
            'rscGardens',
        ));
    }

    public function destroy(
        Request $request,
        Farmer $farmer,
        Land $land,
        Garden $garden,
        RscGarden $rscGarden
    ) {
        $message = '';
        $isFailed = false;

        if (
            auth()->user()->role != 'admin' &&
            $farmer->user_id != auth()->user()->id
        ) {
            return abort(403);
        }
        if ($farmer->id != $land->farmer_id) {
            return abort(403, 'Lahan tidak sama dengan yang ada di petani');
        }
        if (
            $farmer->id != $garden->land->farmer_id
        ) {
            return abort(403);
        }
        if ($garden->id != $rscGarden->garden_id) {
            return abort(403, 'Komoditi tidak sama dengan yang ada di kebun');
        }

        $rscGarden->delete();

        if ($request->acceptsJson()) {
            return response()->json([
                'message' => 'Berhasil dihapus'
            ]);
        }

        return redirect()
            ->route('bitanic.commodity.history', [
                'farmer' => $farmer->id,
                'land' => $land->id,
                'garden' => $garden->id,
            ])
            ->with('success', 'Berhasil dihapus');
    }

    public function exportExcel(
        Farmer $farmer,
        Land $land,
        Garden $garden,
        RscGarden $rscGarden
    ) {
        try {
            $this->checkRscAutorize(
                $farmer,
                $land,
                $garden,
                $rscGarden,
            );

            return Excel::download(new RscGardenTelemetryExport($rscGarden), now()->format('YmdHis') . '_rsc-telemetries.xlsx');
        } catch (Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], !$e->getCode() ? 400 : $e->getCode());
            }

            return back()
                ->withErrors(['service' => $e->getMessage()])
                ->with('failed', $e->getMessage());
        }
    }

    public function exportPdf(
        Farmer $farmer,
        Land $land,
        Garden $garden,
        RscGarden $rscGarden
    ) {
        try {
            $this->checkRscAutorize(
                $farmer,
                $land,
                $garden,
                $rscGarden,
            );

            $rscGarden->load(['device:id,device_series', 'garden:id,name'])
                ->loadAvg('rscGardenTelemetries as avg_n', 'samples->n')
                ->loadAvg('rscGardenTelemetries as avg_p', 'samples->p')
                ->loadAvg('rscGardenTelemetries as avg_k', 'samples->k');

            $fileName = now()->format('YmdHis') . '_rsc-telemetries.pdf';

            $pdf = Pdf::loadView('exports.pdf.rsc-garden-telemetry', [
                'rscGarden'         => $rscGarden,
                'rscTelemetries'    => RscGardenTelemetry::query()
                    ->where('rsc_garden_id', $rscGarden->id)
                    ->get(),
            ]);

            return $pdf->setPaper('a4', 'landscape')
                ->download($fileName);
        } catch (Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], !$e->getCode() ? 400 : $e->getCode());
            }

            return back()
                ->withErrors(['service' => $e->getMessage()])
                ->with('failed', $e->getMessage());
        }
    }

    private function checkRscAutorize(
        Farmer $farmer,
        Land $land,
        Garden $garden,
        RscGarden $rscGarden
    ) : Bool {
        if (
            auth()->user()->role != 'admin' &&
            $farmer->user_id != auth()->user()->id
        ) {
            throw new Exception("Tidak bisa mengakses data ini", 403);

        }
        if ($farmer->id != $land->farmer_id) {
            throw new Exception("Lahan tidak sama dengan yang ada di petani", 403);
        }
        if (
            $farmer->id != $garden->land->farmer_id
        ) {
            throw new Exception("Petani tidak memiliki kebun ini!", 403);
        }

        return true;
    }
}
