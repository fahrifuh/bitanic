<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCropForSaleIdInProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('crop_for_sale_id')->nullable()->after('shop_id')->constrained('crop_for_sales')->cascadeOnDelete();
            $table->unsignedDouble('weight')->nullable()->comment('gram')->after('size_metric');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign('products_crop_for_sale_id_foreign');
            $table->dropColumn(['crop_for_sale_id', 'weight']);
        });
    }
}
