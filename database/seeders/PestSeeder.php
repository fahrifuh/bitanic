<?php

namespace Database\Seeders;

use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PestSeeder extends Seeder
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
                'name' => $faker->word,
                'pest_type' => $faker->randomElement(['mamalia', 'serangga', 'aves', 'nematoda', 'gastropoda'])
            ];
        }

        DB::table('pests')->insert($data);
    }
}
