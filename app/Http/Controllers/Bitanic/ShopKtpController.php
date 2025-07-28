<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ShopKtpController extends Controller
{
    public function index() {
        $shops = Shop::query()
            ->with('farmer:id,full_name')
            ->where('is_ktp_validated', NULL)
            ->where('ktp', '<>', NULL)
            ->latest('created_at')
            ->paginate(10);

        return view('bitanic.ktp.shop.index', compact('shops'));
    }

    public function showKtp(Shop $shop) {
        if (!$shop->ktp) {
            return redirect()
                ->back()
                ->with('failed', 'KTP belum ada');
        }

        return response()
            ->file(storage_path('app/' . $shop->ktp));
    }

    public function update(Request $request, Shop $shop) {
        $request->validate([
            'status' => 'required|integer|in:0,1'
        ]);

        $shop->is_ktp_validated = $request->status;
        $shop->save();

        return response()
            ->json([
                'message' => 'Berhasil disimpan'
            ]);
    }
}
