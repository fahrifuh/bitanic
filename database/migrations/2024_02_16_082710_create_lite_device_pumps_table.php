<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLiteDevicePumpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lite_device_pumps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lite_device_id')->constrained('lite_devices')->cascadeOnDelete();
            $table->unsignedBigInteger('number');
            $table->unsignedTinyInteger('is_active')->default(1);
            $table->double('min_tds')->nullable();
            $table->double('max_tds')->nullable();
            $table->double('min_ph')->nullable();
            $table->double('max_ph')->nullable();
            $table->double('current_tds')->nullable();
            $table->double('current_ph')->nullable();
            $table->unsignedTinyInteger('status')->default(0);
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
        Schema::dropIfExists('lite_device_pumps');
    }
}
