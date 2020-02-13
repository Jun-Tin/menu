<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAfterStartAndAfterEndToStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->string('after_end')->nullable()->after('start_time')->comment('午休结束时间');
            $table->string('after_start')->nullable()->after('start_time')->comment('午休开始时间');
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
            $table->dropColumn('after_end');
            $table->dropColumn('after_start');
        });
    }
}
