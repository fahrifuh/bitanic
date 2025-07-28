<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumn2InGardens extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gardens', function (Blueprint $table) {
            $table->json('polygon')->nullable();
            $table->unsignedFloat('area')->nullable();
            $table->string('color', 6)->default('0400ff');
            $table->string('name', 200)->nullable();
            $table->string('picture')->nullable();
            $table->unsignedTinyInteger('is_indoor')->default(0);
            $table->foreignId('device_id')->nullable()->constrained('devices')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gardens', function (Blueprint $table) {
            $table->dropColumn([
                'polygon',
                'area',
                'color',
                'name',
                'picture',
                'is_indoor',
                'device_id',
            ]);
        });
    }
}
