<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\BalanceWithdraw;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BalanceWithdrawController extends Controller
{
    public function index(): JsonResponse {
        $balanceWithdraws = BalanceWithdraw::query()
            ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json([
            'message' => 'Data penarikan saldo',
            'balanceWithdraws' => $balanceWithdraws
        ]);
    }

    public function store(Request $request): JsonResponse {
        $shop = auth()->user()->farmer->shop;

        if (!$shop->bank_account) {
            return response()->json([
                'message' => 'Terjadi kesalahan!',
                'errors' => [
                    'bank_account' => ['Harap isi no rekening!']
                ]
            ], 422);
        }

        if ($shop->balance < 10000) {
            return response()->json([
                'message' => 'Terjadi kesalahan!',
                'errors' => [
                    'balance' => ['Saldo Anda tidak mencukupi!']
                ]
            ], 422);
        }

        $user_id = auth()->id();

        $checkPrevWithdraw = BalanceWithdraw::query()
            ->where('user_id', $user_id)
            ->whereNull('is_succeed')
            ->count();

        if ($checkPrevWithdraw > 0) {
            return response()->json([
                'message' => 'Error have ocured',
                'errors' => [
                    'withdraw' => ['Permintaan sebelumnya masih diproses. Harap tunggu hingga proses selesai!']
                ],
            ], 422);
        }

        BalanceWithdraw::create([
            'user_id' => $user_id,
            'user_name' => auth()->user()->name,
            'total_balance' => $shop->balance,
            'bank_account' => $shop->bank_account,
            'bank_type' => $shop->bank_type,
        ]);

        return response()->json([
            'message' => 'Permintaan penarikan saldo sebesar Rp '. number_format($shop->balance) .'  berhasil dibuat!',
        ]);
    }
}
