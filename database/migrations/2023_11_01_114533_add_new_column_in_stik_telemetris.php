<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnInStikTelemetris extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stik_telemetris', function (Blueprint $table) {
            $table->string('id_perangkat')->nullable()->after('id');
            $table->string('id_pengukuran')->nullable()->after('id');
            $table->float('temperature')->nullable()->after('id');
            $table->float('moisture')->nullable()->after('id');
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
            $table->dropColumn(['id_perangkat', 'id_pengukuran', 'temperature', 'moisture']);
        });
    }
}
