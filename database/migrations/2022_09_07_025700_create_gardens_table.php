<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGardensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gardens', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('owner')->nullable();
            $table->text('address')->nullable();
            $table->string('gardes_type');
            $table->double('area', 11, 2);
            $table->string('unit');
            $table->decimal('lat', 8, 6)->comment('latitude');
            $table->decimal('lng', 9, 6)->comment('longitude');
            $table->bigInteger('alt')->comment('altitude');
            $table->string('picture')->nullable();
            $table->date('date_created');
            $table->date('harvest_date')->nullable();
            $table->date('estimated_harvest');
            $table->string('harvest_status');
            $table->json('polygon');
            $table->unsignedBigInteger('farmer_id');
            $table->unsignedBigInteger('crop_id')->nullable()->default(null);
            $table->unsignedBigInteger('pest_id')->nullable()->default(null);
            $table->timestamps();

            $table->foreign('farmer_id')
                  ->references('id')
                  ->on('farmers')
                  ->onDelete('cascade');
            $table->foreign('crop_id')
                  ->references('id')
                  ->on('crops')
                  ->onDelete('set null');
            $table->foreign('pest_id')
                ->references('id')
                ->on('pests')
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
        Schema::dropIfExists('gardens');
    }
}
