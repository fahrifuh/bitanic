<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFarmersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('farmers', function (Blueprint $table) {
            $table->id();
            $table->string('full_name')->nullable();
            $table->enum('gender', ['l', 'p']);
            $table->string('nik')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('type')->nullable();
            $table->text('address')->nullable();
            $table->bigInteger('count_gardens')->default(0);
            $table->string('picture')->nullable();
            $table->string('referral_code')->nullable();
            $table->bigInteger('user_id')->unsigned();
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
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
        Schema::dropIfExists('farmers');
    }
}
