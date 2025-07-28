<?php

namespace Database\Seeders;

use App\Models\Farmer;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FarmerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create('id_ID');
        for ($i=0; $i < 10; $i++) {
            $user = User::create([
                'name' => $faker->name,
                'phone_number' => $faker->unique()->regexify('628[1-9]{11}'),
                'email_verified_at' => now('Asia/Jakarta'),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
                'role' => 'farmer'
            ]);

            Farmer::create([
                'user_id' => $user->id,
                'full_name' => $user->name,
                'gender' => 'l',
                'nik' => $faker->unique()->regexify('[0-9]{16}'),
                'address' => $faker->address,
                'referral_code' => $faker->unique()->regexify('[0-9a-z]{7,10}')
            ]);
        }
    }
}
