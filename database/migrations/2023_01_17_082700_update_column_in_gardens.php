<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnInGardens extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gardens', function (Blueprint $table) {
            $table->unsignedInteger('number_hydroponics')->nullable();
            $table->unsignedInteger('levels')->nullable(); // for hydroponics and aquaponics
            $table->unsignedInteger('holes')->nullable(); // for hydroponics and aquaponics
            $table->float('length')->nullable(); // for aquaponics
            $table->float('width')->nullable(); // for aquaponics
            $table->float('height')->nullable(); // for aquaponics
            $table->string('fish_type')->nullable(); // for aquaponics
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gardens', function (Blueprint $table) {
            $table->dropColumn(['number_hydroponics', 'levels', 'holes', 'length', 'width', 'fish_type', 'height']);
        });
    }
}
