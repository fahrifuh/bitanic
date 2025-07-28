<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\BalanceWithdraw;
use Illuminate\Http\Request;

class BalanceWithdrawUserController extends Controller
{
    public function index() {
        $shop = auth()->user()->farmer->shop;
        $balanceWithdraws = BalanceWithdraw::query()
            ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('bitanic.shop.balance-withdraw.index', compact('shop', 'balanceWithdraws'));
    }

    public function store(Request $request) {
        $shop = auth()->user()->farmer->shop;

        if (!$shop->bank_account) {
            return redirect()->back()->withErrors([
                'bank_account' => ['Harap isi no rekening terlebih dahulu!']
            ]);
        }
        if ($shop->balance <= 10000) {
            return redirect()->back()->withErrors([
                'messages' => ['Saldo Anda tidak mencukupi!']
            ]);
        }

        $user_id = auth()->id();

        $checkPrevWithdraw = BalanceWithdraw::query()
            ->where('user_id', $user_id)
            ->whereNull('is_succeed')
            ->count();

        if ($checkPrevWithdraw > 0) {
            return redirect()->back()->withErrors([
                'data' => ['Permintaan sebelumnya masih diproses. Harap tunggu hingga proses selesai!'],
            ]);
        }

        BalanceWithdraw::create([
            'user_id' => $user_id,
            'user_name' => auth()->user()->name,
            'total_balance' => $shop->balance,
            'bank_account' => $shop->bank_account,
            'bank_type' => $shop->bank_type,
        ]);

        return redirect()->back()->with('success', 'Permintaan penarikan saldo berhasil dibuat!');
    }
}
