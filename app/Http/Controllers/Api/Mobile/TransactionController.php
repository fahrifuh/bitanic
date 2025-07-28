<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Bank;
use App\Models\BitanicProduct;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\TransactionSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    function store(Request $request) : JsonResponse
    {
        $request->validate([
            'courier' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'address_id' => 'required|integer|min:0',
            'quantity' => 'required|integer|min:1',
            'platform_fees' => 'required|integer|min:0',
            'discount' => 'nullable|integer|min:0|max:100',
            'bitanic_product_id' => 'required|integer|min:0',
            'shipping_price' => 'required|integer|min:0',
            'payment_method_code' => ['required', 'string', 'max:255'],
        ]);

        $bitanicProduct = BitanicProduct::find($request->bitanic_product_id);

        if (!$bitanicProduct) {
            return response()->json([
                'message' => 'Produk tidak ditemukan',
            ], 404);
        }

        $bank = Bank::query()
            ->firstWhere('code', $request->payment_method_code);

        if (!$bank) {
            return response()->json([
                'message' => 'Bank tidak ditemukan',
            ], 404);
        }

        $user_id = auth()->id();

        $address = Address::query()
            ->where('user_id', $user_id)
            ->find($request->address_id);

        if (!$address) {
            return response()->json([
                'message' => 'Alamat tidak ditemukan',
            ], 404);
        }

        try {
            $product_discount = ($bitanicProduct->discount != null) ? floor($bitanicProduct->price * ($bitanicProduct->discount / 100)) : 0;
            $request_discount = ($request->discount != null) ? floor($bitanicProduct->price * $request->quantity) * ($request->discount / 100) : 0;
            $discount = ($product_discount * $request->quantity) + $request_discount;
            $total_items = $request->shipping_price + ($bitanicProduct->price * $request->quantity) - floor($product_discount * $request->quantity);
            $subtotal = $request->shipping_price + ($bitanicProduct->price * $request->quantity) - floor($discount);
            $bank_fees = 0;

            if ($bank->fees) {
                foreach ($bank->fees as $fee) {
                    switch ($fee['type']) {
                        case 0:
                            $bank_fees += $fee['fee'];
                            break;
                        case 1:
                            $bank_fees += ceil(($subtotal + ceil($request->platform_fees)) * ($fee['fee'] / 100));
                            break;
                    }
                }
            }

            // $half_request_discount = ($request_discount > 0) ? floor($request_discount / $request->quantity) : 0;
            $total = $subtotal + ceil($request->platform_fees) + $bank_fees;
            $code = now()->timestamp . "" . Str::random(40);
            $params = $this->buildMidtransParameters([
                'transaction_code' => $code,
                'price' => $total,
                'payment_method' => $request->payment_method_code,
                // 'item_id' => $bitanicProduct->id,
                // 'item_price' => (($bitanicProduct->price + ceil($request->shipping_price / $request->quantity) + ceil($request->platform_fees / $request->quantity)) - ($product_discount + $half_request_discount)),
                // 'item_quantity' => $request->quantity,
                // 'item_name' => $bitanicProduct->name,
                'ship_address' => $address->address,
                'ship_postal_code' => $address->postal_code,
            ]);

            $midtrans = $this->callMidtrans($params);

            $transaction = Transaction::create([
                'code' => $code,
                'midtrans_token' => $midtrans['token'],
                'platform_fees' => $request->platform_fees,
                'discount' => $request->discount,
                'total' => $total,
                'user_id' => $user_id,
                'status' => 'pending',
                'address_id' => $request->address_id,
                'province_id' => $address->province_id,
                'city_id' => $address->city_id,
                'user_recipient_name' => $address->recipient_name,
                'user_recipient_phone_number' => $address->recipient_phone_number,
                'user_address' => $address->address,
                'courier' => $request->courier,
                'type' => $request->type,
                'bank_name' => $bank->name,
                'bank_code' => $bank->code,
                'bank_fees' => $bank_fees,
            ]);

            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'bitanic_product_id' => $bitanicProduct->id,
                'province_id' => $bitanicProduct->province_id,
                'city_id' => $bitanicProduct->city_id,
                'name' => $bitanicProduct->name,
                'price' => $bitanicProduct->price,
                'discount' => $bitanicProduct->discount,
                'weight' => $bitanicProduct->weight,
                'shipping_price' => $request->shipping_price,
                'quantity' => $request->quantity,
                'total' => $total_items,
            ]);

            return response()->json($midtrans);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }
    private function callMidtrans(array $params)
    {
        // \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        // \Midtrans\Config::$isProduction = (bool) env('MIDTRANS_IS_PRODUCTION');
        // \Midtrans\Config::$isSanitized = (bool) env('MIDTRANS_IS_SANITIZED');
        // \Midtrans\Config::$is3ds = (bool) env('MIDTRANS_IS_3DS');
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = (bool) config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = (bool) config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = (bool) config('midtrans.is_3ds');

        $createTransaction = \Midtrans\Snap::createTransaction($params);

        return [
            'redirect_url' => $createTransaction->redirect_url,
            'token' => $createTransaction->token
        ];
    }

    private function buildMidtransParameters(array $params)
    {
        $transactionDetails = [
            'order_id' => $params['transaction_code'],
            'gross_amount' => $params['price']
        ];

        // $itemDetails = [
        //     [
        //         'id' => $params['item_id'],
        //         'price' => $params['item_price'],
        //         'quantity' => $params['item_quantity'],
        //         'name' => $params['item_name']
        //     ]
        // ];

        $user = auth()->user();
        $splitName = $this->splitName($user->name);

        $shippingAddress = [
            'first_name' => $splitName['first_name'],
            'last_name' => $splitName['last_name'],
            'phone' => $user->phone_number,
            'address' => $params['ship_address'],
            'postal_code' => $params['ship_postal_code'],
            'country_code' => "IDN",
        ];

        $customerDetails = [
            'first_name' => $splitName['first_name'],
            'last_name' => $splitName['last_name'],
            'phone' => $user->phone_number,
            'shipping_address' => $shippingAddress,
        ];

        $enabledPayment = [
            $params['payment_method']
        ];

        return [
            'transaction_details' => $transactionDetails,
            // 'item_details' => $itemDetails,
            'customer_details' => $customerDetails,
            'enabled_payments' => $enabledPayment,
        ];
    }

    private function splitName($fullname)
    {
        $name = explode(' ', $fullname);

        $lastName = count($name) > 1 ? array_pop($name) : $fullname;
        $firstName = implode(' ', $name);

        return [
            'first_name' => $firstName,
            'last_name' => $lastName
        ];
    }

    public function getAllTransactions(Request $request) : JsonResponse {
        $status = $request->query('status');
        $shipping_status = $request->query('shipping_status');
        $search = $request->query('search');

        $transactions = Transaction::query()
            ->with([
                'transaction_item:id,transaction_id,bitanic_product_id,price',
                'transaction_item.bitanic_product:id,name,picture',
            ])
            ->select(['id', 'code', 'status', 'midtrans_token', 'courier', 'type', 'shipping_status', 'bank_name', 'total', 'created_at'])
            ->where('user_id', auth()->id())
            ->when($status, function($query, $status){
                return $query->where('status', trim($status));
            })
            ->when(in_array($shipping_status, ['0','1','2']), function($query, $shipping_status1)use($shipping_status){
                return $query->where('shipping_status', trim($shipping_status));
            })
            ->when($search, function($query, $search1)use($search){
                return $query->whereHas('transaction_item.bitanic_product', function($query1)use($search){
                    $query1->where('name', 'LIKE', '%' . trim($search) . '%');
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json([
            'message' => 'Transaction',
            'transaction' => $transactions
        ]);
    }

    public function getDetailTransaction(Request $request, Transaction $transaction) : JsonResponse {
        $transaction->load([
            'transaction_item',
            'transaction_item.bitanic_product:id,name,picture',
        ]);
        return response()->json([
            'message' => 'Detail transaksi',
            'transaction' => $transaction
        ]);
    }

    public function cancelTransaction(Request $request, Transaction $transaction): JsonResponse
    {
        try {
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = (bool) config('midtrans.is_production');
            \Midtrans\Config::$isSanitized = (bool) config('midtrans.is_sanitized');
            \Midtrans\Config::$is3ds = (bool) config('midtrans.is_3ds');

            $status_code = \Midtrans\Transaction::cancel($transaction->code);

            if ($status_code == "200") {
                $transaction->update([
                    'status' => "cancel"
                ]);

                return response()->json([
                    'message' => "Berhasil dibatalkan!"
                ], 200);
            }

            return response()->json([
                'message' => "Merchant cannot modify the status of the transaction"
            ], 412);
        } catch (\Exception $ex) {
            //Exception $ex;
            return response()->json([
                'message' => $ex->getMessage()
            ], 500);
        }
    }

    public function acceptedShipping(Request $request, Transaction $transaction) : JsonResponse
    {
        if ($transaction->status != "settlement") {
            return response()->json([
                'message' => 'Anda belum selesai melakukan pembayaran produk'
            ], 400);
        }

        $transaction->update([
            'shipping_status' => 2
        ]);

        return response()->json([
            'message' => 'Berhasil diupdate'
        ], 200);
    }

    public function platformFees() : JsonResponse {
        return response()->json([
            'fee' => TransactionSetting::first()
        ]);
    }
}
