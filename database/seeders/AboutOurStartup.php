<?php

namespace Database\Seeders;

use App\Models\AboutOurStarup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AboutOurStartup extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!AboutOurStarup::first()) {
            DB::table('about_our_starups')->insert([
                'event_images' => null,
                'description' => 'Lorem ipsum, dolor sit amet consectetur adipisicing elit. Harum sapiente expedita maiores aut rerum? Similique quam nobis incidunt aut deserunt, eligendi labore possimus exercitationem commodi corporis? Possimus laborum ipsa assumenda.'
            ]);
        }
    }
}
