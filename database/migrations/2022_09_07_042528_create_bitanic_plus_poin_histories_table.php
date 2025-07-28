<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBitanicPlusPoinHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bitanic_plus_poin_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('bitanic_plus_poin_id')->unsigned();
            $table->timestamp('date');
            $table->integer('poin')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('bitanic_plus_poin_id')
                  ->references('id')
                  ->on('bitanic_plus_poins')
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
        Schema::dropIfExists('bitanic_plus_poin_histories');
    }
}
