<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Product;
use App\Models\Shop;
use App\Models\WithdrawalBank;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shop = auth()->user()->farmer->shop;
        if (!$shop) {
            return redirect()->route('bitanic.shop.create');
        }

        if (!$shop->is_ktp_uploaded) {
            return view('bitanic.shop.ktp.update', compact('shop'));
        }
        if ($shop->is_ktp_validated !== 1) {
            return view('bitanic.shop.ktp.pending', compact('shop'));
        }

        $products = Product::query()
            ->with(['crop_for_sale:id,picture,name'])
            ->select(['id', 'name', 'crop_for_sale_id', 'stock', 'picture'])
            ->where('shop_id', $shop->id)
            ->when(request()->query('search'), function($query, $status){
                $search = request()->query('search');

                return $query->where(function($query)use($search){
                    $query->where('name', 'LIKE', '%'.$search.'%');
                });
            })
            ->paginate(6);

        return view('bitanic.shop.index', [
            'shop' => $shop,
            'products' => $products,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $shop = auth()->user()->farmer->shop;
        if ($shop) {
            return redirect()->route('bitanic.shop.index');
        }
        $withdrawalBanks = WithdrawalBank::query()
            ->pluck('name', 'id');

        return view('bitanic.shop.create', compact('withdrawalBanks'));
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
            'name'          => 'required|string|max:255',
            'latitude'      => 'required|regex:/^(-?\d+(\.\d+)?)$/',
            'longitude'     => 'required|regex:/^(-?\d+(\.\d+)?)$/',
            'address'       => 'required|string|max:1000',
            'picture'       => 'required|image|mimes:jpg,png|max:10240',
            'ktp'           => 'required|image|mimes:jpg,png|max:10240',
            'bank_account'  => 'required|numeric|digits_between:10,20',
            'bank_id'       => 'required|integer|min:0',
        ]);

        $withdrawalBank = WithdrawalBank::findOrFail($request->bank_id);

        $image = $request->file('ktp');
        $name = strtoupper(Str::random(10)) . '-' . time() . '.' . $image->extension();
        $path = Storage::putFileAs('ktp/shop', $request->file('ktp'), $name);

        Shop::create($request->only(
            'name',
            'latitude',
            'longitude',
            'address',
            'bank_account',
        ) + [
            'picture' => image_intervention($request->file('picture'), 'bitanic-photo/shop/', (16 / 9)),
            'ktp'   => $path,
            'farmer_id' => auth()->user()->farmer->id,
            'bank_type' => $withdrawalBank->name,
        ]);

        return redirect()->route('bitanic.shop.index')->with('success', "Berhasil membuat toko");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $shop = auth()->user()->farmer->shop;
        if ($shop->is_ktp_validated !== 1) {
            return redirect()
                ->back()
                ->with('failed', 'Toko belum diverifikasi');
        }

        $withdrawalBanks = WithdrawalBank::query()
            ->pluck('name', 'id');

        return view('bitanic.shop.edit', [
            'withdrawalBanks' => $withdrawalBanks,
            'shop' => $shop
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'latitude'               => 'required|regex:/^(-?\d+(\.\d+)?)$/',
            'longitude'               => 'required|regex:/^(-?\d+(\.\d+)?)$/',
            'address'           => 'required|string|max:1000',
            'picture'           => 'nullable|image|mimes:jpg,png|max:10240',
            'bank_account' => 'required|numeric|digits_between:10,20',
            'bank_id' => 'required|integer|min:0',
        ]);

        $shop = auth()->user()->farmer->shop;

        $foto = $shop->picture;

        if ($request->file('picture')) {
            $foto = image_intervention($request->file('picture'), 'bitanic-photo/shop/', 16 / 9);

            if (File::exists(public_path($shop->picture))) {
                File::delete(public_path($shop->picture));
            }
        }

        $withdrawalBank = WithdrawalBank::findOrFail($request->bank_id);

        $shop->update($request->only(
            'name',
            'latitude',
            'longitude',
            'address',
            'bank_account',
        ) + [
            'picture' => $foto,
            'bank_type' => $withdrawalBank->name,
        ]);

        return back()->with('success', "Berhasil disimpan");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        $shop = auth()->user()->farmer->shop;

        $shop->delete();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Toko berhasil dihapus');
    }

    public function showKtp() {
        $shop = auth()->user()->farmer->shop;

        abort_if(!$shop->is_ktp_uploaded, 404, 'KTP belum diupload');

        return response()
            ->file(storage_path('app/' . $shop->ktp));
    }

    public function editKtp() {
        $shop = auth()->user()->farmer->shop;

        return view('bitanic.shop.ktp.update', compact('shop'));
    }

    public function updateKtp(Request $request) {
        $shop = auth()->user()->farmer->shop;

        $request->validate([
            'ktp'   => 'required|image|mimes:jpg,jpeg,png|max:10240'
        ]);

        if (Storage::exists($shop->ktp)) {
            Storage::delete($shop->ktp);
        }

        $image = $request->file('ktp');
        $name = strtoupper(Str::random(5)) . '-' . time() . '.' . $image->extension();
        $path = Storage::putFileAs('ktp/shop', $request->file('ktp'), $name);
        $shop->ktp = $path;
        $shop->is_ktp_validated = null;
        $shop->push();

        return redirect()
            ->route('bitanic.shop.index')
            ->with('success', 'Berhasil disimpan');
    }
}
