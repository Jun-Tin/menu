<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIntervalToStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->integer('condition')->nullable()->default(1)->after('category')->comment('是否为空才能预约：0：否，1：是');
            $table->integer('interval')->nullable()->after('category')->comment('时间间隔');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('condition');
            $table->dropColumn('interval');
        });
    }
}
