<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddValvesInFertilizations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fertilizations', function (Blueprint $table) {
            $table->json('valves')->nullable();
            $table->json('formula')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fertilizations', function (Blueprint $table) {
            $table->dropColumn(['valves', 'formula']);
        });
    }
}
