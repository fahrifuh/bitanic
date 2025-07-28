<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\BitanicProduct;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class BitanicProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): View
    {
        $bitanicProducts = BitanicProduct::query()
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('bitanic.bitanic-product.index', compact('bitanicProducts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {
        return view('bitanic.bitanic-product.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'price' => 'required|integer|min:0',
            'discount' => 'nullable|integer|min:0|max:100',
            'weight' => 'required|integer|min:0',
            'description' => 'required|string|max:1000',
            'type' => 'required|integer|in:1,2,3',
            'category' => 'required|string|in:controller,tongkat',
            'name' => 'required|string|max:255',
            'version' => 'required|regex:/^[0-9.]+$/',
            'picture' => 'required|image|mimes:jpg,png|max:20480'
        ]);

        $picture = image_intervention($request->file('picture'), 'bitanic-photo/bitanic-product/', 4/3);

        $allowedTags = '<h1><h2><h3><h4><h5><h6><p><i><strong><ul><ol><li><a><blockquote>';

        // Remove disallowed tags and attributes
        $description = strip_tags($request->description, $allowedTags);
        $description = preg_replace('/<(.*?)>/i', '<$1>', $description);

        BitanicProduct::create(
            $request->only(['name', 'type', 'category', 'version', 'price', 'weight', 'discount']) +
            [
                'picture' => $picture,
                'description' => $description
            ]
        );

        return redirect()->route('bitanic.bitanic-product.index')->with('success', 'Berhasil disimpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BitanicProduct  $bitanicProduct
     * @return \Illuminate\Http\Response
     */
    public function show(BitanicProduct $bitanicProduct): View
    {
        return view('bitanic.bitanic-product.show', compact('bitanicProduct'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BitanicProduct  $bitanicProduct
     * @return \Illuminate\Http\Response
     */
    public function edit(BitanicProduct $bitanicProduct): View
    {
        return view('bitanic.bitanic-product.edit', compact('bitanicProduct'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BitanicProduct  $bitanicProduct
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BitanicProduct $bitanicProduct)
    {
        $request->validate([
            'price' => 'required|integer|min:0',
            'discount' => 'nullable|integer|min:0|max:100',
            'weight' => 'required|integer|min:0',
            'description' => 'required|string|max:1000',
            'type' => 'required|integer|in:1,2,3',
            'category' => 'required|string|in:controller,tongkat',
            'name' => 'required|string|max:255',
            'version' => 'required|regex:/^[0-9.]+$/',
            'picture' => 'nullable|image|mimes:jpg,png|max:20480'
        ]);

        $picture = $bitanicProduct->picture;

        if ($request->file('picture')) {
            $picture = image_intervention($request->file('picture'), 'bitanic-photo/bitanic-product/', 4/3);

            if (File::exists(public_path($bitanicProduct->picture))) {
                File::delete(public_path($bitanicProduct->picture));
            }
        }

        $allowedTags = '<h1><h2><h3><h4><h5><h6><p><i><strong><ul><ol><li><a><blockquote>';

        // Remove disallowed tags and attributes
        $description = strip_tags($request->description, $allowedTags);
        $description = preg_replace('/<(.*?)>/i', '<$1>', $description);

        $bitanicProduct->update(
            $request->only(['name', 'type', 'category', 'version', 'price', 'weight', 'discount']) +
            [
                'picture' => $picture,
                'description' => $description
            ]
        );

        return back()->with('success', 'Berhasil disimpan');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BitanicProduct  $bitanicProduct
     * @return \Illuminate\Http\Response
     */
    public function destroy(BitanicProduct $bitanicProduct)
    {
        if(File::exists(public_path($bitanicProduct->picture))){
          File::delete(public_path($bitanicProduct->picture));
        }

        $bitanicProduct->delete();

        session()->flash('success', 'Berhasil dihapus');

        return response()->json([
          'message' => "Berhasil"
        ], 200);
    }
}
