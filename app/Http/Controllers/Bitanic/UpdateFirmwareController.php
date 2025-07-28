<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\UpdateFirmware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use PhpMqtt\Client\Facades\MQTT;

class UpdateFirmwareController extends Controller
{
    public function index() {
        $update_firmware = UpdateFirmware::query()
            ->when(request()->query('search'), function($query){
                $search = request()->query('search');
                return $query->where('version', 'LIKE', '%'.$search.'%')
                    ->orWhere('series', 'LIKE', '%'.$search.'%');
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('bitanic.firmware.index', compact('update_firmware'));
    }

    public function create() {
        return view('bitanic.firmware.create');
    }

    public function store(Request $request) {
        // dd($request->all());
        $request->validate([
            'series' => 'required|string|max:255|regex:/^[a-zA-Z\d]+$/',
            'firmware_file' => 'required|file',
            'version' => 'required|regex:/^[0-9.]+$/',
        ]);

        $version = str_replace('.', '_', $request->version);

        $file_name = now('Asia/Jakarta')->format('YmdHis') . "_" . $request->series . "_firmware_" . $version . ".bin";
        $path = Storage::putFileAs('firmwares', $request->file('firmware_file'), $file_name);

        UpdateFirmware::create([
            'series' => $request->series,
            'file_path' => $path,
            'version' => $request->version
        ]);

        return back()->with('success', 'Berhasil disimpan');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LiteDevice  $liteDevice
     * @return \Illuminate\Http\Response
     */
    public function destroy(UpdateFirmware $update_firmware)
    {
        if(Storage::exists($update_firmware->file_path)){
            Storage::delete($update_firmware->file_path);
        }

        $update_firmware->delete();

        return response()->json([
            'message' => 'berhasil disimpan'
        ]);
    }

    public function updateSelected(Request $request, UpdateFirmware $updateFirmware)
    {
        DB::table('update_firmware')
            ->where('series', $updateFirmware->series)
            ->where('is_selected', 1)
            ->update([
                'is_selected' => 0
            ]);

        $updateFirmware->update([
            'is_selected' => 1
        ]);

        $message = (object) [
            "id" => $updateFirmware->series,
        ];

        MQTT::publish("bitanic/" . $updateFirmware->series, json_encode($message), false, config('app.mqtt'));

        return response()->json([
            'message' => 'File berhasil dipilih'
        ]);
    }

    public function downloadFile(Request $request) {
        $firmware = UpdateFirmware::query()
            ->where('series', $request->query('id'))
            ->where('is_selected', 1)
            ->orderByDesc('created_at')
            ->first();

        if (!$firmware) {
            return response()->json([
                'message' => 'data tidak ditemukan'
            ]);
        }

        return Storage::download($firmware->file_path);
    }
}
