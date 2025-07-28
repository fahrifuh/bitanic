<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddFormulaCropsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('crops')->update([
            'target_ph' => 6.5,
            'target_persen_corganik' => 3,
            'frekuensi_siram' => 15,
            'n_kg_ha' => 156,
            'sangat_rendah_p2o5' => 100,
            'rendah_p2o5' => 67,
            'sedang_p2o5' => 44,
            'tinggi_p2o5' => 0,
            'sangat_tinggi_p2o5' => 0,
            'sangat_rendah_k2o' => 156,
            'rendah_k2o' => 100,
            'sedang_k2o' => 44,
            'tinggi_k2o' => 0,
            'sangat_tinggi_k2o' => 0,
            'catatan' => "250 251 350 351 352 353 354"
        ]);
    }
}
