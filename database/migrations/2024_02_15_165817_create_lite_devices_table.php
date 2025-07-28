<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLiteDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lite_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lite_user_id')->nullable()->constrained('lite_users')->nullOnDelete();
            $table->unsignedBigInteger('number')->nullable();
            $table->foreignId('lite_series_id')->constrained('lite_series')->cascadeOnDelete();
            $table->string('full_series')->nullable();
            $table->string('version');
            $table->date('production_date');
            $table->date('purchase_date');
            $table->date('activate_date')->nullable();
            $table->unsignedTinyInteger('status')->default(0);
            $table->string('image')->nullable();
            $table->double('temperature')->nullable();
            $table->double('humidity')->nullable();
            $table->double('min_tds')->nullable();
            $table->double('max_tds')->nullable();
            $table->double('min_ph')->nullable();
            $table->double('max_ph')->nullable();
            $table->double('current_tds')->nullable();
            $table->double('current_ph')->nullable();
            $table->timestamp('last_updated_telemetri')->nullable();
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
        Schema::dropIfExists('lite_devices');
    }
}
