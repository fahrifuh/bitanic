<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnInGarders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gardens', function (Blueprint $table) {
            $table->dropForeign(['farmer_id']);
            $table->dropColumn(['name','owner','address','area','unit','lat','lng','alt','picture','polygon','farmer_id']);
            $table->foreignId('land_id')->nullable()->constrained('lands')->cascadeOnDelete();
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
            $table->dropForeign(['land_id']);
            $table->dropColumn(['land_id']);
        });
    }
}
