<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumn2InStikTelemetris extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stik_telemetris', function (Blueprint $table) {
            $table->double('co2')->nullable()->after('rh');
            $table->double('n2o')->nullable()->after('co2');
            $table->double('no2')->nullable()->after('n2o');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stik_telemetris', function (Blueprint $table) {
            $table->dropColumn(['co2', 'n2o', 'no2']);
        });
    }
}
