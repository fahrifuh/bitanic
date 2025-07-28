<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\BalanceWithdraw;
use App\Models\Bank;
use App\Models\FarmerTransactionShop;
use App\Models\Shop;
use App\Models\User;
use App\Models\WithdrawalBank;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ShopController extends Controller
{
    public function index(): JsonResponse
    {
        $shop = auth()->user()->farmer->shop;

        if (!$shop) {
            return response()->json([
                'message' => "Anda belum membuat toko!",
                'status' => 404
            ], 404);
        }

        if ($shop->is_ktp_validated !== 1) {
            return response()->json([
                'message' => "Harap pastikan verifikasi KTP anda diterima! Upload ulang KTP jika ditolak.",
                'status' => 404
            ], 404);
        }

        $shop->load([
            'products',
        ]);

        $balance_withdraw = BalanceWithdraw::query()
            ->where('user_id', auth()->id())
            ->whereNull('is_succeed')
            ->first();

        return response()->json([
            'shop' => $shop,
            'balance_withdraw' => $balance_withdraw,
            'message' => 'Data toko',
            'status' => 200
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $shop = auth()->user()->farmer->shop;

        if ($shop !== null && $shop->is_ktp_validated === 1) {
            return response()->json([
                'message' => "Anda sudah memiliki toko",
                'status' => 400
            ], 400);
        }

        if ($shop !== null && $shop->is_ktp_validated === null) {
            return response()->json([
                'message' => "Toko anda belum diverifikasi. Harap tunggu...",
                'status' => 400
            ], 400);
        }

        $v = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'latitude'  => 'required|regex:/^(-?\d+(\.\d+)?)$/',
            'longitude' => 'required|regex:/^(-?\d+(\.\d+)?)$/',
            'address'   => 'required|string|max:1000',
            'picture'   => 'required|image|mimes:jpg,png|max:10240',
            'province_id'    => 'required|integer|min:0',
            'city_id'   => 'required|integer|min:0',
            'ktp'       => 'required|image|mimes:jpg,jpeg,png|max:5120'
        ]);

        if ($v->fails()) {
            return response()->json([
                'messages' => $v->errors(),
                'status' => 400
            ], 400);
        }


        $image = $request->file('ktp');
        $name = strtoupper(Str::random(5)) . '-' . time() . '.' . $image->extension();
        $path = Storage::putFileAs('ktp/shop', $request->file('ktp'), $name);

        Shop::create($request->only(
            'name',
            'latitude',
            'longitude',
            'address',
            'province_id',
            'city_id',
        ) + [
            'picture' => image_intervention($request->file('picture'), 'bitanic-photo/shop/', (16 / 9)),
            'ktp'   => $path,
            'farmer_id' => auth()->user()->farmer->id
        ]);

        return response()->json([
            'message' => "Data berhasil disimpan!",
            'status' => 200
        ], 200);
    }

    public function update(Request $request): JsonResponse
    {
        $shop = auth()->user()->farmer->shop;

        if (!$shop) {
            return response()->json([
                'message' => "Anda belum membuat toko!",
                'status' => 404
            ], 404);
        }

        if ($shop->is_ktp_validated !== 1) {
            return response()->json([
                'message' => "Harap pastikan verifikasi KTP anda diterima! Upload ulang KTP jika ditolak.",
                'status' => 404
            ], 404);
        }

        $v = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'latitude'  => 'required|regex:/^(-?\d+(\.\d+)?)$/',
            'longitude' => 'required|regex:/^(-?\d+(\.\d+)?)$/',
            'address'   => 'required|string|max:1000',
            'picture'   => 'nullable|image|mimes:jpg,png|max:10240',
            'province_id'    => 'required|integer|min:0',
            'city_id'    => 'required|integer|min:0',
        ]);

        if ($v->fails()) {
            return response()->json([
                'messages' => $v->errors(),
                'status' => 400
            ], 400);
        }

        $foto = $shop->picture;

        if ($request->file('picture')) {
            $foto = image_intervention($request->file('picture'), 'bitanic-photo/shop/', 16 / 9);

            if (File::exists(public_path($shop->picture))) {
                File::delete(public_path($shop->picture));
            }
        }

        $shop->update($request->only(
            'name',
            'latitude',
            'longitude',
            'address',
            'province_id',
            'city_id',
        ) + [
            'picture' => $foto
        ]);

        return response()->json([
            'message' => "Data berhasil disimpan!",
            'status' => 200
        ], 200);
    }

    public function destroy(): JsonResponse
    {
        $shop = auth()->user()->farmer->shop;

        if (!$shop) {
            return response()->json([
                'message' => "Anda belum membuat toko!",
                'status' => 404
            ], 404);
        }

        if (File::exists(public_path($shop->picture))) {
            File::delete(public_path($shop->picture));
        }

        $shop->delete();

        return response()->json([
            'message' => "Data berhasil dihapus!",
            'status' => 200
        ], 200);
    }

    public function getBeingPackedTransactions() : JsonResponse
    {
        $shop = auth()->user()->farmer->shop;

        if (!$shop) {
            return response()->json([
                'message' => "Anda belum membuat toko!",
                'status' => 404
            ], 404);
        }

        if ($shop->is_ktp_validated !== 1) {
            return response()->json([
                'message' => "Harap pastikan verifikasi KTP anda diterima! Upload ulang KTP jika ditolak.",
                'status' => 404
            ], 404);
        }

        $farmer_transaction_shops = FarmerTransactionShop::query()
            ->with([
                'farmer_transaction:id,bank_name,bank_fees,user_name,user_recipient_name,user_recipient_phone_number,user_recipient_province_id,user_recipient_city_id,user_recipient_address',
                'farmer_transaction_items' => function($qf){
                    $qf->leftJoin(
                            'products',
                            'farmer_transaction_items.product_id', '=', 'products.id'
                        )
                        ->select([
                            'farmer_transaction_items.id',
                            'farmer_transaction_items.farmer_transaction_shop_id',
                            'farmer_transaction_items.product_id',
                            'products.picture as product_pictures',
                            'farmer_transaction_items.product_name',
                            'farmer_transaction_items.product_price',
                            'farmer_transaction_items.quantity',
                            'farmer_transaction_items.discount',
                            'farmer_transaction_items.total',
                        ]);
                },
            ])
            ->where([
                ['shop_id', $shop->id],
                ['shipping_status', 0]
            ])
            ->whereHas('farmer_transaction', function($query){
                $query->where('status', 'settlement');
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json([
            'message' => 'Settlement Transactions',
            'transactions' => $farmer_transaction_shops
        ]);
    }

    public function updateDeliveryReceipt(Request $request, FarmerTransactionShop $farmerTransactionShop) : JsonResponse
    {
        $request->validate([
            'delivery_receipt' => 'required|string|max:255'
        ]);

        $shop = auth()->user()->farmer->shop;

        if (!$shop) {
            return response()->json([
                'message' => "Anda belum membuat toko!",
                'status' => 404
            ], 404);
        }

        if ($shop->is_ktp_validated !== 1) {
            return response()->json([
                'message' => "Harap pastikan verifikasi KTP anda diterima! Upload ulang KTP jika ditolak.",
                'status' => 404
            ], 404);
        }

        if ($farmerTransactionShop->shop_id != $shop->id) {
            return response()->json([
                'message' => "Bukan transaksi anda!",
                'status' => 403
            ], 403);
        }

        $farmerTransactionShop->load(['farmer_transaction']);

        if ($farmerTransactionShop->farmer_transaction->status != 'settlement') {
            return response()->json([
                'message' => 'Pembeli belum membayar pesanan'
            ], 400);
        }

        if ($farmerTransactionShop->shipping_status != 0) {
            return response()->json([
                'message' => 'Pesanan sedang dikirim/sudah diterima oleh pembeli'
            ], 400);
        }

        $farmerTransactionShop->delivery_receipt = $request->delivery_receipt;
        $farmerTransactionShop->shipping_status = 1;
        $farmerTransactionShop->save();

        $fcmTokens = User::query()
            ->where('id', $farmerTransactionShop->farmer_transaction->user_id)
            ->whereNotNull('firebase_token')
            ->pluck('firebase_token')
            ->all();

        if ($fcmTokens) {
            $result = firebaseNotification($fcmTokens, [
                "notification" => [
                    "title" => "Pesanan Sedang Dikirim!",
                    "body" => "Pesanan anda sudah diberikan ke pihak pengiriman oleh toko!"
                ],
            ]);
        }

        return response()->json([
            'message' => 'Resi berhasil disimpan dan status pengiriman sudah diubah!'
        ],200);
    }

    public function cancelTransaction(Request $request, FarmerTransactionShop $farmerTransactionShop) : JsonResponse
    {
        $request->validate([
            'delivery_receipt' => 'required|string|max:255'
        ]);

        $shop = auth()->user()->farmer->shop;

        if (!$shop) {
            return response()->json([
                'message' => "Anda belum membuat toko!",
                'status' => 404
            ], 404);
        }

        if ($shop->is_ktp_validated !== 1) {
            return response()->json([
                'message' => "Harap pastikan verifikasi KTP anda diterima! Upload ulang KTP jika ditolak.",
                'status' => 404
            ], 404);
        }

        if ($farmerTransactionShop->shop_id != $shop->id) {
            return response()->json([
                'message' => "Bukan transaksi anda!",
                'status' => 403
            ], 403);
        }

        $farmerTransactionShop->load(['farmer_transaction']);

        try {
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = (bool) config('midtrans.is_production');
            \Midtrans\Config::$isSanitized = (bool) config('midtrans.is_sanitized');
            \Midtrans\Config::$is3ds = (bool) config('midtrans.is_3ds');

            $status_code = \Midtrans\Transaction::cancel($farmerTransactionShop->farmer_transaction->code);

            if ($status_code == "200") {
                $farmerTransactionShop->farmer_transaction->status = 'cancel';
                $farmerTransactionShop->push();

                $fcmTokens = User::query()
                    ->where('id', $farmerTransactionShop->farmer_transaction->user_id)
                    ->whereNotNull('firebase_token')
                    ->pluck('firebase_token')
                    ->all();

                if ($fcmTokens) {
                    $result = firebaseNotification($fcmTokens, [
                        "notification" => [
                            "title" => "Pesanan Dibatalkan!",
                            "body" => "Toko membatalkan pesanan anda!"
                        ],
                    ]);
                }

                return response()->json([
                    'message' => "Berhasil dibatalkan!"
                ], 200);
            }

            return response()->json([
                'message' => "Merchant cannot modify the status of the transaction"
            ], 412);
        } catch (\Exception $ex) {
            return response()->json([
                'message' => $ex->getMessage()
            ], 500);
        }
    }

    public function getShippedReceivedTransactions() : JsonResponse
    {
        $shop = auth()->user()->farmer->shop;

        if (!$shop) {
            return response()->json([
                'message' => "Anda belum membuat toko!",
                'status' => 404
            ], 404);
        }

        if ($shop->is_ktp_validated !== 1) {
            return response()->json([
                'message' => "Harap pastikan verifikasi KTP anda diterima! Upload ulang KTP jika ditolak.",
                'status' => 404
            ], 404);
        }

        $transactions = FarmerTransactionShop::query()
            ->with([
                'farmer_transaction:id,bank_name,bank_fees,user_name,user_recipient_name,user_recipient_phone_number,user_recipient_province_id,user_recipient_city_id,user_recipient_address',
                'farmer_transaction_items' => function($qf){
                    $qf->leftJoin(
                            'products',
                            'farmer_transaction_items.product_id', '=', 'products.id'
                        )
                        ->select([
                            'farmer_transaction_items.id',
                            'farmer_transaction_items.farmer_transaction_shop_id',
                            'farmer_transaction_items.product_id',
                            'products.picture as product_pictures',
                            'farmer_transaction_items.product_name',
                            'farmer_transaction_items.product_price',
                            'farmer_transaction_items.quantity',
                            'farmer_transaction_items.discount',
                            'farmer_transaction_items.total',
                        ]);
                },
            ])
            ->where('shop_id', $shop->id)
            ->whereIn('shipping_status', [1,2])
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json([
            'message' => 'Data barang yang sedang dikirim atau sudah diterima pembeli!',
            'transactions' => $transactions
        ], 200);
    }

    public function updateBankAccount(Request $request) : JsonResponse {
        $shop = auth()->user()->farmer->shop;

        if (!$shop) {
            return response()->json([
                'message' => "Anda belum membuat toko!",
                'status' => 404
            ], 404);
        }

        if ($shop->is_ktp_validated !== 1) {
            return response()->json([
                'message' => "Harap pastikan verifikasi KTP anda diterima! Upload ulang KTP jika ditolak.",
                'status' => 404
            ], 404);
        }

        $request->validate([
            'bank_account' => 'required|numeric|digits_between:10,20',
            'bank_id' => 'required|integer|min:0',
        ]);

        $withdrawalBank = WithdrawalBank::findOrFail($request->bank_id);

        $shop->update($request->only('bank_account') + [
            'bank_type' => $withdrawalBank->name,
        ]);

        return response()->json([
            'message' => 'Bank Account Updated',
        ], 200);
    }
}
