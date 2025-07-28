<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEndDatetimeInFertilizations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fertilizations', function (Blueprint $table) {
            $table->dateTime('end_datetime')->nullable()->after('set_minute');
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
            $table->dropColumn('end_datetime');
        });
    }
}
