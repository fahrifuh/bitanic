<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWaterTempInLiteDevices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lite_devices', function (Blueprint $table) {
            $table->double('water_temperature')->nullable()->after('temperature');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lite_devices', function (Blueprint $table) {
            $table->dropColumn('water_temperature');
        });
    }
}
