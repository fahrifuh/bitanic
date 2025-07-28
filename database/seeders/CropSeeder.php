<?php

namespace Database\Seeders;

use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CropSeeder extends Seeder
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
                'crop_name' => $faker->word,
                'type' => $faker->randomElement(['buah', 'sayur']),
                'season' => $faker->randomElement(['hujan', 'kemarau']),
                'optimum_temperature' => $faker->numberBetween(20, 27),
                'minimum_temperature' => $faker->numberBetween(15, 20),
                'maximum_temperature' => $faker->numberBetween(27, 34),
                'altitude' => $faker->numberBetween(100, 200),
                'description' => $faker->sentence(15)
            ];
        }

        DB::table('crops')->insert($data);
    }
}
