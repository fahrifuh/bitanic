<?php

namespace Database\Seeders;

use App\Models\Crop;
use Illuminate\Database\Seeder;

class FillMoistureCropsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Crop::query()
            ->update([
                'moisture' => json_encode([
                    "maximum" => 10,
                    "minimum" => 1,
                    "optimum" => 5
                ])
            ]);
    }
}
