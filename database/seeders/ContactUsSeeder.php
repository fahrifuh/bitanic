<?php

namespace Database\Seeders;

use App\Models\ContactUsMessage;
use App\Models\ContactUsSetting;
use Illuminate\Database\Seeder;

class ContactUsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!ContactUsSetting::first()) {
            ContactUsSetting::create([
                'email' => 'costumer@bitanic.id',
                'phone_number' => '+62873-8000-135',
                'address' => 'Pesona Ciganitri, No. A 39, Bojongsoang, Cipagalo, Bojongsoang, Bandung Regency, West Java 40287.',
                'linkedin_link' => '#',
                'ig_link' => '#',
                'facebook_link' => '#',
            ]);
        }
    }
}
