<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\FarmerTransaction;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FarmerTransactionController extends Controller
{
    public function index() : View {
        $farmerTransactions = FarmerTransaction::query()
            ->when(request()->query('search'), function($query, $a){
                $search = request()->query('search');
                return $query->where(function($query)use($search){
                    $query->where('code', 'LIKE', '%'.$search.'%')
                        ->orWhereHas('user', function($fm)use($search){
                            $fm->where('name', 'LIKE', '%'.$search.'%');
                        });
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('bitanic.komodity-transaction.index', compact('farmerTransactions'));
    }

    public function show(FarmerTransaction $farmerTransaction) : View {
        $farmerTransaction->load([
            'user:id,name,phone_number',
            'farmer_transaction_shops.farmer_transaction_items',
        ]);
        return view('bitanic.komodity-transaction.show', compact('farmerTransaction'));
    }

    public function updateStatus(Request $request, FarmerTransaction $farmerTransaction): JsonResponse
    {
        try {
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = (bool) config('midtrans.is_production');
            \Midtrans\Config::$isSanitized = (bool) config('midtrans.is_sanitized');
            \Midtrans\Config::$is3ds = (bool) config('midtrans.is_3ds');

            $status = (object) \Midtrans\Transaction::status($farmerTransaction->code);

            if ($farmerTransaction && $farmerTransaction->status != $status->transaction_status) {
                $farmerTransaction->status = $status->transaction_status;
                $farmerTransaction->save();
            }

            return response()->json([
                'message' => "Berhasil diupdate!",
                'a' => $status->transaction_status,
            ], 200);
        } catch (\Exception $ex) {
            //Exception $ex;
            return response()->json([
                'message' => $ex->getMessage()
            ], 500);
        }
    }
}
