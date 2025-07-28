<?php

namespace Database\Seeders;

use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GardenSeeder extends Seeder
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

        for ($i=0; $i < 5; $i++) {
            $lat = -6.777246;
            $data[] = [
                'name' => $faker->lastName.' Garden',
                'owner' => $faker->firstName(),
                'address' => $faker->address,
                'gardes_type' => $faker->randomElement(['buah', 'sayur']),
                'area' => $faker->numberBetween(100, 250),
                'unit' => 'm',
                'lat' => -6.777246,
                'lng' => 107.863190,
                'alt' => 200,
                'season' => $faker->randomElement(['hujan', 'kemarau']),
                'optimum_temperature' => $faker->numberBetween(20, 27),
                'minimum_temperature' => $faker->numberBetween(15, 20),
                'maximum_temperature' => $faker->numberBetween(27, 34),
                'altitude' => $faker->numberBetween(100, 200),
                'description' => $faker->sentence(15)
            ];
        }

        // DB::table('crops')->insert($data);
    }
}
