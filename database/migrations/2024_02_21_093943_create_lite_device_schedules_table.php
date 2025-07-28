<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLiteDeviceSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lite_device_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lite_device_id')->constrained('lite_devices')->cascadeOnDelete();
            $table->foreignId('crop_id')->nullable()->constrained('crops')->nullOnDelete();
            $table->string('crop_name')->nullable();
            $table->unsignedTinyInteger('is_finished')->default(0);
            $table->dateTime('end_datetime')->nullable();
            $table->unsignedInteger('weeks');
            $table->json('days');
            $table->json('setontimes');
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
        Schema::dropIfExists('lite_device_schedules');
    }
}
