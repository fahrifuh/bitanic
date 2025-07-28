<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('device_name')->nullable();
            $table->string('device_series')->unique();
            $table->string('version');
            $table->date('production_date');
            $table->date('purchase_date');
            $table->date('activate_date')->nullable();
            $table->string('status');
            $table->string('picture')->nullable();
            $table->bigInteger('garden_id')->unsigned()->nullable();
            $table->bigInteger('farmer_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('garden_id')
                  ->references('id')
                  ->on('gardens')
                  ->onDelete('set null');
            $table->foreign('farmer_id')
                  ->references('id')
                  ->on('farmers')
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
        Schema::dropIfExists('devices');
    }
}
