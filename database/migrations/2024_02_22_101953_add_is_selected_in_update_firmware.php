<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsSelectedInUpdateFirmware extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('update_firmware', function (Blueprint $table) {
            $table->unsignedTinyInteger('is_selected')->default(0)->after('version');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('update_firmware', function (Blueprint $table) {
            $table->dropColumn('is_selected');
        });
    }
}
