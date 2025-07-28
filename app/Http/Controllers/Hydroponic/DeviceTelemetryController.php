<?php

namespace App\Http\Controllers\Hydroponic;

use App\Exports\HydroponicDeviceTelemetryExport;
use App\Http\Controllers\Controller;
use App\Models\HydroponicDevice;
use App\Models\HydroponicDeviceTelemetry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class DeviceTelemetryController extends Controller
{
    public function index(HydroponicDevice $hydroponicDevice) : View {
        $hydroponicDeviceTelemetries = HydroponicDeviceTelemetry::query()
            ->where('hydroponic_device_id', $hydroponicDevice->id)
            ->latest('created_at')
            ->paginate(10);

        return view('bitanic.hydroponic.device.telemetry.index', compact('hydroponicDeviceTelemetries', 'hydroponicDevice'));
    }

    public function exportExcel(HydroponicDevice $hydroponicDevice)
    {
        $queryFrom = request()->query('from');
        $queryTo = request()->query('to');

        Validator::make(
                [
                    'from' => $queryFrom,
                    'to' => $queryTo
                ],
                [
                    'from' => 'required|date|date_format:Y-m-d',
                    'to' => 'nullable|date|after_or_equal:from|date_format:Y-m-d',
                ],
                [],
                [
                    'from' => 'Awal',
                    'to' => 'Akhir',
                ]
            )
            ->validate();

        $hydroponicDeviceTelemetries = HydroponicDeviceTelemetry::query()
            ->where('hydroponic_device_id', $hydroponicDevice->id)
            ->when((!$queryTo), function(Builder $query, $a)use($queryFrom){
                $query->whereDate('created_at', $queryFrom);
            })
            ->when(($queryTo), function(Builder $query, $a)use($queryFrom, $queryTo){
                $query->whereDate('created_at', '>=', $queryFrom)
                ->whereDate('created_at', '<=', $queryTo);
            })
            ->latest('created_at')
            ->get();

        if ($hydroponicDeviceTelemetries->count() == 0) {
            return redirect()
                ->back()
                ->with('failed', 'Tidak ada data');
        }

        return Excel::download(
            new HydroponicDeviceTelemetryExport($hydroponicDeviceTelemetries),
            'hydroponic-telemetry.xlsx'
        );
    }
}
