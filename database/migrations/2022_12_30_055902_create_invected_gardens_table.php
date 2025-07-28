<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvectedGardensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invected_gardens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('garden_id');
            $table->unsignedBigInteger('pest_id')->nullable()->default(null);
            $table->string('pest_name', 255)->nullable();
            $table->date('invected_date');
            $table->string('status', 255);
            $table->string('picture')->nullable();

            $table->timestamps();

            $table->foreign('garden_id')
                ->references('id')
                ->on('gardens')
                ->onDelete('cascade');

            $table->foreign('pest_id')
                ->references('id')
                ->on('pests')
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
        Schema::dropIfExists('invected_gardens');
    }
}
