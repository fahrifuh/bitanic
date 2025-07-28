<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('transaction_settings')->truncate();

        DB::table('transaction_settings')->insert([
            'platform_fees' => 1000,
            'created_at' => now()
        ]);
    }
}
