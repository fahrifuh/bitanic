<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTorenColumnInDevices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->json('toren_pemupukan')->nullable()->after('farmer_id');
            $table->json('toren_penyiraman')->nullable()->after('toren_pemupukan');
            $table->unsignedInteger('delay')->default(5)->after('farmer_id');
            $table->tinyInteger('status_penyiraman')->default(0)->after('toren_penyiraman');
            $table->tinyInteger('status_pemupukan')->default(0)->after('status_penyiraman');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn(['toren_pemupukan', 'toren_penyiraman', 'status_penyiraman', 'status_pemupukan']);
        });
    }
}
