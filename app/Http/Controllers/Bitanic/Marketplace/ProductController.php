<?php

namespace App\Http\Controllers\Bitanic\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\CropForSale;
use App\Models\Product;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $crop_for_sales = CropForSale::query()
            ->orderBy('name')
            ->get(['id', 'name', 'days']);

        return view('bitanic.shop.product.create', compact('crop_for_sales'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user_id = auth()->id();
        $max_commodities = 20;

        $products_count = Product::query()
            ->whereHas('shop.farmer', function($query)use($user_id){
                $query->where('user_id', $user_id);
            })
            ->count();

        $subscription = Subscription::query()
            ->with('member:id,max_commodities')
            ->whereDate('expired', '>', now()->format('Y-m-d'))
            ->where('user_id', $user_id)
            ->where('is_canceled', '0')
            ->where('status', 'settlement')
            ->first();

        if ($subscription) {
            $max_commodities = $subscription->member->max_commodities;
        }

        if ($products_count == $max_commodities) {
            return redirect()->back()->withErrors([
                'max_commodities' => ['Komodity sudah mencapai batas!']
            ]);
        }

        $request->validate([
            'crop_for_sale_id' => 'required|integer|exists:crop_for_sales,id',
            'name'              => 'required|string|max:200',
            'category'       => 'required|string|in:buah,sayur',
            'weight'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            // 'stock_metric'       => 'required|string|in:kg,gr,mg,ons,pcs',
            // 'size'       => 'required|integer|min:0',
            // 'size_metric'       => 'required|string|in:m,cm,inch,mm',
            'price'       => 'required|integer|min:0',
            'discount'       => 'nullable|numeric|min:0|max:100',
            'description'           => 'required|string|max:1000',
            'picture'           => 'required|image|mimes:jpg,png,jpeg|max:10240',
            // 'picture'           => 'required|array|min:1|max:5',
            // 'picture.*'           => 'required|image|mimes:jpg,png|max:10240',
        ]);

        $images = [];

        $picture = image_intervention($request->file('picture'), 'bitanic-photo/product/', 1/1);

        // foreach ($request->picture as $picture) {
        //     array_push($images, image_intervention($picture, 'bitanic-photo/product/', (1 / 1)));
        // }

        Product::create($request->only(
            'crop_for_sale_id',
            'name',
            'price',
            'discount',
            'category',
            'weight',
            'stock',
            'description',
        ) + [
            'stock_metric' => '',
            'size' => 0,
            'size_metric' => '',
            'picture' => [$picture],
            'shop_id' => auth()->user()->farmer->shop->id
        ]);

        return redirect()->route('bitanic.shop.index')->with('success', "Berhasil menambah produk");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        $this->authorize('user-products', $product);

        $product->load('shop');

        return view('bitanic.shop.product.show', [
            'product' => $product,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $this->authorize('user-products', $product);

        $crop_for_sales = CropForSale::query()
            ->orderBy('name')
            ->get(['id', 'name', 'days']);

        return view('bitanic.shop.product.edit', [
            'product' => $product,
            'crop_for_sales' => $crop_for_sales,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $this->authorize('user-products', $product);

        $request->validate([
            'crop_for_sale_id' => 'required|integer|exists:crop_for_sales,id',
            'name'              => 'required|string|max:200',
            'category'       => 'required|string|in:buah,sayur',
            'weight'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            // 'stock_metric'       => 'required|string|in:kg,gr,mg,ons,pcs',
            // 'size'       => 'required|integer|min:0',
            // 'size_metric'       => 'required|string|in:m,cm,inch,mm',
            'price'       => 'required|integer|min:0',
            'discount'       => 'nullable|numeric|min:0|max:100',
            'description'           => 'required|string|max:1000',
            'picture'           => 'nullable|image|mimes:jpg,png,jpeg|max:10240',
            // 'picture'           => 'nullable|array|max:5',
            // 'picture.*'           => 'nullable|image|mimes:jpg,png|max:10240',
        ]);

        // $images = [];
        // $delete_images = [];
        // $old_images = $product->picture;

        // if ($request->picture) {
        //     foreach ($request->picture as $picture) {
        //         array_push($images, image_intervention($picture, 'bitanic-photo/product/', (1 / 1)));
        //     }
        // }

        $picture = $product->picture;

        if ($request->file('picture')) {
            $picture = image_intervention($request->file('picture'), 'bitanic-photo/product/', 1/1);

            if(isset($product->picture[0]) && File::exists(public_path($product->picture[0]))){
                File::delete(public_path($product->picture[0]));
            }
        }

        $update = $request->only(
            'crop_for_sale_id',
            'name',
            'price',
            'discount',
            'category',
            'weight',
            'stock',
            // 'stock_metric',
            // 'size',
            // 'size_metric',
            'description',
        );

        // if (count($images) > 0) {
        //     if (count($images) > (5 - count($old_images))) {
        //         for ($i=0; $i < count($images); $i++) {
        //             if (File::exists(public_path($product->picture[$i]))) {
        //                 array_push($delete_images, public_path($product->picture[$i]));
        //             }
        //             array_shift($old_images);
        //         }
        //         File::delete($delete_images);
        //     }

        //     $update = array_merge(
        //         $update,
        //         [
        //             'picture' => array_merge($old_images, $images)
        //         ]
        //     );
        // }

        $product->update($update + [
            'picture' => [$picture]
        ]);

        return back()->with('success', "Berhasil update produk");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $this->authorize('user-products', $product);

        foreach ($product->picture as $picture) {
            if (File::exists(public_path($picture))) {
                File::delete(public_path($picture));
            }
        }

        $product->delete();

        return redirect()->route('bitanic.shop.index');
    }
}
