<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFarmerTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('farmer_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('midtrans_token');
            $table->string('status');
            $table->string('bank_name')->nullable();
            $table->string('bank_code');
            $table->unsignedBigInteger('subtotal');
            $table->unsignedDouble('discount')->nullable();
            $table->unsignedBigInteger('platform_fees');
            $table->unsignedBigInteger('total');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_name');
            $table->foreignId('address_id')->nullable()->constrained('addresses')->nullOnDelete();
            $table->string('user_recipient_name', 100);
            $table->string('user_recipient_phone_number');
            $table->unsignedBigInteger('user_recipient_province_id')->nullable();
            $table->unsignedBigInteger('user_recipient_city_id')->nullable();
            $table->text('user_recipient_address');
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
        Schema::dropIfExists('farmer_transactions');
    }
}
