<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class UserProductController extends Controller
{
    public function index() {
        $products = Product::query()
            ->with([
                'shop:id,name,farmer_id',
                'shop.farmer:id,full_name',
            ])
            ->latest()
            ->paginate(20);

        return view('bitanic.marketplace.user-product.index', compact('products'));
    }

    public function show(Product $product) {
        $product->load([
            'shop:id,name,farmer_id',
            'shop.farmer:id,full_name',
        ]);

        return view('bitanic.marketplace.user-product.show', compact('product'));
    }

    public function updateDisable(Product $product) {
        $product->is_disabled = $product->is_disabled ? 0 : 1;
        $product->save();

        return redirect()
            ->back()
            ->with('success', 'Status berhasil diubah');
    }
}
