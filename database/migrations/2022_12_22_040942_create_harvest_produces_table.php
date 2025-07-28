<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHarvestProducesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('harvest_produces', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('crop_id');
            $table->unsignedBigInteger('garden_id');
            $table->double('value', 11, 2)->unsigned();
            $table->string('unit', 100);
            $table->date('date');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('crop_id')
                  ->references('id')
                  ->on('crops')
                  ->onDelete('cascade');

            $table->foreign('garden_id')
                ->references('id')
                ->on('gardens')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('harvest_produces');
    }
}
