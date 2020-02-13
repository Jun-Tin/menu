<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPlaceIdToOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->integer('place_id')->nullable()->after('menu_id')->default(0)->comment('座位id');
            $table->integer('pid')->nullable()->after('place_id')->default(0)->comment('父级id');
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
            $table->dropColumn('place_id');
        });
    }
}
