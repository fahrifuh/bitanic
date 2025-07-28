<?php

namespace App\Http\Controllers\Bitanic;

use App\Http\Controllers\Controller;
use App\Models\Fertilization;
use App\Models\Garden;
use Illuminate\Http\Request;
use PhpMqtt\Client\Facades\MQTT;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class FertilizationController extends Controller
{
    public function create($farmer, Garden $garden) : View {
        if (!Gate::allows('store-fertilization', $garden)) {
            return response()->json([
                'messages' => (object) [
                    'warning' => ["Anda tidak dapat mengakses data ini"]
                ]
            ], 403);
        }

        return view('bitanic.garden.create-fertilization');
    }

    public function store(Request $request, $farmer, $garden)
    {
        $mqtt = $request->query('mqtt', 1);

        $mqtt = is_numeric($mqtt) ? $mqtt : 0;

        $garden = Garden::query()
            ->with('device')
            ->find($garden);

        if (!$garden) {
            return response()->json([
                'messages' => (object) [
                    'text' => ["Data kebun tidak ditemukan"]
                ]
            ], 404);
        }

        if (!Gate::allows('store-fertilization', $garden)) {
            return response()->json([
                'messages' => (object) [
                    'warning' => ["Anda tidak dapat mengakses data ini"]
                ]
            ], 403);
        }

        if (!$garden->device) {
            return response()->json([
                'messages' => (object) [
                    'errors' => ["Kebun tidak memiliki perangkat!"]
                ]
            ], 400);
        }

        if ($garden->device->status == 0) {
            return response()->json([
                'messages' => (object) [
                    'errors' => ["Status tidak aktif untuk perangkat. Check perangkat anda!"]
                ]
            ], 400);
        }

        if ($garden->unfinished_fertilization) {
            return response()->json([
                'messages' => (object) [
                    'text' => ["Kebun masih memiliki pemupukan yang belum selesai!"]
                ]
            ], 404);
        }

        $v = Validator::make($request->all(), [
            'crop_name'              => 'required|string|max:255',
            'weeks'             => 'required|integer|min:0|max:15',
            'set_time'       => 'required|date_format:H:i',
            'set_minute'              => 'required|integer|min:0|max:60',
            'days'              => 'required',
        ]);

        if ($v->fails()) {
            return response()->json([
                'messages' => $v->errors()
            ], 400);
        }

        $days = ['minggu', 'senin', 'selasa', 'rabu', 'kamis', "jumat", 'sabtu'];

        $sethari = '';

        foreach ($days as $day) {
            if (in_array($day, explode(',', $request->days), true)) {
                $sethari .= '1';
            } else {
                $sethari .= "0";
            }
        }

        $sethari = collect(str_split($sethari))->implode(',');

        $setwaktu = now()->parse($request->set_time)->format('H:i:s');

        Fertilization::create($request->only([
            'crop_name',
            'weeks',
            'set_minute'
        ])+[
            'set_time' => $setwaktu,
            'set_day' => $sethari,
            'garden_id' => $garden->id,
            'device_id' => optional($garden->device)->id,
            'farmer_id' => $garden->farmer_id
        ]);

        $device_series = optional($garden->device)->device_series;

        if ($device_series) {
            MQTT::publish('bitanic/'.$device_series, 'RESETALL,*');
            MQTT::publish('bitanic/'.$device_series, 'SETHARI,'.$sethari.',*');
            MQTT::publish('bitanic/'.$device_series, 'SETMINGGU,'.$request->weeks.',*');
            MQTT::publish('bitanic/'.$device_series, 'SETONTIME1,'.$setwaktu.','.$request->set_minute.',*');
            MQTT::publish('bitanic/'.$device_series, 'SETONTIME2,00:00:00,00,*');
            MQTT::publish('bitanic/'.$device_series, 'SETONTIME3,00:00:00,00,*');
            MQTT::publish('bitanic/'.$device_series, 'SETONTIME4,00:00:00,00,*');
            MQTT::publish('bitanic/'.$device_series, 'SETONTIME5,00:00:00,00,*');
        }


        return response()->json([
            'message' => "Berhasil disimpan"
        ], 200);
    }

    public function resendSetting(Request $request, $farmer, $garden, $id)
    {
        $fertilization = Fertilization::query()
            ->with(['garden.device'])
            ->find($id);

        if (!$fertilization) {
            return response()->json([
                'messages' => (object) [
                    'text' => ['Data tidak ditemukan']
                ]
            ], 404);
        }

        $device_series = optional($fertilization->garden->device)->device_series;

        if ($device_series) {
            MQTT::publish('bitanic/'.$device_series, 'RESETALL,*');
            MQTT::publish('bitanic/'.$device_series, 'SETHARI,'.$fertilization->set_day.',*');
            MQTT::publish('bitanic/'.$device_series, 'SETMINGGU,'.$fertilization->weeks.',*');
            MQTT::publish('bitanic/'.$device_series, 'SETONTIME1,'.$fertilization->set_time.','.$fertilization->set_minute.',*');
            MQTT::publish('bitanic/'.$device_series, 'SETONTIME2,00:00:00,00,*');
            MQTT::publish('bitanic/'.$device_series, 'SETONTIME3,00:00:00,00,*');
            MQTT::publish('bitanic/'.$device_series, 'SETONTIME4,00:00:00,00,*');
            MQTT::publish('bitanic/'.$device_series, 'SETONTIME5,00:00:00,00,*');
        }


        return response()->json([
            'message' => 'Setting dikirim. Periksa kembali perangkat anda.'
        ], 200);
    }

    public function destroyAndResetDevice($farmer, $garden, $id)
    {
        $fertilization = Fertilization::find($id);

        if (!$fertilization) {
            return response()->json([
                'messages' => (object) [
                    'text' => ['Data tidak ditemukan']
                ]
            ], 404);
        }

        $device_series = optional($fertilization->garden->device)->device_series;

        if ($device_series) {
            MQTT::publish('bitanic/'.$device_series, 'RESETALL,*');
        }

        $fertilization->delete();

        return response()->json([
            'message' => 'Perangkat direset dan pemupukan telah dihapus. Harap periksa kembali perangkat anda.'
        ], 200);
    }

    public function stopAndSaveFertilization(Request $request, $farmer, $garden, $id)
    {
        $fertilization = Fertilization::find($id);

        if (!$fertilization) {
            return response()->json([
                'messages' => (object) [
                    'text' => ['Data tidak ditemukan']
                ]
            ], 404);
        }

        $device_series = optional($fertilization->garden->device)->device_series;

        if ($device_series) {
            MQTT::publish('bitanic/'.$device_series, 'RESETALL,*');
        }

        $fertilization->is_finished = 1;
        $fertilization->save();

        return response()->json([
            'message' => 'Setting dikirim. Periksa kembali perangkat anda.'
        ], 200);
    }

    public function destroyFinished($id)
    {
        $fertilization = Fertilization::query()
            ->where('is_finished', 1)
            ->findOrFail($id);

        $fertilization->delete();

        return response()->json([
            'message' => 'Berhasil disimpan'
        ], 200);
    }
}
