<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBitanicProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bitanic_products', function (Blueprint $table) {
            $table->id();
            $table->string('picture');
            $table->string('name');
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('discount')->nullable();
            $table->unsignedBigInteger('weight')->nullable()->comment('gram');
            $table->text('description');
            $table->string('version');
            $table->integer('type');
            $table->string('category');
            $table->unsignedTinyInteger('is_listed')->default(0);
            $table->unsignedBigInteger('province_id')->nullable()->default(9);
            $table->unsignedBigInteger('city_id')->nullable()->default(22);
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
        Schema::dropIfExists('bitanic_products');
    }
}
