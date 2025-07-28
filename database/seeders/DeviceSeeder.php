<?php

namespace Database\Seeders;

use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [];
        $faker = Factory::create('id_ID');

        for ($i=0; $i < 10; $i++) {
            $data[] = [
                'device_name' => $faker->word,
                'device_series' => 'BT'.$faker->unique()->regexify('[0-9]{1,5}'),
                'version' => '1.0',
                'production_date' => $faker->date(),
                'purchase_date' => $faker->date(),
                'status' => 0
            ];
        }

        DB::table('devices')->insert($data);
    }
}
