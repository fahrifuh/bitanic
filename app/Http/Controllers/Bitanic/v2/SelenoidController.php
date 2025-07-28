<?php

namespace App\Http\Controllers\Bitanic\v2;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Land;
use App\Models\Selenoid;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SelenoidController extends Controller
{
    public function create(Device $device) {
        if (! Gate::allows('update-device', $device)) {
            abort(403);
        }

        if (!$device->farmer_id) {
            return back()->with('failed', 'Perangkat harus dimiliki oleh petani lebih dulu!');
        }

        $role = auth()->user()->role;

        $lands = Land::query()
            ->doesntHave('selenoid')
            ->when($role == 'farmer', function ($query, $sortByVotes) {
                return $query->where('farmer_id', auth()->user()->farmer->id);
            })
            ->when($role != 'farmer', function ($query, $sortByVotes) use($device){
                return $query->where('farmer_id', $device->farmer_id);
            })
            ->pluck('name', 'id');

        return view('bitanic.device.type-3.selenoid.create', compact('lands', 'device'));
    }

    public function store(Request $request, Device $device) {
        if (! Gate::allows('update-device', $device)) {
            abort(403);
        }

        $request->validate([
            'land_id' => 'required|integer|min:0',
            'water_before' => 'required|array',
            'water_after' => 'required|array',
            'water_before.minutes' => 'required_without:water_before.seconds|nullable|integer|min:0|max:59',
            'water_before.seconds' => 'required_without:water_before.minutes|nullable|integer|min:0|max:59',
            'water_after.minutes' => 'required_without:water_after.seconds|nullable|integer|min:0|max:59',
            'water_after.seconds' => 'required_without:water_after.minutes|nullable|integer|min:0|max:59',
        ]);

        $device->loadCount('selenoids');

        $role = auth()->user()->role;

        $land = Land::query()
            ->doesntHave('selenoid')
            ->when($role == 'farmer', function ($query, $sortByVotes) {
                return $query->where('farmer_id', auth()->user()->farmer->id);
            })
            ->findOrFail($request->land_id, ['id', 'farmer_id']);

        $selenoid_id = $device->selenoids_count + 1;

        if ($selenoid_id > 4) {
            return back()->withErrors([
                'messages' => ['Perangkat sudah mencapai batas untuk menangani lahan. Harap pilih perangkat lain!']
            ])->withInput();
        }

        Selenoid::create([
            'device_id' => $device->id,
            'land_id' => $request->land_id,
            'selenoid_id' => $selenoid_id,
            'selenoid_status' => 0,
            'selenoid_watering' => (object) [
                'water_before' => (object) [
                    "minutes" => (int) $request['water_before']['minutes'],
                    "seconds" => (int) $request['water_before']['seconds'],
                ],
                'water_after' => (object) [
                    "minutes" => (int) $request['water_after']['minutes'],
                    "seconds" => (int) $request['water_after']['seconds'],
                ]
            ]
        ]);

        return redirect()->route('bitanic.v3-device.show', $device->id)->with('success', 'Berhasil disimpan');
    }

    public function edit(Device $device, Selenoid $selenoid) {
        if (! Gate::allows('update-device', $device)) {
            abort(403);
        }

        if ($device->id != $selenoid->device_id) {
            abort(403, 'Data selenoid tidak sama dengan yang ada di perangkat');
        }

        if (!$device->farmer_id) {
            return back()->with('failed', 'Perangkat harus dimiliki oleh petani lebih dulu!');
        }

        $role = auth()->user()->role;

        $lands = Land::query()
            ->whereDoesntHave('selenoid', function($query)use($selenoid){
                $query->where('id', '<>', $selenoid->id);
            })
            ->when($role == 'farmer', function ($query, $sortByVotes) {
                return $query->where('farmer_id', auth()->user()->farmer->id);
            })
            ->when($role != 'farmer', function ($query, $sortByVotes) use($device){
                return $query->where('farmer_id', $device->farmer_id);
            })
            ->pluck('name', 'id');

        return view('bitanic.device.type-3.selenoid.edit', compact('lands', 'device', 'selenoid'));
    }

    public function update(Request $request, Device $device, Selenoid $selenoid) {
        if (! Gate::allows('update-device', $device)) {
            abort(403);
        }

        if ($device->id != $selenoid->device_id) {
            abort(403, 'Data selenoid tidak sama dengan yang ada di perangkat');
        }

        $request->validate([
            'land_id' => 'required|integer|min:0',
            'water_before' => 'required|array',
            'water_after' => 'required|array',
            'water_before.minutes' => 'required_without:water_before.seconds|nullable|integer|min:0|max:59',
            'water_before.seconds' => 'required_without:water_before.minutes|nullable|integer|min:0|max:59',
            'water_after.minutes' => 'required_without:water_after.seconds|nullable|integer|min:0|max:59',
            'water_after.seconds' => 'required_without:water_after.minutes|nullable|integer|min:0|max:59',
        ]);

        $role = auth()->user()->role;

        $land = Land::query()
            ->whereDoesntHave('selenoid', function($query)use($selenoid){
                $query->where('id', '<>', $selenoid->id);
            })
            ->when($role == 'farmer', function ($query, $sortByVotes) {
                return $query->where('farmer_id', auth()->user()->farmer->id);
            })
            ->findOrFail($request->land_id, ['id', 'farmer_id']);

        $selenoid->update([
            'land_id' => $request->land_id,
            'selenoid_watering' => (object) [
                'water_before' => (object) [
                    "minutes" => (int) $request['water_before']['minutes'],
                    "seconds" => (int) $request['water_before']['seconds'],
                ],
                'water_after' => (object) [
                    "minutes" => (int) $request['water_after']['minutes'],
                    "seconds" => (int) $request['water_after']['seconds'],
                ]
            ]
        ]);

        return back()->with('success', 'berhasil disimpan');
    }

    public function destroy(Device $device, Selenoid $selenoid) : JsonResponse {
        if (! Gate::allows('update-device', $device)) {
            abort(403);
        }

        if ($device->id != $selenoid->device_id) {
            abort(403, 'Data selenoid tidak sama dengan yang ada di perangkat');
        }

        $selenoid_id = $selenoid->selenoid_id;

        $selenoid->delete();

        $lands = Selenoid::query()
            ->where([
                ['device_id', $device->id],
                ['selenoid_id', '>', $selenoid_id],
            ])
            ->orderBy('selenoid_id')
            ->get(['id', 'device_id', 'selenoid_id']);

        foreach ($lands as $a) {
            $a->update([
                'selenoid_id' => $selenoid_id
            ]);

            $selenoid_id++;
        }

        return response()->json('berhasil dihapus');
    }
}
