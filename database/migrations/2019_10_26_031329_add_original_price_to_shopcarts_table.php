<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOriginalPriceToShopcartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shopcarts', function (Blueprint $table) {
            $table->string('original_price')->nullable()->after('price')->comment('套餐单品价格');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shopcarts', function (Blueprint $table) {
            $table->dropColumn('original_price');
        });
    }
}
