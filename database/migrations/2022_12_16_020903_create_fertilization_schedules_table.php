<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFertilizationSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fertilization_schedules', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('fertilization_id')->unsigned()->nullable();
            $table->bigInteger('farmer_id')->unsigned()->nullable();
            $table->bigInteger('device_id')->unsigned()->nullable();
            $table->bigInteger('garden_id')->unsigned()->nullable();
            $table->enum('type', ['schedule', 'manual_motor_1', 'manual_motor_2'])->nullable();
            $table->unsignedInteger('week');
            $table->unsignedInteger('day');
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->timestamps();

            $table->foreign('device_id')
                  ->references('id')
                  ->on('devices')
                  ->onDelete('set null');

            $table->foreign('farmer_id')
                  ->references('id')
                  ->on('farmers')
                  ->onDelete('set null');

            $table->foreign('garden_id')
                  ->references('id')
                  ->on('gardens')
                  ->onDelete('set null');

            $table->foreign('fertilization_id')
                  ->references('id')
                  ->on('fertilizations')
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
        Schema::dropIfExists('fertilization_schedules');
    }
}
