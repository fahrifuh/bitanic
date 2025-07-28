<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\BalanceWithdraw;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class BalanceWithdrawAdminController extends Controller
{
    public function index() {
        $balanceWithdraws = BalanceWithdraw::query()
            ->when(request()->query('search'), function($query, $a){
                $search = request()->query('search');
                return $query->where(function($query)use($search){
                    $query->where('user_name', 'LIKE', '%'.$search.'%')
                        ->orWhere('admin_name', 'LIKE', '%'.$search.'%');
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10);
        return view('bitanic.balance-withdraw.admin.index', [
            'balanceWithdraws' => $balanceWithdraws,
        ]);
    }

    public function updateStatus(Request $request, BalanceWithdraw $balanceWithdraw) {
        $request->validate([
            'is_accepted' => 'required|integer|in:0,1',
        ], [
            'is_accepted.required' => 'Status Transfer wajib diisi.'
        ]);

        if ($balanceWithdraw->is_succeed !== null) {
            return redirect()->back()->withErrors([
                'messages' => ['Proses penarikan ini sudah selesai, anda tidak bisa mengubahnya lagi!']
            ]);
        }

        $balanceWithdraw->admin_id = auth()->id();
        $balanceWithdraw->admin_name = auth()->user()->name;
        $balanceWithdraw->is_succeed = $request->is_accepted;
        $balanceWithdraw->transfer_datetime = now();
        $balanceWithdraw->save();

        if ($request->is_accepted == 1) {
            Shop::query()
                ->whereHas('farmer', function(Builder $query)use($balanceWithdraw){
                    $query->where('user_id', $balanceWithdraw->user_id);
                })
                ->update([
                    'balance' => 0,
                ]);
        }

        return redirect()->back()->with('success', 'Berhasil disimpan');
    }
}
