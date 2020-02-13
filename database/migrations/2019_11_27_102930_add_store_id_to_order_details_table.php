<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStoreIdToOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->integer('store_id')->nullable()->after('order_order')->comment('门店id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn('store_id');
        });
    }
}
