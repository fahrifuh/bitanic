<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHydroponicDeviceTelemetriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hydroponic_device_telemetries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hydroponic_device_id')->constrained('hydroponic_devices')->cascadeOnDelete();
            $table->json('sensors');
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
        Schema::dropIfExists('hydroponic_device_telemetries');
    }
}
