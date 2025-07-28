<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraInFertilizationSchedules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fertilization_schedules', function (Blueprint $table) {
            $table->json('extras')->nullable()->after('end_time');
            $table->unsignedTinyInteger('motor_status')->nullable()->after('extras');
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
            $table->dropColumn(['extras', 'motor_status']);
        });
    }
}
