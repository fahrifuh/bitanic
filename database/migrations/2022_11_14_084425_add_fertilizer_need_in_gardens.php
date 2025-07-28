<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFertilizerNeedInGardens extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gardens', function (Blueprint $table) {
            $table->float('temperature', 8, 2)->nullable();
            $table->float('moisture', 8, 2)->nullable();
            $table->float('nitrogen', 8, 2)->nullable();
            $table->float('phosphor', 8, 2)->nullable();
            $table->float('kalium', 8, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gardens', function (Blueprint $table) {
            $table->dropColumn(['temperature','moisture','nitrogen','phosphor','kalium']);
        });
    }
}
