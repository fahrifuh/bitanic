<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNecessityDifferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('necessity_differences', function (Blueprint $table) {
            $table->id();
            $table->decimal('selisih_ph', 11, 1);
            $table->decimal('kebutuhan_dolomit', 11, 1)->comment("(ton/ha)");
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
        Schema::dropIfExists('necessity_differences');
    }
}
