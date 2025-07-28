<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Fertilization;
use App\Models\Garden;
use Illuminate\Http\Request;
use PhpMqtt\Client\Facades\MQTT;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class FertilizationController extends Controller
{
    public function index($garden)
    {
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

        $fertilizations = Fertilization::query()
            ->where('garden_id', $garden->id)
            ->get()
            ->groupBy(function ($item, $key) {
                return $item['is_finished'] == 1 ? 'finish' : 'unfinish';
            });

        if (!$fertilizations->has('finish')) {
            $fertilizations->put('finish', []);
        }

        if ($fertilizations->has('unfinish')) {
            $fertilizations['unfinish'] = $fertilizations['unfinish'][0];
        } else {
            $fertilizations->put('unfinish', null);
        }

        return response()->json([
            'fertilizations' => $fertilizations,
            'status' => 200
        ], 200);
    }

    public function show($garden, $id)
    {
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

        $fertilization = Fertilization::query()
            ->with(['schedule' => function($query){
                $query->select('id', 'fertilization_id', 'type', 'week', 'day', 'start_time', 'end_time');
            }])
            ->find($id);

        if (!$fertilization) {
            return response()->json([
                'messages' => (object) [
                    'text' => ["Data schedule pemupukan tidak ditemukan"]
                ]
            ], 404);
        }

        if (!Gate::allows('delete-fertilization', $fertilization)) {
            return response()->json([
                'messages' => (object) [
                    'warning' => ["Anda tidak dapat mengakses data ini"]
                ]
            ], 403);
        }

        if ($fertilization->schedule) {
            $hari_pengiriman = now()->parse($fertilization->created_at)->format('N');
            $collect = collect($fertilization->schedule);
            $group = $collect->groupBy('week');

            $telemetri = collect([]);

            $days = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", 'Jum\'at', "Sabtu"];

            foreach ($group->all() as $week => $val) {
                if ($week > 0) {
                    $array_week = [];
                    foreach ($val as $schedule) {
                        $hari = $schedule->hari + 1;
                        $diff = $hari - $hari_pengiriman;
                        if ($diff >= 0) {
                            $tgl = now()->parse($fertilization->created_at)->addDays($diff)->addWeeks($schedule->week - 1)->format('Y/m/d');
                        } else {
                            $tgl = now()->parse($fertilization->created_at)->subDays(abs($diff))->addWeeks($schedule->week - 1)->format('Y/m/d');
                        }

                        $start_time = ($schedule->start_time) ? today()->parse($schedule->start_time)->format('H:i:s') : null;
                        $end_time = ($schedule->end_time) ? today()->parse($schedule->end_time)->format('H:i:s') : null;

                        if ($schedule->type == "schedule") {
                            $start_time = today()->parse($fertilization->set_time)->format('H:i:s');
                            $end_time = today()->parse($fertilization->set_time)->addMinutes($fertilization->set_minute)->format('H:i:s');
                        }

                        $array_week[] = (object)[
                            'week' => $schedule->week,
                            'day' => $days[$schedule->day],
                            'date' => $tgl,
                            'start_time' => $start_time,
                            'end_time' => $end_time,
                            'type' => str_replace('_', " ", $schedule->type)
                        ];
                    }
                    $telemetri->put($week, $array_week);
                }
            }

            $fertilization['new_schedule'] = $telemetri->all();
        }

        return response()->json([
            'fertilization' => $fertilization,
            'status' => 200
        ], 200);
    }

    public function store(Request $request, $garden)
    {
        $user = auth()->user();

        $garden = Garden::query()
            ->with('device')
            ->find($garden);

        if (!$garden) {
            return response()->json([
                'messages' => (object) [
                    'kebun' => ["Data kebun tidak ditemukan"]
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

        if ($garden->unfinished_fertilization) {
            return response()->json([
                'messages' => (object) [
                    'text' => ["Kebun masih memiliki pemupukan yang belum selesai!"]
                ]
            ], 404);
        }

        if ($garden->device->type == 2) {
            if (!$request->setwaktu || !is_string($request->setwaktu) && !is_array($request->setwaktu)) {
                return response()->json([
                    'messages' => [
                        'setwaktu' => [
                            'waktu harus diisi',
                            'Waktu harus berupa json atau string json'
                        ]
                    ]
                ], 400);
            }

            if (is_string($request->setwaktu) && json_decode($request->setwaktu) == null) {
                return response()->json([
                    'messages' => [
                        'setwaktu' => [
                            'format data waktu tidak sesuai, pastikan isi berupa json'
                        ]
                    ]
                ], 400);
            }

            if (!$request->days || !is_string($request->days) && !is_array($request->days)) {
                return response()->json([
                    'messages' => [
                        'days' => [
                            'waktu harus diisi',
                            'Waktu harus berupa json atau string json'
                        ]
                    ]
                ], 400);
            }

            if (is_string($request->days) && json_decode($request->days) == null) {
                return response()->json([
                    'messages' => [
                        'setwaktu' => [
                            'format data waktu tidak sesuai, pastikan isi berupa json'
                        ]
                    ]
                ], 400);
            }

            if (is_string($request->setwaktu)) {
                $request['setwaktu'] = json_decode($request->setwaktu);
            } elseif (is_array($request->setwaktu)) {
                $request['setwaktu'] = json_decode(json_encode($request->setwaktu));
            }

            if (is_string($request->days)) {
                $request['days'] = json_decode($request->days);
            }

            if (count($request->setwaktu) > 5) {
                return response()->json([
                    'errors' => [
                        'messages' => ['Set waktu tidak bisa lebih dari 5']
                    ]
                ], 400);
            }
        }

        $v = Validator::make($request->all(), [
            'crop_name'              => 'required|string|max:255',
            'weeks'             => 'required|integer|min:1|max:15',
            // 'set_time'       => 'required|date_format:H:i',
            // 'set_minute'              => 'required|integer|min:0',
            'days'              => 'required|array',
            // 'setwaktu' => 'required|array|max:5',
            // 'setwaktu.*.time' => 'required|date_format:H:i',
            // 'setwaktu.*.minute' => 'required|integer|min:0',
            // 'setwaktu.*.selenoid' => 'required|array|max:6',
            // 'setwaktu.*.selenoid.*' => 'in:0,1',
        ]);

        if ($v->fails()) {
            return response()->json([
                'messages' => $v->errors()
            ], 400);
        }

        if ($garden->device->type == 1) {
            $v2 = Validator::make($request->all(), [
                'set_time'      => 'required|date_format:H:i',
                'set_minute'    => 'required|integer|min:0',
            ]);

            if ($v2->fails()) {
                return response()->json([
                    'messages' => $v2->errors()
                ], 400);
            }
        }

        $days = ['minggu', 'senin', 'selasa', 'rabu', 'kamis', 'jum\'at', 'sabtu'];

        $sethari = '';

        foreach ($days as $day) {
            if (in_array($day, $request->days, true)) {
                $sethari .= '1';
            } else {
                $sethari .= '0';
            }
        }

        $sethari = collect(str_split($sethari))->implode(',');

        $setwaktu = now()->parse(($garden->device->type == 1) ? $request->set_time : $request->setwaktu[0]->time)->format('H:i:s');

        $settimes = collect([]);
        $checkTime = null;

        $setminutes = ($garden->device->type == 1) ? $request->set_minute : 0;

        if ($garden->device->type == 2) {
            $h = 1;
            foreach ($request->setwaktu as $val) {
                $valTime = now()->parse($val->time);
                if ($checkTime && $valTime->lte($checkTime)) { // check if current time is smaller than previous time
                    return response()->json([
                        'errors' => [
                            'messages' => ["Waktu $h tidak bisa KURANG dari waktu sebelum!"]
                        ]
                    ], 400);
                }

                $settimes->push((object) [
                    'time' => $valTime->format('H:i'),
                    'minute' => $val->minute,
                    'selenoid' => $val->selenoid
                ]);

                $checkTime = now()->parse($val->time);
                $h++;
            }

            $settimes = collect($request->setwaktu)->map(function($val,$key){
                return (object) [
                    'time' => now()->parse($val->time)->format('H:i:s'),
                    'minute' => $val->minute,
                    'selenoid' => $val->selenoid
                ];
            });
        }

        Fertilization::create($request->only([
            'crop_name',
            'weeks',
            // 'set_minute'
        ])+[
            'set_minute' => $setminutes,
            'set_time' => $setwaktu,
            'set_day' => $sethari,
            'garden_id' => $garden->id,
            'device_id' => optional($garden->device)->id,
            'farmer_id' => $user->farmer->id,
            'settimes' => $settimes->all()
        ]);

        $device_series = $garden->device->device_series;

        if ($device_series) {
            $topic = 'bitanic/'.$device_series;
            $mqtt = MQTT::connection();
            $mqtt->publish($topic, 'RESETALL,*');
            $mqtt->publish($topic, 'SETHARI,'.$sethari.',*');
            $mqtt->publish($topic, 'SETMINGGU,'.$request->weeks.',*');

            if ($garden->device->type == 1) {
                $mqtt->publish($topic, "SETONTIME1,$setwaktu,$setminutes,*");
                for ($i = 2; $i <= 5; $i++) {
                    $mqtt->publish($topic, "SETONTIME$i,00:00:00,0,*");
                }
            } elseif ($garden->device->type == 2) {
                $i = 1;
                foreach ($settimes->all() as $time) {
                    $selenoid = collect($time->selenoid);
                    if ($selenoid->count() < 6) {
                        for ($j=$selenoid->count(); $j < 6; $j++) {
                            $selenoid->push(0);
                        }
                    }
                    $mqtt->publish($topic, "SETONTIME$i,". $time->time .",". $time->minute .",". $selenoid->join(',') .",*");
                    $i++;
                }

                for ($i; $i < 6; $i++) {
                    $mqtt->publish($topic, "SETONTIME$i,00:00:00,00,0,0,0,0,0,0,*");
                }
            }

            $mqtt->disconnect();
        }

        return response()->json([
            'message' => "Berhasil disimpan",
            'status' => 200
        ], 200);
    }

    public function destroy($garden, $id)
    {
        $garden = Garden::query()
            ->find($garden);

        if (!$garden) {
            return response()->json([
                'messages' => (object) [
                    'text' => ["Data kebun tidak ditemukan"]
                ]
            ], 404);
        }

        $fertilization = Fertilization::find($id);

        if (!$fertilization) {
            return response()->json([
                'messages' => (object) [
                    'text' => ["Data schedule tidak ditemukan"]
                ]
            ], 404);
        }

        if (!Gate::allows('delete-fertilization', $fertilization)) {
            return response()->json([
                'messages' => (object) [
                    'warning' => ["Anda tidak dapat mengakses data ini"]
                ]
            ], 403);
        }

        $device_series = optional($fertilization->garden->device)->device_series;

        if ($device_series) {
            MQTT::publish('bitanic/'.$device_series, 'RESETALL,*');
        }

        $fertilization->delete();

        return response()->json([
            'message' => "Data dihapus!",
            'status' => 200
        ], 200);
    }
}
