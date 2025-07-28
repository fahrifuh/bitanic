<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class FarmerExport implements FromView, ShouldAutoSize
{
    public function __construct(protected Collection $users)
    {
        //
    }

    public function view(): View
    {
        return view('exports.users', [
            'users' => $this->users
        ]);
    }
}
