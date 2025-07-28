<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NecessityDiffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('necessity_differences')->delete();

        $now = now('Asia/Jakarta');

        $data = [
            [
                'selisih_ph' => 0.1,
                'kebutuhan_dolomit' => 0.8,
                'created_at' => $now
            ],
            [
                'selisih_ph' => 0.2,
                'kebutuhan_dolomit' => 1.1,
                'created_at' => $now
            ],
            [
                'selisih_ph' => 0.3,
                'kebutuhan_dolomit' => 1.5,
                'created_at' => $now
            ],
            [
                'selisih_ph' => 0.4,
                'kebutuhan_dolomit' => 1.8,
                'created_at' => $now
            ],
            [
                'selisih_ph' => 0.5,
                'kebutuhan_dolomit' => 2.1,
                'created_at' => $now
            ],
            [
                'selisih_ph' => 0.6,
                'kebutuhan_dolomit' => 2.4,
                'created_at' => $now
            ],
            [
                'selisih_ph' => 0.7,
                'kebutuhan_dolomit' => 2.7,
                'created_at' => $now
            ],
            [
                'selisih_ph' => 0.8,
                'kebutuhan_dolomit' => 3,
                'created_at' => $now
            ],
            [
                'selisih_ph' => 0.9,
                'kebutuhan_dolomit' => 3.4,
                'created_at' => $now
            ],
            [
                'selisih_ph' => 1,
                'kebutuhan_dolomit' => 3.7,
                'created_at' => $now
            ],
            [
                'selisih_ph' => 1.1,
                'kebutuhan_dolomit' => 4.2,
                'created_at' => $now
            ],
            [
                'selisih_ph' => 1.2,
                'kebutuhan_dolomit' => 4.3,
                'created_at' => $now
            ],
            [
                'selisih_ph' => 1.3,
                'kebutuhan_dolomit' => 4.6,
                'created_at' => $now
            ],
            [
                'selisih_ph' => 1.4,
                'kebutuhan_dolomit' => 4.9,
                'created_at' => $now
            ],
            [
                'selisih_ph' => 1.5,
                'kebutuhan_dolomit' => 5.3,
                'created_at' => $now
            ],
            [
                'selisih_ph' => 1.6,
                'kebutuhan_dolomit' => 5.6,
                'created_at' => $now
            ],
            [
                'selisih_ph' => 1.7,
                'kebutuhan_dolomit' => 5.9,
                'created_at' => $now
            ],
            [
                'selisih_ph' => 1.8,
                'kebutuhan_dolomit' => 6.2,
                'created_at' => $now
            ],
            [
                'selisih_ph' => 1.9,
                'kebutuhan_dolomit' => 6.5,
                'created_at' => $now
            ],
            [
                'selisih_ph' => 2,
                'kebutuhan_dolomit' => 6.8,
                'created_at' => $now
            ],
        ];

        DB::table('necessity_differences')->insert($data);
    }
}
