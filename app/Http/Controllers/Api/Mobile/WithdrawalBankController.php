<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalBank;
use Illuminate\Http\JsonResponse;

class WithdrawalBankController extends Controller
{
    public function index() : JsonResponse {
        $withdrawalBanks = WithdrawalBank::query()
            ->get(['id', 'name', 'code', 'picture']);

        return response()->json([
            'message' => 'Data bank',
            'withdrawal_banks' => $withdrawalBanks
        ]);
    }
}
