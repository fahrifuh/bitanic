<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelemetrisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telemetris', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('device_id');
            $table->unsignedBigInteger('garden_id');
            $table->unsignedBigInteger('farmer_id');
            $table->integer('soil1')->nullable();
            $table->integer('soil2')->nullable();
            $table->decimal('temperature', 16, 8);
            $table->decimal('humidity', 16, 8);
            $table->decimal('heatIndex', 16, 8);
            $table->dateTime('datetime');
            $table->timestamps();
            
            $table->foreign('device_id')
                ->references('id')
                ->on('devices')
                ->onDelete('cascade');
            $table->foreign('garden_id')
                ->references('id')
                ->on('gardens')
                ->onDelete('cascade');
            $table->foreign('farmer_id')
                ->references('id')
                ->on('farmers')
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
        Schema::dropIfExists('telemetris');
    }
}
