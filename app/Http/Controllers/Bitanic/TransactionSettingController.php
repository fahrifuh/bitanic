<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\TransactionSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TransactionSettingController extends Controller
{
    public function index() : View {
        return view('bitanic.platform-fees-setting.index', [
            'transactionSetting' => TransactionSetting::firstOrFail()
        ]);
    }

    public function update(Request $request) {
        $request->validate([
            'platform_fees' => 'required|integer|min:0'
        ]);

        $transactionSetting = TransactionSetting::firstOrFail();

        $transactionSetting->update($request->only('platform_fees'));

        return back()->with('success', 'Berhasil disimpan');
    }
}
