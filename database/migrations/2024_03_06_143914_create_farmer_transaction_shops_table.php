<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFarmerTransactionShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('farmer_transaction_shops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmer_transaction_id')->constrained('farmer_transactions')->cascadeOnDelete();
            $table->foreignId('shop_id')->nullable()->constrained('shops')->nullOnDelete();
            $table->string('shop_name');
            $table->unsignedBigInteger('subtotal');
            $table->unsignedDouble('discount')->nullable();
            $table->unsignedBigInteger('total');
            $table->unsignedBigInteger('province_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->decimal('latitude', 20, 15)->nullable();
            $table->decimal('longitude', 20, 15)->nullable();
            $table->string('courier')->nullable();
            $table->string('type')->nullable();
            $table->unsignedBigInteger('subtotal_shipping')->nullable();
            $table->unsignedBigInteger('discount_shipping')->nullable();
            $table->unsignedBigInteger('total_shipping');
            $table->unsignedTinyInteger('shipping_status')->default(0);
            $table->string('delivery_receipt')->nullable();
            $table->string('message')->nullable();
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
        Schema::dropIfExists('farmer_transaction_shops');
    }
}
