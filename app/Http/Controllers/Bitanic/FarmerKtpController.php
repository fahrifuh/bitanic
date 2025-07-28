<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\Farmer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FarmerKtpController extends Controller
{
    public function index() {
        $farmers = Farmer::query()
            ->where('is_ktp_validated', NULL)
            ->where('ktp', '<>', NULL)
            ->latest('created_at')
            ->paginate(10);

        return view('bitanic.ktp.user.index', compact('farmers'));
    }

    public function showKtp(Farmer $farmer) {
        if (!$farmer->ktp) {
            return redirect()
                ->back()
                ->with('failed', 'KTP belum ada');
        }

        return response()
            ->file(storage_path('app/' . $farmer->ktp));
    }

    public function update(Request $request, Farmer $farmer) {
        $request->validate([
            'status' => 'required|integer|in:0,1'
        ]);

        $farmer->is_ktp_validated = $request->status;
        $farmer->save();

        return response()
            ->json([
                'message' => 'Berhasil disimpan'
            ]);
    }
}
