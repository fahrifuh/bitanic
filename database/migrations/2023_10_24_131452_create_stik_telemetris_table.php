<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStikTelemetrisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stik_telemetris', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('latitude', 20, 15)->nullable();
            $table->decimal('longitude', 20, 15)->nullable();
            $table->json('polygon')->nullable();
            $table->float('area')->nullable();
            $table->float('n')->nullable();
            $table->float('p')->nullable();
            $table->float('k')->nullable();
            $table->float('t')->nullable();
            $table->float('rh')->nullable();
            $table->string('type')->comment('luas || npk');
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
        Schema::dropIfExists('stik_telemetris');
    }
}
