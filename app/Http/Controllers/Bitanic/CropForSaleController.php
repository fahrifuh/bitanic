<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\CropForSale;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CropForSaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index(): View
    {
        $cropForSales = CropForSale::query()
            ->when(request()->query('search'), function($query){
                $search = request()->query('search');
                return $query->where('name', 'LIKE', '%'.$search.'%');
            })
            ->orderBy('name')
            ->paginate(10);

        return view('bitanic.crop-for-sale.index', compact('cropForSales'));
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create(): View
    {
        return view('bitanic.crop-for-sale.create');
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
            'name' => 'required|string|max:255|unique:crop_for_sales,name',
            'days' => 'required|integer|min:1|max:360',
            'picture' => 'required|image|mimes:jpg,png|max:10240',
        ]);

        $picture = image_intervention($request->file('picture'), 'bitanic-photo/crop-for-sales/', 1/1);

        CropForSale::create(
            $request->only(['name', 'days']) +
            [
                'picture' => $picture
            ]
        );

        return redirect()->route('bitanic.crop-for-sale.index')->with('success', 'berhasil disimpan');
    }

    /**
     * Display the specified resource.
     *
     */
    public function show(CropForSale $cropForSale): View
    {
        return view('bitanic.crop-for-sale.show', compact('cropForSale'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     */
    public function edit(CropForSale $cropForSale): View
    {
        return view('bitanic.crop-for-sale.edit', compact('cropForSale'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CropForSale $cropForSale)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:crop_for_sales,name,' . $cropForSale->id,
            'days' => 'required|integer|min:1|max:360',
            'picture' => 'nullable|image|mimes:jpg,png|max:10240',
        ]);

        $picture = $cropForSale->picture;

        if ($request->file('picture')) {
            $picture = image_intervention($request->file('picture'), 'bitanic-photo/crop-for-sales/', 1/1);

            if(File::exists(public_path($cropForSale->picture))){
                File::delete(public_path($cropForSale->picture));
            }
        }

        $cropForSale->update(
            $request->only(['name', 'days']) +
            [
                'picture' => $picture
            ]
        );

        return back()->with('success', 'Berhasil disimpan');
    }

    /**
     * Remove the specified resource from storage.
     *
     */
    public function destroy(CropForSale $cropForSale): JsonResponse
    {
        if(File::exists(public_path($cropForSale->picture))){
            File::delete(public_path($cropForSale->picture));
        }

        $cropForSale->delete();

        session()->flash('success', 'Berhasil dihapus');

        return response()->json([
          'message' => "Berhasil"
        ], 200);
    }
}
