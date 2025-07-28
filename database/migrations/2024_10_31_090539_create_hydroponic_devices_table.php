<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHydroponicDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hydroponic_devices', function (Blueprint $table) {
            $table->id();
            $table->string('series')->unique();
            $table->string('version');
            $table->foreignId('user_id')->nullable()->constrained('hydroponic_users')->nullOnDelete();
            $table->date('activation_date')->nullable();
            $table->date('production_date');
            $table->date('purchase_date')->nullable();
            $table->string('picture')->nullable();
            $table->unsignedTinyInteger('is_auto')->default(0);
            $table->json('thresholds');
            $table->json('pumps');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hydroponic_devices');
    }
}
