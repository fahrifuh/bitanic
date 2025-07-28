<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnsInPests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pests', function (Blueprint $table) {
            $table->foreignId('crop_id')->nullable()->after('id')->constrained('crops')->cascadeOnDelete();
            $table->text('features')->nullable()->after('pest_type');
            $table->text('symptomatic')->nullable()->after('features');
            $table->text('precautions')->nullable()->after('symptomatic');
            $table->text('countermeasures')->nullable()->after('precautions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pests', function (Blueprint $table) {
            //
        });
    }
}
