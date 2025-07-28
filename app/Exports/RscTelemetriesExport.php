<?php

namespace App\Exports;

use App\Models\RscTelemetri;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RscTelemetriesExport implements FromView, ShouldAutoSize
{
    public function __construct(private $land_id)
    {
    }
    public function view(): View
    {
        return view('exports.rsc-telemetries', [
            'rscTelemetries' => RscTelemetri::query()
                ->where('land_id', $this->land_id)
                ->get()
        ]);
    }
}
