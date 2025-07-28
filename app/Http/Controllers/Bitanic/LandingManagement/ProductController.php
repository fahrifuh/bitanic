<?php

namespace App\Http\Controllers\Bitanic\LandingManagement;

use App\Http\Controllers\Controller;
use App\Models\LandingProduct;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index(): View
    {
        $landingProducts = LandingProduct::query()
            ->latest()
            ->paginate(10);

        return view('bitanic.landing-setting.product.index', compact('landingProducts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create(): View
    {
        return view('bitanic.landing-setting.product.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'picture'       => 'required|image|mimes:jpg,png,svg|max:2048',
            'title'         => 'required|string|max:255',
            'price'         => 'required|integer|min:0',
            'description'   => 'required|string|max:2000',
            'tags'          => 'required|string|max:255',
        ]);
        
        $picture = image_intervention($request->file('picture'), 'bitanic-photo/landing/product/', 16/9);

        $tags = [];

        foreach (explode(',', $request->tags) as $tag) {
            $tags[] = trim($tag);
        }

        LandingProduct::create(
            $request->only(['title', 'price', 'description']) + 
            [
                'image' => $picture,
                'tags'  => $tags,
            ]
        );

        return redirect()
            ->route('bitanic.product.index')
            ->with('success', 'Berhasil disimpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\LandingProduct  $landingProduct
     */
    public function show(LandingProduct $product): View
    {
        return view('bitanic.landing-setting.product.show', [
            'landingProduct' => $product,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LandingProduct  $landingProduct
     */
    public function edit(LandingProduct $product): View
    {
        return view('bitanic.landing-setting.product.edit', [
            'landingProduct' => $product,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LandingProduct  $landingProduct
     */
    public function update(Request $request, LandingProduct $product): RedirectResponse
    {
        $request->validate([
            'picture'       => 'nullable|image|mimes:jpg,png,svg|max:2048',
            'title'         => 'required|string|max:255',
            'price'         => 'required|integer|min:0',
            'description'   => 'required|string|max:2000',
            'tags'          => 'required|string|max:255',
        ]);
        
        $picture = $product->image;

        if ($request->file('picture')) {
            $picture = image_intervention($request->file('picture'), 'bitanic-photo/landing/product/', 16/9);

            if (File::exists(public_path($product->image))) {
                File::delete(public_path($product->image));
            }
        }

        $tags = [];

        foreach (explode(',', $request->tags) as $tag) {
            $tags[] = trim($tag);
        }

        $product->update(
            $request->only(['title', 'price', 'description']) + 
            [
                'image' => $picture,
                'tags'  => $tags,
            ]
        );

        return redirect()
            ->route('bitanic.product.show', $product->id)
            ->with('success', 'Berhasil disimpan');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LandingProduct  $landingProduct
     */
    public function destroy(LandingProduct $product): JsonResponse
    {
        if (File::exists(public_path($product->image))) {
            File::delete(public_path($product->image));
        }

        $product->delete();

        $message = 'Berhasil disimpan';

        session()->flash('success', $message);

        return response()
            ->json([
                'message' => $message
            ]);
    }
}
