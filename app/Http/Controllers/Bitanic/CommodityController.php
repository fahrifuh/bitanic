<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\Commodity;
use App\Models\Crop;
use App\Models\Farmer;
use App\Models\Garden;
use App\Models\Land;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommodityController extends Controller
{
    public function create(Farmer $farmer, Land $land, Garden $garden) : View {
        if (
            auth()->user()->role != 'admin' &&
            $farmer->user_id != auth()->user()->id
        ) {
            return abort(403);
        }
        if ($farmer->id != $land->farmer_id) {
            return back()->with('failed', 'Lahan tidak sama dengan yang ada di petani');
        }
        if (
            $farmer->id != $garden->land->farmer_id
        ) {
            return abort(403);
        }

        $crops = Crop::query()
            ->get(['id', 'crop_name', 'frekuensi_siram']);

        return view('bitanic.garden.commodity.create', compact(
            'farmer',
            'garden',
            'crops',
            'land',
        ));
    }

    public function store(Request $request, Farmer $farmer, Land $land, Garden $garden) : RedirectResponse {
        if (
            auth()->user()->role != 'admin' &&
            $farmer->user_id != auth()->user()->id
        ) {
            return abort(403);
        }
        if ($farmer->id != $land->farmer_id) {
            return back()->with('failed', 'Lahan tidak sama dengan yang ada di petani');
        }
        if (
            $farmer->id != $garden->land->farmer_id
        ) {
            return abort(403);
        }

        $validated = $request->validate([
            'crop_id' => 'required|integer|min:0',
            'total' => 'required|integer|min:0',
            'planting_dates' => 'required|date|date_format:Y-m-d',
        ]);

        $crop = Crop::query()
            ->find($validated['crop_id']);

        if (!$crop) {
            return redirect()->back()->withErrors([
                'crop_id' => ['Tanaman tidak ditemukan']
            ])
            ->withInput();
        }

        Commodity::create($validated + [
            'garden_id' => $garden->id,
            'estimated_harvest' => now()->parse($validated['planting_dates'])->addWeeks($crop->frekuensi_siram)
        ]);

        return redirect()->route('bitanic.garden.show', [
            'farmer' => $farmer->id,
            'land' => $land->id,
            'garden' => $garden->id
        ])->with('success', 'Berhasil disimpan!');
    }

    public function editYield(
        Farmer $farmer,
        Land $land,
        Garden $garden,
        Commodity $commodity
    ) : View {
        if (
            auth()->user()->role != 'admin' &&
            $farmer->user_id != auth()->user()->id
        ) {
            return abort(403);
        }
        if ($farmer->id != $land->farmer_id) {
            return back()
                ->with('failed', 'Lahan tidak sama dengan yang ada di petani');
        }
        if ($farmer->id != $garden->land->farmer_id) {
            return abort(403);
        }
        if ($garden->id != $commodity->garden_id) {
            return back()
                ->with('failed', 'Komoditi tidak sama dengan yang ada di kebun');
        }
        if ($commodity->is_finished == 1) {
            return redirect()
                ->back()
                ->with('failed', 'Komoditi sudah selesai, tidak bisa diubah!');
        }

        return view('bitanic.garden.commodity.yield', compact(
            'farmer',
            'land',
            'garden',
            'commodity',
        ));
    }

    public function updateYield(
        Request $request,
        Farmer $farmer,
        Land $land,
        Garden $garden,
        Commodity $commodity
    ) {
        if (
            auth()->user()->role != 'admin' &&
            $farmer->user_id != auth()->user()->id
        ) {
            return abort(403);
        }
        if ($farmer->id != $land->farmer_id) {
            return back()
                ->with('failed', 'Lahan tidak sama dengan yang ada di petani');
        }
        if ($farmer->id != $garden->land->farmer_id) {
            return abort(403);
        }
        if ($garden->id != $commodity->garden_id) {
            return back()
                ->with('failed', 'Komoditi tidak sama dengan yang ada di kebun');
        }

        if ($commodity->is_finished == 1) {
            return redirect()
                ->route('bitanic.commodity.history')
                ->with('failed', 'Komoditi sudah selesai, tidak bisa diubah!');
        }

        $validated = $request->validate([
            'hasil_panen' => 'required|numeric|min:0',
            'satuan' => 'required|in:kuintal,kg,ton',
            'catatan' => 'nullable|string|max:255',
        ]);

        $commodity->update([
           'value' => $validated['hasil_panen'],
           'unit' => $validated['satuan'],
           'note' => $validated['catatan'],
           'harvested' => now('Asia/Jakarta'),
           'is_finished' => 1
        ]);

        return redirect()
            ->route('bitanic.commodity.history', [
                'farmer' => $farmer->id,
                'land' => $land->id,
                'garden' => $garden->id,
            ])
            ->with('success', 'Berhasil disimpan');
    }

    public function destroy(
        Request $request,
        Farmer $farmer,
        Land $land,
        Garden $garden,
        Commodity $commodity
    ) {
        if (
            auth()->user()->role != 'admin' &&
            $farmer->user_id != auth()->user()->id
        ) {
            return abort(403);
        }
        if ($farmer->id != $land->farmer_id) {
            return back()->with('failed', 'Lahan tidak sama dengan yang ada di petani');
        }
        if (
            $farmer->id != $garden->land->farmer_id
        ) {
            return abort(403);
        }
        if ($garden->id != $commodity->garden_id) {
            return back()->with('failed', 'Komoditi tidak sama dengan yang ada di kebun');
        }

        $commodity->delete();

        if ($request->acceptsJson()) {
            return response()->json([
                'message' => 'Berhasil dihapus'
            ]);
        }

        return redirect()
            ->route('bitanic.commodity.history', [
                'farmer' => $farmer->id,
                'land' => $land->id,
                'garden' => $garden->id,
            ])
            ->with('success', 'Berhasil dihapus');
    }

    public function historyCommodities(
        Farmer $farmer,
        Land $land,
        Garden $garden
    ) : View {
        if (
            auth()->user()->role != 'admin' &&
            $farmer->user_id != auth()->user()->id
        ) {
            return abort(403);
        }
        if ($farmer->id != $land->farmer_id) {
            return back()->with('failed', 'Lahan tidak sama dengan yang ada di petani');
        }
        if (
            $farmer->id != $garden->land->farmer_id
        ) {
            return abort(403);
        }

        $history_commodities = Commodity::query()
            ->with('crop:id,crop_name')
            ->where('garden_id', $garden->id)
            ->where('is_finished', 1)
            ->paginate(10);

        return view('bitanic.garden.commodity.history', compact(
            'farmer',
            'garden',
            'land',
            'history_commodities',
        ));
    }
}
