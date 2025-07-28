<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFarmerTransactionItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('farmer_transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmer_transaction_shop_id')->constrained('farmer_transaction_shops')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('product_name');
            $table->unsignedBigInteger('product_price');
            $table->unsignedDouble('product_weight');
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('total');
            $table->unsignedDouble('discount')->nullable();
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
        Schema::dropIfExists('farmer_transaction_items');
    }
}
