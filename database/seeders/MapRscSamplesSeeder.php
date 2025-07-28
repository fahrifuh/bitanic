<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MapRscSamplesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rsc_telemetries = DB::table('rsc_telemetris')
            ->get();

        foreach ($rsc_telemetries as $rsc_telemetry) {
            if (is_array(json_decode($rsc_telemetry->samples))) {
                $samples = json_decode($rsc_telemetry->samples);

                $insert_telemetries = collect($samples)->map(function($sample, $key)use($rsc_telemetry){
                    return [
                        'device_id' => $rsc_telemetry->device_id,
                        'land_id' => $rsc_telemetry->land_id,
                        'samples' => json_encode((object) [
                            "latitude" => $sample->latitude,
                            "longitude" => $sample->longitude,
                            "moisture" => $sample->moisture,
                            "temperature" => $sample->temperature,
                            "n" => $sample->n,
                            "p" => $sample->p,
                            "k" => $sample->k,
                            "rh" => isset($sample->rh) ? $sample->rh : null,
                            "t" => isset($sample->t) ? $sample->t : null,
                            "co2" => isset($sample->co2) ? $sample->co2 : null,
                            "no2" => isset($sample->no2) ? $sample->no2 : null,
                            "n2o" => isset($sample->n2o) ? $sample->n2o : null,
                        ]),
                        'created_at' => $rsc_telemetry->created_at
                    ];
                })
                ->all();

                DB::table('rsc_telemetris')->where('id', $rsc_telemetry->id)->delete();

                DB::table('rsc_telemetris')->insert($insert_telemetries);
            }
        }
    }
}
