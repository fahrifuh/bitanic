<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Bank;
use App\Models\Cart;
use App\Models\FarmerTransaction;
use App\Models\FarmerTransactionShop;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Heyharpreetsingh\FCM\Facades\FCMFacade;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MarketplaceController extends Controller
{
    public function getProducts(Request $request): JsonResponse
    {
        $shop = auth()->user()->farmer->shop;

        $products = Product::query()
            ->select(['id', 'name', 'category', 'picture', 'price', 'stock', 'stock_metric', 'shop_id', 'crop_for_sale_id', 'discount'])
            ->with([
                'shop:id,name,address,latitude,longitude',
                'crop_for_sale:id,picture,name',
            ])
            ->when($request->has('search') && $request->query('search'), function ($query, $bool) use ($request) {
                $search = '%' . $request->query('search') . '%';
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', $search)
                        ->orWhereHas('shop', function ($shop) use ($search) {
                            $shop->where('name', 'LIKE', $search);
                        });
                });
            })
            ->when($shop, function ($query, $bool) use ($shop) {
                return $query->where('shop_id', '<>', $shop->id);
            })
            ->when(
                $request->has('category') &&
                    in_array($request->query('category'), ['buah', 'sayur']),
                function ($query, $bool) use ($request) {
                    return $query->where('category', $request->query('category'));
                }
            )
            ->when(
                $request->has('orderby') &&
                    in_array($request->query('orderby'), [
                        'price_high_low',
                        'price_low_high',
                        'name_asc',
                        'name_desc'
                    ]),
                function ($query, $bool) use ($request) {
                    switch ($request->query('orderby')) {
                        case 'price_high_low':
                            return $query->orderBy('price', 'desc');
                            break;
                        case 'price_low_high':
                            return $query->orderBy('price', 'asc');
                            break;
                        case 'name_asc':
                            return $query->orderBy('name', 'asc');
                            break;
                        case 'name_desc':
                            return $query->orderBy('name', 'desc');
                            break;
                    }
                }
            )
            ->get();

        return response()->json([
            'message' => "List semua data produk",
            'status' => 200,
            'products' => $products,
        ], 200);
    }

    public function detailProduct(Request $request, Product $product): JsonResponse
    {
        $product->load([
            'shop:id,name,address,latitude,longitude,picture',
            'crop_for_sale:id,picture,name,days,created_at',
        ]);

        return response()->json([
            'message' => 'Detail data produk ' . $product->name,
            'status' => 200,
            'product' => $product
        ], 200);
    }

    public function payout(Request $request): JsonResponse
    {
        $request->validate([
            'address_id' => 'required|integer|min:0',
            'platform_fees' => 'required|integer|min:0',
            'payment_method_code' => ['required', 'string', 'max:255'],
            'shops' => 'required|array|size:1',
            'shops.*.courier' => 'required|string|max:255',
            'shops.*.type' => 'required|string|max:255',
            'shops.*.id' => 'required|integer|min:1|distinct',
            'shops.*.shipping_price' => 'required|integer|min:0',
            'shops.*.message' => 'nullable|string|max:250',
            'shops.*.products' => 'required|array|min:1',
            'shops.*.products.*.id' => 'required|integer|min:1',
            'shops.*.products.*.quantity' => 'required|integer|min:1',
        ]);

        $farmer_id = auth()->user()->farmer->id;

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

        $now = now();

        $formatedShops = collect();

        $id_products = collect($request->shops)
            ->map(
                fn($shop, $key) => collect($shop["products"])
                    ->map(fn($products, $key) => $products["id"])
                    ->all()
            )
            ->flatten()
            ->all();

        $model_shops = Shop::query()
            ->with([
                'products' => function ($p_query) use ($id_products) {
                    $p_query->whereIn('id', $id_products);
                },
                'farmer:id,user_id',
            ])
            ->whereIn('id', collect($request->shops)->map(fn($shop, $key) => $shop["id"])->all())
            ->where('farmer_id', '<>', $farmer_id)
            ->get();

        $collect_shops = collect($request->shops);
        $format_shops = collect();
        $format_products = collect();
        $mindtrans_items = collect();
        $user_ids = collect();
        $shops_total = 0;

        foreach ($model_shops as $shop) {
            $current_shop = $collect_shops->firstWhere('id', $shop->id);
            $products_total = 0;
            $user_ids->push($shop->farmer->user_id);

            foreach ($shop->products as $product) {
                $current_product = collect($current_shop["products"])->firstWhere("id", $product->id);
                $discount_price = ($product->discount && $product->discount > 0)
                    ? floor($product->price * ($product->discount / 100))
                    : 0;
                $products_total += ($product->price - $discount_price) * $current_product["quantity"];
                $format_products->push([
                    "shop_id" => $current_shop["id"],
                    "product_id" => $product->id,
                    "product_name" => $product->name,
                    "product_price" => $product->price,
                    "product_weight" => $product->weight,
                    "discount" => $product->discount,
                    "quantity" => $current_product["quantity"],
                    "total" => ($product->price - $discount_price) * $current_product["quantity"],
                    "created_at" => $now,
                ]);
                // $mindtrans_items->push([
                //     'item_id' => $product->id,
                //     'item_price' => (($product->price - $discount_price) + ceil($current_shop["shipping_price"] / count($shop->products))),
                //     'item_quantity' => $current_product["quantity"],
                //     'item_name' => $product->name,
                // ]);
            }

            $format_shops->push([
                "shop_id" => $shop->id,
                "shop_name" => $shop->name,
                "province_id" => $shop->province_id,
                "city_id" => $shop->city_id,
                "latitude" => $shop->latitude,
                "longitude" => $shop->longitude,
                "courier" => $current_shop["courier"],
                "type" => $current_shop["type"],
                "subtotal" => $products_total,
                "total_shipping" => $current_shop["shipping_price"],
                "total" => $products_total + $current_shop["shipping_price"],
                "message" => $current_shop["message"],
                "created_at" => $now,
            ]);

            $shops_total += $products_total + $current_shop["shipping_price"];
        }

        try {
            DB::beginTransaction();
            $subtotal_transaction = $shops_total;
            $bank_fees = 0;
            $discount_transaction = 0;

            if ($bank->fees) {
                foreach ($bank->fees as $fee) {
                    switch ($fee['type']) {
                        case 0:
                            $bank_fees += $fee['fee'];
                            break;
                        case 1:
                            $bank_fees += ceil(($shops_total + $request->platform_fees) * ($fee['fee'] / 100));
                            break;
                    }
                }
            }

            $total = $subtotal_transaction + $request->platform_fees + $bank_fees;
            $code = now()->timestamp . "" . Str::random(40);
            $params = $this->buildMidtransParameters([
                'transaction_code' => $code,
                'price' => $total,
                'payment_method' => $request->payment_method_code,
                // 'items' => $mindtrans_items->all(),
                'ship_address' => $address->address,
                'ship_postal_code' => $address->postal_code,
            ]);

            $midtrans = $this->callMidtrans($params);

            $farmer_transaction_id = DB::table('farmer_transactions')->insertGetId([
                "code" => $code,
                "midtrans_token" => $midtrans['token'],
                "status" => 'pending',
                "bank_name" => $bank->name,
                "bank_code" => $bank->code,
                "bank_fees" => $bank_fees,
                "subtotal" => $subtotal_transaction,
                "discount" => null,
                "platform_fees" => $request->platform_fees,
                "total" => $total,
                "user_id" => $user_id,
                "user_name" => auth()->user()->name,
                "address_id" => $address->id,
                'user_recipient_name' => $address->recipient_name,
                'user_recipient_phone_number' => $address->recipient_phone_number,
                'user_recipient_province_id' => $address->province_id,
                'user_recipient_city_id' => $address->city_id,
                'user_recipient_address' => $address->address,
                "created_at" => $now,
            ]);

            DB::table('farmer_transaction_shops')->insert(
                $format_shops->map(fn($shop, $key) => array_merge($shop, ['farmer_transaction_id' => $farmer_transaction_id]))->all()
            );

            $db_shops = DB::table('farmer_transaction_shops')->where('farmer_transaction_id', $farmer_transaction_id)->get(['id', 'shop_id']);

            DB::table('farmer_transaction_items')->insert(
                $format_products
                    ->map(
                        fn($product, $key) => array_merge(
                            collect($product)->except(['shop_id'])->toArray(),
                            ['farmer_transaction_shop_id' => $db_shops->firstWhere("shop_id", $product["shop_id"])->id]
                        )
                    )
                    ->all()
            );

            $cart = Cart::query()
                ->where('user_id', auth()->id())
                ->whereIn('product_id', $id_products)
                ->delete();

            $fcmTokens = User::query()
                ->whereIn('id', $user_ids->all())
                ->whereNotNull('firebase_token')
                ->pluck('firebase_token')
                ->all();

            if ($fcmTokens) {
                $result = firebaseNotification($fcmTokens, [
                    "notification" => [
                        "title" => "Pesanan Baru!",
                        "body" => "Seseorang memesan produk anda, harap lihat produk yang dibeli!"
                    ],
                ]);
            }

            DB::commit();

            return response()->json($midtrans);
        } catch (\Exception $ex) {
            DB::rollBack();

            return response()->json([
                'message' => $ex->getMessage(),
                'line' => $ex->getLine()
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
            // 'item_details' => $params['items'],
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

    public function getTransactions(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $status = $request->query('status');

        $transactions = FarmerTransaction::query()
            ->with([
                'farmer_transaction_shops:id,farmer_transaction_id,shop_name,total',
                'farmer_transaction_shops.farmer_transaction_items:id,farmer_transaction_shop_id,product_id,product_name,product_price,quantity,discount,total',
                'farmer_transaction_shops.farmer_transaction_items.product:id,name,picture',
            ])
            ->select(['id', 'code', 'status', 'midtrans_token', 'bank_name', 'bank_code', 'total', 'created_at'])
            ->where('user_id', auth()->id())
            ->when($this->validateTransactionStatus($status), function ($query, $status1) use ($status) {
                return $query->where('status', $status);
            })
            ->when($search, function ($query, $search1) use ($search) {
                return $query->whereHas('farmer_transaction_shops.farmer_transaction_items', function ($query1) use ($search) {
                    $query1->where('product_name', 'LIKE', '%' . trim($search) . '%');
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json([
            'message' => 'All Transactions',
            'transaction' => $transactions
        ]);
    }

    private function validateTransactionStatus(?String $transaction_status): bool
    {
        if (!$transaction_status) {
            return false;
        }

        return in_array($transaction_status, [
            'pending',
            'capture',
            'settlement',
            'deny',
            'cancel',
            'expire',
            'failure',
            // 'refund',
            // 'chargeback',
            // 'partial_refund',
            // 'partial_chargeback',
            'authorize',
        ]);
    }

    public function pendingTransactions(Request $request): JsonResponse
    {
        $search = $request->query('search');

        $transactions = FarmerTransaction::query()
            ->with([
                'farmer_transaction_shops:id,farmer_transaction_id,shop_name,total',
                'farmer_transaction_shops.farmer_transaction_items:id,farmer_transaction_shop_id,product_id,product_name,product_price,quantity,discount,total',
                'farmer_transaction_shops.farmer_transaction_items.product:id,name,picture',
            ])
            ->select(['id', 'code', 'status', 'midtrans_token', 'bank_name', 'bank_code', 'total', 'created_at'])
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->when($search, function ($query, $search1) use ($search) {
                return $query->whereHas('farmer_transaction_shops.farmer_transaction_items', function ($query1) use ($search) {
                    $query1->where('product_name', 'LIKE', '%' . trim($search) . '%');
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json([
            'message' => 'Pending Transactions, waiting for user payment(s)',
            'transaction' => $transactions
        ]);
    }

    public function detailTransaction(FarmerTransaction $farmerTransaction): JsonResponse
    {
        try {
            $this->authorize('transaction-show', $farmerTransaction);
        } catch (AuthorizationException $ae) {
            return response()->json([
                'message' => $ae->getMessage()
            ], 403);
        }

        $farmerTransaction->load([
            'farmer_transaction_shops:id,farmer_transaction_id,shop_id,shop_name,subtotal,discount,total_shipping,total',
            'farmer_transaction_shops.farmer_transaction_items' => function ($qf) {
                $qf->leftJoin(
                    'products',
                    'farmer_transaction_items.product_id',
                    '=',
                    'products.id'
                )
                    ->select([
                        'farmer_transaction_items.id',
                        'farmer_transaction_items.farmer_transaction_shop_id',
                        'farmer_transaction_items.product_id',
                        'products.picture as product_pictures',
                        'farmer_transaction_items.product_name',
                        'farmer_transaction_items.product_price',
                        'farmer_transaction_items.product_weight',
                        'farmer_transaction_items.quantity',
                        'farmer_transaction_items.discount',
                        'farmer_transaction_items.total',
                        'farmer_transaction_items.created_at',
                    ]);
            },
        ]);

        return response()->json([
            'message' => 'Transaction Detail',
            'transaction' => $farmerTransaction
        ], 200);
    }

    public function shippingTransactionShops(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $shipping_status = $request->query('shipping_status');

        $transaction_shops = FarmerTransactionShop::query()
            ->with([
                'farmer_transaction_items' => function ($qf) {
                    $qf->leftJoin(
                        'products',
                        'farmer_transaction_items.product_id',
                        '=',
                        'products.id'
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
            ->select([
                'id',
                'farmer_transaction_id',
                'shop_id',
                'shop_name',
                'total',
                'shipping_status',
            ])
            ->whereHas('farmer_transaction', function ($query) {
                $query->where('status', 'settlement');
            })
            ->when($search, function ($query, $search1) use ($search) {
                return $query->whereHas('farmer_transaction_items', function ($query1) use ($search) {
                    $query1->where('product_name', 'LIKE', '%' . trim($search) . '%');
                });
            })
            ->when(in_array($shipping_status, ['0', '1', '2']), function ($query, $shipping_status1) use ($shipping_status) {
                return $query->where('shipping_status', trim($shipping_status));
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json([
            'message' => 'Shipping transactions',
            'transactions' => $transaction_shops
        ], 200);
    }

    public function detailShippingShop(FarmerTransactionShop $farmerTransactionShop): JsonResponse
    {
        $farmerTransactionShop->load([
            'farmer_transaction:id,code,midtrans_token,bank_name,bank_fees,user_id,user_name,address_id,user_recipient_name,user_recipient_phone_number,user_recipient_province_id,user_recipient_city_id,user_recipient_address',
            'farmer_transaction_items' => function ($qf) {
                $qf->leftJoin(
                    'products',
                    'farmer_transaction_items.product_id',
                    '=',
                    'products.id'
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
        ]);

        try {
            $this->authorize('transaction-show', $farmerTransactionShop->farmer_transaction);
        } catch (AuthorizationException $ae) {
            return response()->json([
                'message' => $ae->getMessage()
            ], 403);
        }

        return response()->json([
            'message' => 'Detail transaction shop',
            'transaction_shop' => $farmerTransactionShop
        ], 200);
    }

    public function cancelTransaction(Request $request, FarmerTransaction $farmerTransaction): JsonResponse
    {
        try {
            $this->authorize('transaction-show', $farmerTransaction);
        } catch (AuthorizationException $ae) {
            return response()->json([
                'message' => $ae->getMessage()
            ], 403);
        }

        if ($farmerTransaction->status == 'cancel') {
            return response()->json([
                'message' => 'Transaksi sudah dibatalkan sebelumnya!'
            ], 400);
        }

        try {
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = (bool) config('midtrans.is_production');
            \Midtrans\Config::$isSanitized = (bool) config('midtrans.is_sanitized');
            \Midtrans\Config::$is3ds = (bool) config('midtrans.is_3ds');

            $status_code = \Midtrans\Transaction::cancel($farmerTransaction->code);

            if ($status_code == "200") {
                $farmerTransaction->status = 'cancel';
                $farmerTransaction->save();

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

    public function acceptedShipping(Request $request, FarmerTransactionShop $farmerTransactionShop): JsonResponse
    {
        $farmerTransactionShop->load([
            'farmer_transaction:id,status,user_id',
            'shop:id,balance'
        ]);

        try {
            $this->authorize('transaction-show', $farmerTransactionShop->farmer_transaction);
        } catch (AuthorizationException $ae) {
            return response()->json([
                'message' => $ae->getMessage()
            ], 403);
        }

        if ($farmerTransactionShop->farmer_transaction->status != "settlement") {
            return response()->json([
                'message' => 'Anda belum selesai melakukan pembayaran produk'
            ], 400);
        }

        if ($farmerTransactionShop->shipping_status == 2) {
            return response()->json([
                'message' => 'Pesanan sudah diterima'
            ], 400);
        }

        $farmerTransactionShop->shipping_status = 2;

        if ($farmerTransactionShop->shop_id) {
            $farmerTransactionShop->shop->balance = $farmerTransactionShop->shop->balance + $farmerTransactionShop->total;
        }

        $farmerTransactionShop->push();

        FCMFacade::send([
            "message" => [
                "token" => "ctZP-eyHStCfH2Arm4KCo4:APA91bF_NCP2MNkQnpH38WS3QVZa05y2JM8iqhrJ2n_b7UbE4SC83EejpA3h9ry0NHGT27Ymc57XbwXHAr9ns2FWA2eZGpECMLgvg7VmdaRjR-YI5JQ5An4",
                "notification" => [
                    "body" => "Produk yang dikirim telah diterima oleh pelanggan.",
                    "title" => "Produk diterima"
                ]
            ]
        ]);

        return response()->json([
            'message' => 'Berhasil diupdate'
        ], 200);
    }
}
