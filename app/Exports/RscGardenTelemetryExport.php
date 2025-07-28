<?php

namespace App\Exports;

use App\Models\RscGarden;
use App\Models\RscGardenTelemetry;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RscGardenTelemetryExport implements FromView, ShouldAutoSize
{
    public function __construct(protected RscGarden $rscGarden)
    {
    }

    public function view(): View
    {
        return view('exports.rsc-garden-telemetry', [
            'rscTelemetries' => RscGardenTelemetry::query()
                ->where('rsc_garden_id', $this->rscGarden->id)
                ->get(),
        ]);
    }
}
