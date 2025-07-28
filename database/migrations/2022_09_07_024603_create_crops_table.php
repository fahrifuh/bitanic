<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCropsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crops', function (Blueprint $table) {
            $table->id();
            $table->string('crop_name');
            $table->enum('type', ['sayur', 'buah']);
            $table->string('season');
            $table->float('optimum_temperature', 8, 2);
            $table->float('minimum_temperature', 8, 2);
            $table->float('maximum_temperature', 8, 2);
            $table->float('altitude', 8, 2);
            $table->string('picture')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('crops');
    }
}
