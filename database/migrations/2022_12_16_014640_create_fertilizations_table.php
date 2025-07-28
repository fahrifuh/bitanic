<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFertilizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fertilizations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('farmer_id')->unsigned()->nullable();
            $table->bigInteger('device_id')->unsigned()->nullable();
            $table->bigInteger('garden_id')->unsigned()->nullable();
            $table->string('crop_name')->nullable();
            $table->unsignedInteger('weeks');
            $table->tinyInteger('is_finished')->unsigned()->default(0);
            $table->string('set_day');
            $table->time('set_time');
            $table->unsignedInteger('set_minute');
            $table->timestamps();

            $table->foreign('device_id')
                  ->references('id')
                  ->on('devices')
                  ->onDelete('set null');

            $table->foreign('farmer_id')
                  ->references('id')
                  ->on('farmers')
                  ->onDelete('cascade');

            $table->foreign('garden_id')
                  ->references('id')
                  ->on('gardens')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fertilizations');
    }
}
