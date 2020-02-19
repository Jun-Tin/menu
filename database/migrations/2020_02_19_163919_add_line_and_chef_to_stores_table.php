<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLineAndChefToStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->integer('line')->nullable()->default(1)->after('language_id')->comment('是否打开自动打印排队：0：否，1：是');
            $table->integer('chef')->nullable()->default(1)->after('language_id')->comment('是否打开自动打印后厨：0：否，1：是');
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
            $table->dropColumn('line');
            $table->dropColumn('chef');
        });
    }
}
