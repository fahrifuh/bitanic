<?php

namespace App\Exports;

use App\Models\HydroponicDeviceTelemetry;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class HydroponicDeviceTelemetryExport implements FromView, ShouldAutoSize
{
    public function __construct(private Collection $hydroponicDeviceTelemetries)
    {
    }

    public function view(): View
    {
        return view('exports.hydroponic-telemetry', [
            'hydroponicDeviceTelemetries' => $this->hydroponicDeviceTelemetries
        ]);
    }
}
