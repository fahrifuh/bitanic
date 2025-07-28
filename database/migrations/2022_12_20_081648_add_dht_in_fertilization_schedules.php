<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDhtInFertilizationSchedules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fertilization_schedules', function (Blueprint $table) {
            $table->float('DHT1Temp', 11)->nullable()->default(null)->after('end_time');
            $table->float('DHT2Temp', 11)->nullable()->default(null)->after('end_time');
            $table->float('DHT1Hum', 11)->nullable()->default(null)->after('end_time');
            $table->float('DHT2Hum', 11)->nullable()->default(null)->after('end_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fertilization_schedules', function (Blueprint $table) {
            $table->dropColumn([
                'DHT1Temp',
                'DHT2Temp',
                'DHT1Hum',
                'DHT2Hum'
            ]);
        });
    }
}
