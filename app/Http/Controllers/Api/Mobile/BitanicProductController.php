<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\BitanicProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BitanicProductController extends Controller
{
    public function index(Request $request) : JsonResponse {
        $name = $request->query('name');
        $bitanicProducts = BitanicProduct::query()
            ->when($name, function ($query, $name) {
                $name = '%' . trim($name) . '%';
                return $query->where('name', 'LIKE', $name);
            })
            ->orderByDesc('created_at')
            ->get(['id', 'name', 'picture', 'price', 'discount', 'type', 'category', 'version']);

        return response()->json([
            'message' => 'Data produk bitanic',
            'bitanic_products' => $bitanicProducts,
        ], 200);
    }

    public function show(BitanicProduct $bitanicProduct) : JsonResponse {
        return response()->json([
            'message' => 'Data detail produk',
            'bitanic_product' => $bitanicProduct
        ], 200);
    }
}
