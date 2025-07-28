<?php

namespace Database\Seeders;

use App\Models\Interpretation;
use App\Models\LevelInterpretation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InterpretationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('interpretations')->delete();

        $data = [
            [
                'unsur' => "P",
                'sangat_rendah' => "1",
                'rendah' => "1-3",
                'sedang' => "3-5",
                'tinggi' => "5-6",
                'sangat_tinggi' => "6",
                'ppm' => 2.5,
                'status' => 'rendah'
            ],
            [
                'unsur' => "K",
                'sangat_rendah' => "2",
                'rendah' => "2-3.5",
                'sedang' => "3.5-6",
                'tinggi' => "6-12.5",
                'sangat_tinggi' => "12.5",
                'ppm' => 5,
                'status' => 'sedang'
            ],
            [
                'unsur' => "Mg",
                'sangat_rendah' => null,
                'rendah' => "15",
                'sedang' => "15-30",
                'tinggi' => "30",
                'sangat_tinggi' => null,
                'ppm' => 20,
                'status' => 'sedang'
            ],
            [
                'unsur' => "Ca",
                'sangat_rendah' => "50",
                'rendah' => "50-100",
                'sedang' => "100-300",
                'tinggi' => "300-500",
                'sangat_tinggi' => "500",
                'ppm' => 20,
                'status' => 'sangat_rendah'
            ],
            [
                'unsur' => "Corg",
                'sangat_rendah' => "1",
                'rendah' => "1-2",
                'sedang' => "2-3",
                'tinggi' => "3-5",
                'sangat_tinggi' => "5",
                'ppm' => 1.6,
                'status' => 'rendah'
            ]
        ];

        foreach ($data as $value) {
            $unsur = Interpretation::insertGetId([
                'nama' => $value['unsur']
            ]);

            LevelInterpretation::create([
                'interpretation_id' => $unsur,
                'sangat_rendah' => $value['sangat_rendah'],
                'rendah' => $value['rendah'],
                'sedang' => $value['sedang'],
                'tinggi' => $value['tinggi'],
                'sangat_tinggi' => $value['sangat_tinggi']
            ]);
        }
    }
}
