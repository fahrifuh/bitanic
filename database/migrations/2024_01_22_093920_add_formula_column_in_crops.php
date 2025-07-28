<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFormulaColumnInCrops extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crops', function (Blueprint $table) {
            $table->double('target_ph');
            $table->double('target_persen_corganik');
            $table->integer('frekuensi_siram');
            $table->double('n_kg_ha');
            $table->double('sangat_rendah_p2o5');
            $table->double('rendah_p2o5');
            $table->double('sedang_p2o5');
            $table->double('tinggi_p2o5');
            $table->double('sangat_tinggi_p2o5');
            $table->double('sangat_rendah_k2o');
            $table->double('rendah_k2o');
            $table->double('sedang_k2o');
            $table->double('tinggi_k2o');
            $table->double('sangat_tinggi_k2o');
            $table->text('catatan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('crops', function (Blueprint $table) {
            $table->dropColumn([
                'target_ph',
                'target_persen_corganik',
                'frekuensi_siram',
                'n_kg_ha',
                'sangat_rendah_p2o5',
                'rendah_p2o5',
                'sedang_p2o5',
                'tinggi_p2o5',
                'sangat_tinggi_p2o5',
                'sangat_rendah_k2o',
                'rendah_k2o',
                'sedang_k2o',
                'tinggi_k2o',
                'sangat_tinggi_k2o',
                'catatan',
            ]);
        });
    }
}
