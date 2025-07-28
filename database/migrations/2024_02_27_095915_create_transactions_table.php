<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('midtrans_token');
            $table->string('status');
            $table->unsignedTinyInteger('shipping_status')->default(0);
            $table->string('bank_name')->nullable();
            $table->string('bank_code');
            $table->unsignedBigInteger('platform_fees');
            $table->unsignedBigInteger('discount')->nullable();
            $table->unsignedBigInteger('total');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('address_id')->nullable()->constrained('addresses')->nullOnDelete();
            $table->unsignedBigInteger('province_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->string('delivery_receipt')->nullable();
            $table->string('user_recipient_name', 100);
            $table->string('user_recipient_phone_number');
            $table->text('user_address');
            $table->string('courier')->nullable();
            $table->string('type')->nullable();
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
        Schema::dropIfExists('transactions');
    }
}
