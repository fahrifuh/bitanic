<?php

namespace App\Exports;

use App\Models\LiteUser;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class FarmerLiteExport implements FromView, ShouldAutoSize
{
    /**
     * @param LiteUser[] $liteUsers
     */
    public function __construct(protected Collection $liteUsers)
    {
        //
    }

    public function view(): View
    {
        return view('exports.lite-users', [
            'liteUsers' => $this->liteUsers
        ]);
    }
}
