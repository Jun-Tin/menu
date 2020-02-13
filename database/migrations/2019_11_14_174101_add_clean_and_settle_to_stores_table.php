<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCleanAndSettleToStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->integer('settle')->nullable()->after('set_time')->default(0)->comment('支付规则');
            $table->integer('clean')->nullable()->after('set_time')->default(0)->comment('打扫规则');
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
            $table->dropColumn('settle');
            $table->dropColumn('clean');
        });
    }
}
