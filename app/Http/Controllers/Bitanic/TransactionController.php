<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function index() : View {
        $transactions = Transaction::query()
            ->with([
                'user:id,name',
                'transaction_item:id,transaction_id,bitanic_product_id',
                'transaction_item.bitanic_product:id,name'
            ])
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

        return view('bitanic.transaction.index', compact('transactions'));
    }

    public function show(Transaction $transaction) : View {
        $transaction->load([
            'address',
            'user:id,name,phone_number',
            'user.farmer:id,user_id,address',
            'transaction_item',
            'transaction_item.bitanic_product:id,name,picture',
        ]);

        $shipping_text = '-';

        if ($transaction->status == 'settlement') {
            switch ($transaction->shipping_status) {
                case 0:
                    $shipping_text = 'Sedang Dikemas';
                    break;
                case 1:
                    $shipping_text = 'Sedang Dikirim';
                    break;
                case 2:
                    $shipping_text = 'Diterima';
                    break;

                default:
                    $shipping_text = '-';
                    break;
            }
        }

        return view('bitanic.transaction.show', compact('transaction', 'shipping_text'));
    }

    public function updateStatus(Request $request, Transaction $transaction): JsonResponse
    {
        try {
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = (bool) config('midtrans.is_production');
            \Midtrans\Config::$isSanitized = (bool) config('midtrans.is_sanitized');
            \Midtrans\Config::$is3ds = (bool) config('midtrans.is_3ds');

            $status = (object) \Midtrans\Transaction::status($transaction->code);

            if ($transaction && $transaction->status != $status->transaction_status) {
                $transaction->update([
                    'status' => $status->transaction_status
                ]);
            }

            return response()->json([
                'message' => "Berhasil diupdate!"
            ], 200);
        } catch (\Exception $ex) {
            //Exception $ex;
            return response()->json([
                'message' => $ex->getMessage()
            ], 500);
        }
    }

    public function updateShipping(Request $request, Transaction $transaction): RedirectResponse
    {
        $request->validate([
            'resi' => 'required|string|max:255'
        ]);

        if ($transaction->status != 'settlement') {
            return back()->with('failed', 'User belum membayar produk!');
        }

        if ($transaction->shipping_status != 0) {
            return back()->with('success', 'Barang sedang/sudah dikirim!');
        }

        $transaction->shipping_status = 1;
        $transaction->delivery_receipt = $request->resi;
        $transaction->save();

        return back()->with('success', 'Berhasil disimpan');
    }
}
