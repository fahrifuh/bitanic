<?php

namespace App\Http\Controllers\Api\Mobile\Lite;

use App\Http\Controllers\Controller;
use App\Models\Crop;
use App\Models\LiteDevice;
use App\Models\LiteDeviceSchedule;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use PhpMqtt\Client\Facades\MQTT;

class ScheduleController extends Controller
{
    public function store(Request $request, LiteDevice $liteDevice) : JsonResponse {
        if (!Gate::allows('lite-device-mobile', $liteDevice)) {
            return response()->json([
                'errors' => (object) [
                    'role' => ["Anda tidak dapat mengakses data ini"]
                ]
            ], 403);
        }

        $request->validate([
            'setontimes' => 'required|json',
            'days' => 'required|json',
            'weeks' => 'required|integer|min:1|max:15',
            'crop_id' => 'nullable|integer',
        ]);

        $crop = null;

        if ($request->crop_id) {
            $crop = Crop::query()
                ->find($request->crop_id, ['id', 'crop_name']);
        }

        $days = json_decode($request->days, true);
        $setontimes = json_decode($request->setontimes, true);

        $v = Validator::make([
            'days' => $days,
            'setontimes' => $setontimes,
        ], [
            'setontimes' => 'required|array|min:1',
            'setontimes.*.time' => 'required|date_format:H:i',
            'setontimes.*.duration.minutes' => 'required|integer|min:0|max:59',
            'setontimes.*.duration.seconds' => 'required|integer|min:0|max:59',
            'setontimes.*.pumps.*.number' => 'required|integer',
            'setontimes.*.pumps.*.status' => 'required|boolean',
            'days' => 'required|array|min:1|max:7',
            'days.*' => 'string|in:senin,selasa,rabu,kamis,jum\'at,sabtu,minggu'
        ]);

        if ($v->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $v->errors()
            ], 422);
        }

        try {
            [$formatedSetontimes, $messageSetontimes, $timeDurations] = $this->checkAndFormatSetontimes($setontimes);
        } catch (Exception $th) {
            return response()->json([
                'errors' => [
                    'setontimes' => [
                        $th->getMessage(),
                    ]
                ]
            ], 400);
        }

        $lastestTime = $timeDurations[count($timeDurations) - 1]->end;

        $countLasyDay = $this->countLastDay($days);

        $end_date = now('Asia/Jakarta')->startOfWeek()->addWeeks($request->weeks - 1)->addDays($countLasyDay)->format('Y-m-d');

        $end_datetime = $end_date . " " .$lastestTime->format('H:i:s');

        LiteDeviceSchedule::create($request->only([
            'weeks',
        ]) + [
            'crop_id' => $crop?->id ?? null,
            'crop_name' => $crop?->crop_name ?? null,
            'lite_device_id' => $liteDevice->id,
            'days' => $days,
            'setontimes' => $formatedSetontimes,
            'end_datetime' => $end_datetime
        ]);

        $message = (object) [
            "type" => "lite",
            "mode" => "jadwal",
            "jadwal" => (object) [
                "minggu" => $request->weeks,
                "days" => $days,
                "setontimes" => $messageSetontimes
            ]
        ];

        MQTT::publish("bitanic/" . $liteDevice->full_series, json_encode($message), false, config('app.mqtt'));

        return response()->json([
            'message' => 'Jadwal berhasil dibuat. Silahkan check perangkat untuk memastikan jadwal sudah terkirim.'
        ], 200);
    }

    private function checkAndFormatSetontimes(array $setontimes)
    {
        $dbFormatedSetontimes = collect();
        $messageFormatedSetontimes = collect();
        $timeDurations = collect();
        $checkTime = null;
        $j = 1;

        foreach ($setontimes as $setontime) {
            $time = now('Asia/Jakarta')->parse($setontime['time']);
            if ($checkTime && $time->lte($checkTime)) { // check if current time is smaller than previous time
                throw new Exception("Waktu $j tidak bisa KURANG atau SAMA dari waktu sebelum!");
            }

            $dbFormatedSetontimes->push((object) [
                "time" => $setontime["time"],
                "duration" => (object) [
                    "minute" => $setontime["duration"]["minutes"],
                    "seconds" => $setontime["duration"]["seconds"],
                ],
                "pump" => $setontime["pumps"]
            ]);

            $messageFormatedSetontimes->push((object) [
                "time" => $setontime["time"],
                "duration" => (object) [
                    "minute" => $setontime["duration"]["minutes"],
                    "seconds" => $setontime["duration"]["seconds"],
                ],
                "pump" => collect($setontime["pumps"])->mapWithKeys(function ($pump, $key) {
                    return ["pump" . $pump["number"] => $pump["status"]];
                })->all()
            ]);

            $timeDurations->push((object) [
                "start" => $time->copy(),
                "end" => $time->copy()->addMinute()->addMinutes((int) $setontime["duration"]["minutes"])->addSeconds((int) $setontime["duration"]["seconds"]),
            ]);

            $checkTime = $time->copy();

            $j++;
        }

        $timeDurations = $timeDurations->all();

        for ($i=1; $i < count($timeDurations); $i++) {
            if ($timeDurations[$i]->start->between($timeDurations[$i-1]->start, $timeDurations[$i-1]->end)) {
                throw new Exception("Terdapat waktu yang bentrok, harap cek kembali waktunya!");
            }
        }

        return [$dbFormatedSetontimes->all(), $messageFormatedSetontimes->all(), $timeDurations];
    }

    private function countLastDay(array $days) {
        return collect($days)
            ->map(function($day, $key){
                return getDayIndex($day);
            })
            ->sortDesc()
            ->values()
            ->first();
    }

    public function destroy(LiteDevice $liteDevice, LiteDeviceSchedule $liteDeviceSchedule) : JsonResponse {
        if (!Gate::allows('lite-device-mobile', $liteDevice)) {
            return response()->json([
                'errors' => (object) [
                    'role' => ["Anda tidak dapat mengakses data ini"]
                ]
            ], 403);
        }

        if ($liteDevice->id != $liteDeviceSchedule->lite_device_id) {
            abort(403, 'Data jadwal tidak sama dengan yang ada di perangkat');
        }

        $liteDeviceSchedule->delete();

        return response()->json([
            'message' => 'Berhasil dihapus'
        ], 200);
    }
}
