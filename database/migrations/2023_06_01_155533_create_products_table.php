<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id');
            $table->string('name', 200);
            $table->string('category', 100);
            $table->json('picture');
            $table->text('description')->nullable();
            $table->bigInteger('price')->unsigned()->default(0);
            $table->integer('stock')->unsigned()->default(0);
            $table->string('stock_metric');
            $table->integer('size')->unsigned()->default(0);
            $table->string('size_metric');
            $table->timestamps();

            $table->foreign('shop_id')
                ->references('id')
                ->on('shops')
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
        Schema::dropIfExists('products');
    }
}
