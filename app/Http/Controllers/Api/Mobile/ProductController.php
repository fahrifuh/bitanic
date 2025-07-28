<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): JsonResponse
    {
        $products = Product::query()
        ->with(['crop_for_sale:id,picture,name'])
        ->where('shop_id', auth()->user()->farmer->shop->id)
        ->get([
            'id',
            'shop_id',
            'name',
            'picture',
            'stock',
            'stock_metric',
            'price',
            'crop_for_sale_id',
            'discount',
        ]);

        return response()->json([
            'products' => $products,
            'message' => "Data produk petani",
            'status' => 200
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
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
            return response()->json([
                'errors' => [
                    'max_commodities' => ['Komodity sudah mencapai batas!']
                ]
            ], 422);
        }

        $v = Validator::make($request->all(), [
            'crop_for_sale_id' => 'required|integer|exists:crop_for_sales,id',
            'name'              => 'required|string|max:200',
            'category'       => 'required|string|in:buah,sayur',
            'stock'       => 'required|integer|min:0',
            'weight'       => 'required|numeric|min:0',
            // 'stock_metric'       => 'required|string|in:kg,gr,mg,ons,pcs',
            // 'size'       => 'required|integer|min:0',
            // 'size_metric'       => 'required|string|in:m,cm,inch,mm',
            'price'       => 'required|integer|min:0',
            'description'           => 'required|string|max:1000',
            'picture'           => 'nullable|image|mimes:jpg,png,jpeg|max:10240',
            // 'picture'           => 'nullable|array|max:5',
            // 'picture.*'           => 'nullable|image|mimes:jpg,png|max:10240',
        ]);

        if ($v->fails()) {
            return response()->json([
                'messages' => $v->errors(),
                'status' => 400
            ], 400);
        }

        $images = [];

        if ($request->hasFile('picture')) {
            $images = [image_intervention($request->file('picture'), 'bitanic-photo/product/', 1/1)];
        }

        // foreach ($request->picture as $picture) {
        //     array_push($images, image_intervention($picture, 'bitanic-photo/product/', (1 / 1)));
        // }

        Product::create($request->only(
            'crop_for_sale_id',
            'name',
            'price',
            'category',
            'stock',
            'weight',
            'description',
        ) + [
            'picture' => $images,
            'stock_metric' => '',
            'size_metric' => '',
            'shop_id' => auth()->user()->farmer->shop->id
        ]);

        return response()->json([
            'message' => "Data berhasil disimpan!",
            'status' => 200
        ], 200);
    }

    public function show(Product $product): JsonResponse
    {
        $this->authorize('user-products', $product);

        $product->load(['crop_for_sale']);

        return response()->json([
            'product' => $product,
            'message' => "Data produk ".$product->name,
            'status' => 200
        ], 200);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $this->authorize('user-products', $product);

        $v = Validator::make($request->all(), [
            'crop_for_sale_id' => 'required|integer|exists:crop_for_sales,id',
            'name'              => 'required|string|max:200',
            'category'       => 'required|string|in:buah,sayur',
            'stock'       => 'required|integer|min:0',
            'weight'       => 'required|numeric|min:0',
            // 'stock_metric'       => 'required|string|in:kg,gr,mg,ons,pcs',
            // 'size'       => 'required|integer|min:0',
            // 'size_metric'       => 'required|string|in:m,cm,inch,mm',
            'price'       => 'required|integer|min:0',
            'description'           => 'required|string|max:1000',
            'picture'           => 'nullable|image|mimes:jpg,png,jpeg|max:10240',
            // 'picture'           => 'nullable|array|max:5',
            // 'picture.*'           => 'nullable|image|mimes:jpg,png|max:10240',
        ]);

        if ($v->fails()) {
            return response()->json([
                'messages' => $v->errors(),
                'status' => 400
            ], 400);
        }

        $images = $product->picture;

        if ($request->hasFile('picture')) {
            $images = [image_intervention($request->file('picture'), 'bitanic-photo/product/', 1/1)];
            if (isset($product->picture[0]) && File::exists(public_path($product->picture[0]))) {
                File::delete(public_path($product->picture[0]));
            }
        }

        // if ($request->picture) {
        //     foreach ($request->picture as $picture) {
        //         array_push($images, image_intervention($picture, 'bitanic-photo/product/', (1 / 1)));
        //     }

        //     foreach ($product->picture as $picture) {
        //         if (File::exists(public_path($picture))) {
        //             File::delete(public_path($picture));
        //         }
        //     }
        // }


        $product->update($request->only(
            'crop_for_sale_id',
            'name',
            'price',
            'category',
            'weight',
            'stock',
            // 'stock_metric',
            // 'size',
            // 'size_metric',
            'description',
        ) + [
            'picture' => $images,
        ]);

        return response()->json([
            'message' => "Data berhasil disimpan",
            'status' => 200,
        ], 200);
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->authorize('user-products', $product);

        foreach ($product->picture as $picture) {
            if (File::exists(public_path($picture))) {
                File::delete(public_path($picture));
            }
        }

        $product->delete();

        return response()->json([
            'message' => "Data berhasil disimpan",
            'status' => 200
        ], 200);
    }
}
