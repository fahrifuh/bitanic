<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\FarmerTransaction;
use App\Models\FarmerTransactionShop;
use Heyharpreetsingh\FCM\Facades\FCMFacade;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ShopTransactionController extends Controller
{
    public function index() : View {
        $shop = auth()->user()->farmer->shop;

        $farmerTransactions = FarmerTransaction::query()
            ->with([
                'user:id,name',
            ])
            ->whereHas('farmer_transaction_shops', function(Builder $query)use($shop){
                $query->where('shop_id', $shop->id);
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('bitanic.shop.transaction.index', compact('farmerTransactions', 'shop'));
    }

    public function show($id) : View {
        $shop = auth()->user()->farmer->shop;
        $farmerTransactionShop = FarmerTransactionShop::query()
            ->with([
                'farmer_transaction' => function($query)use($id){
                    $query->where('id', $id);
                },
                'farmer_transaction.user:id,name,phone_number',
                'farmer_transaction_items',
            ])
            ->whereHas('farmer_transaction', function(Builder $query)use($id){
                $query->where('id', $id);
            })
            ->where('shop_id', $shop->id)
            ->firstOrFail();

        return view('bitanic.shop.transaction.show', compact('farmerTransactionShop'));
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

    public function updateShipping(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'resi' => 'required|string|max:255'
        ]);

        $shop = auth()->user()->farmer->shop;
        $farmerTransactionShop = FarmerTransactionShop::query()
            ->with([
                'farmer_transaction'
            ])
            ->whereHas('farmer_transaction', function(Builder $query)use($id){
                $query->where('id', $id);
            })
            ->where('shop_id', $shop->id)
            ->firstOrFail();

        if ($farmerTransactionShop->farmer_transaction->status != 'settlement') {
            return back()->with('failed', 'User belum membayar produk!');
        }

        if ($farmerTransactionShop->shipping_status != 0) {
            return back()->with('success', 'Barang sedang/sudah dikirim!');
        }

        $farmerTransactionShop->shipping_status = 1;
        $farmerTransactionShop->delivery_receipt = $request->resi;
        $farmerTransactionShop->save();

        FCMFacade::send([
            "message" => [
                "token" => "ctZP-eyHStCfH2Arm4KCo4:APA91bF_NCP2MNkQnpH38WS3QVZa05y2JM8iqhrJ2n_b7UbE4SC83EejpA3h9ry0NHGT27Ymc57XbwXHAr9ns2FWA2eZGpECMLgvg7VmdaRjR-YI5JQ5An4",
                "notification" => [
                    "body" => "Hai, pesananmu sudah dikirim. Kamu bisa check resinya di detail transaksi.",
                    "title" => "Pesanan Dikirim"
                ]
            ]
        ]);

        return back()->with('success', 'Berhasil disimpan');
    }
}
