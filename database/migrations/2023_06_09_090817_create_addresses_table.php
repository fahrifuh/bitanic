<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('recipient_name', 100);
            $table->string('recipient_phone_number');
            $table->text('address');
            $table->integer('postal_code', false, true);
            $table->decimal('latitude', 20, 15);
            $table->decimal('longitude', 20, 15);
            $table->text('detail')->nullable();
            $table->unsignedBigInteger('province_id')->nullable()->after('user_id');
            $table->unsignedBigInteger('city_id')->nullable()->after('province_id');
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
        Schema::dropIfExists('addresses');
    }
}
