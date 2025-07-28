<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRscGardenTelemetriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rsc_garden_telemetries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rsc_garden_id')->constrained('rsc_gardens')->cascadeOnDelete();
            $table->decimal('latitude', 20, 15);
            $table->decimal('longitude', 20, 15);
            $table->json('samples');
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
        Schema::dropIfExists('rsc_garden_telemetries');
    }
}
