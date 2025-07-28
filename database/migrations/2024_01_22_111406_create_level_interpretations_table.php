<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLevelInterpretationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('level_interpretations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interpretation_id')->constrained('interpretations')->cascadeOnDelete();
            $table->string('sangat_rendah')->nullable();
            $table->string('rendah');
            $table->string('sedang');
            $table->string('tinggi');
            $table->string('sangat_tinggi')->nullable();
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
        Schema::dropIfExists('level_interpretations');
    }
}
