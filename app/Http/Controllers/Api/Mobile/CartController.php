<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function index(): JsonResponse
    {
        $user_id = auth()->user()->id;
        $carts = Shop::query()
            ->select(['id', 'name'])
            ->with([
                'products' => function($products)use($user_id){
                    $products->whereHas('carts', function($c)use($user_id){
                        $c->where('user_id', $user_id);
                    })
                    ->join('carts', function ($join)use($user_id) {
                        $join->on('products.id', '=', 'carts.product_id')
                             ->where('user_id', $user_id);
                    })
                    ->select([
                        'products.id',
                        'carts.id as cart_id',
                        'products.shop_id',
                        'products.name',
                        'products.picture',
                        'products.price',
                        'products.discount',
                        'carts.total as cart_total',
                    ]);
                }
            ])
            ->whereHas('products.carts', function($q)use($user_id) {
                $q->where('user_id', $user_id);
            })
            ->get();

        return response()->json([
            'message' => 'List keranjang pengguna',
            'status' => 200,
            'carts' => $carts
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $v = Validator::make($request->all(), [
            'product_id' => 'required',
            'total' => 'required|integer|min:1'
        ]);

        if ($v->fails()) {
            return response()->json([
                'messages' => $v->errors(),
                'status' => 400
            ], 400);
        }

        if (!$product = Product::find($request->product_id)) {
            return response()->json([
                'messages' => [
                    'product_id' => ["The selected product id is invalid."]
                ],
                'status' => 400
            ], 400);
        }

        if ($request->total > $product->stock) {
            return response()->json([
                'messages' => [
                    'errors' => ["Jumlah yang dipilih melebihi stok produk."]
                ],
                'status' => 400
            ], 400);
        }

        $user_id = auth()->user()->id;

        $cart = Cart::query()
            ->firstWhere([
                'product_id' => $request->product_id,
                'user_id' => $user_id
            ]);

        $product->stock = $product->stock - $request->total;
        $product->save();

        if ($cart) {
            $newTotal = $cart->total + $request->total;

            if ($newTotal > $product->stock) {
                return response()->json([
                    'messages' => [
                        'total' => ["Total yang dipilih melebihi stok produk."]
                    ],
                    'status' => 400
                ], 400);
            }

            $cart->total = $newTotal;

            $cart->save();

            return response()->json([
                'message' => 'Produk sudah ditambah ke keranjang',
                'status' => 200
            ], 200);
        }

        Cart::insert(
            $request->only('product_id', 'total') +
            [
                'user_id' => auth()->user()->id,
                'created_at' => now()
            ]
        );

        return response()->json([
            'message' => 'Data berhasil disimpan!',
            'status' => 200,
        ], 200);
    }

    public function show(Cart $cart): JsonResponse
    {
        $this->authorize('user-cart', $cart);

        $cart->load([
            'product:id,name,picture,shop_id,discount,weight,price',
            'product.shop:id,name,picture,address',
        ]);

        return response()->json([
            'message' => 'Detail keranjang',
            'status' => 200,
            'cart' => $cart
        ]);
    }

    public function update(Request $request, Cart $cart): JsonResponse
    {
        $this->authorize('user-cart', $cart);

        $cart->load('product:id,stock');

        $product = Product::findOrFail($cart->product_id);
        $stock = $product->stock;

        $v = Validator::make($request->all(), [
            'total' => 'required|integer|min:1|max:'.$stock
        ]);

        if ($v->fails()) {
            return response()->json([
                'messages' => $v->errors(),
                'status' => 400
            ], 400);
        }

        $differences = $cart->total - $request->total;

        $cart->update($request->only('total'));

        $product->stock = $product->stock + $differences;
        $product->save();

        return response()->json([
            'message' => 'Data berhasil disimpan!',
            'status' => 200,
        ], 200);
    }

    public function destroy(Cart $cart): JsonResponse
    {
        $this->authorize('user-cart', $cart);

        $product = Product::findOrFail($cart->product_id);

        $product->stock = $product->stock + $cart->total;
        $product->save();

        $cart->delete();

        return response()->json([
            'message' => 'Data berhasil dihapus!',
            'status' => 200,
        ], 200);
    }

    public function getProduct(Product $product): Product
    {
        return $product;
    }
}
