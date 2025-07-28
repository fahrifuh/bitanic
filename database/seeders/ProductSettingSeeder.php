<?php

namespace Database\Seeders;

use App\Models\ProductSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!ProductSetting::first()) {
            DB::table('product_settings')->insert([
                'title' => 'Bitanic Node Farm',
                'sub' => 'It\'s powerfull inovation',
                'description' => 'Lorem ipsum dolor sit amet consectetur, adipisicing elit. Accusamus quasi fugiat officiis atque vero aperiam beatae quis quidem quisquam, asperiores temporibus porro quo hic nihil facere enim ad doloribus iste. Lorem ipsum dolor sit amet consectetur adipisicing elit. Odit ullam, unde soluta ipsum atque incidunt earum? Iusto, consectetur veniam veritatis neque tempora harum minus consequuntur ullam aspernatur porro. Quo, eius.',
                'created_at' => now('Asia/Jakarta')
            ]);
        }
    }
}
