<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPerangkatIdInLands extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lands', function (Blueprint $table) {
            $table->foreignId('device_id')->nullable()->after('farmer_id')->constrained('devices')->nullOnDelete();
            $table->json('selenoid_watering')->nullable()->after('device_id');
            $table->unsignedInteger('selenoid_status')->nullable()->after('device_id');
            $table->unsignedInteger('selenoid_id')->nullable()->after('device_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lands', function (Blueprint $table) {
            $table->dropForeign('lands_device_id_foreign');
            $table->dropColumn([
                'device_id',
                'selenoid_id',
                'selenoid_status',
                'selenoid_watering',
            ]);
        });
    }
}
