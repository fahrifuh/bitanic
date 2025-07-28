<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubdisIdInFarmerGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('farmer_groups', function (Blueprint $table) {
            $table->bigInteger('subdis_id')->unsigned()->nullable()->after('picture');

            $table->foreign('subdis_id')
                  ->references('id')
                  ->on('subdistricts')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('farmer_groups', function (Blueprint $table) {
            $table->dropForeign('subdis_id');
            $table->dropColumn('subdis_id');
        });
    }
}
