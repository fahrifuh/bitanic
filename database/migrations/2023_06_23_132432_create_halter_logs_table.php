<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHalterLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('halter_logs', function (Blueprint $table) {
            $table->id();
            $table->string('No_Device', 2)->nullable();
            $table->string('Header', 13)->nullable();
            $table->double('AccX', 10, 2)->nullable();
            $table->double('AccY', 10, 2)->nullable();
            $table->double('AccZ', 10, 2)->nullable();
            $table->double('GyroX', 10, 2)->nullable();
            $table->double('GyroY', 10, 2)->nullable();
            $table->double('GyroZ', 10, 2)->nullable();
            $table->float('Vbatt', 6, 2)->nullable();
            $table->mediumInteger('HR')->nullable();
            $table->mediumInteger('SPO2')->nullable();
            $table->float('Suhu', 4, 2)->nullable();
            $table->char('Tail', 1)->nullable();
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
        Schema::dropIfExists('halter_logs');
    }
}
