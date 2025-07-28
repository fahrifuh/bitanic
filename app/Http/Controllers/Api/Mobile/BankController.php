<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function index() : JsonResponse {
        $banks = Bank::query()
            ->get(['id', 'name', 'code', 'picture', 'fees']);

        return response()->json([
            'message' => 'Data bank',
            'banks' => $banks
        ]);
    }
}
