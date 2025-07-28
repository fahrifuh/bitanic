<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActiveGardensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('active_gardens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('garden_id');
            $table->date('active_date');
            $table->date('finished_date')->nullable();
            $table->timestamps();

            $table->foreign('garden_id')
                ->references('id')
                ->on('gardens')
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
        Schema::dropIfExists('active_gardens');
    }
}
