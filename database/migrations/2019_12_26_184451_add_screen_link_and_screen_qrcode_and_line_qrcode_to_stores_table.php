<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddScreenLinkAndScreenQrcodeAndLineQrcodeToStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->string('screen_link')->nullable()->after('actived_at')->commit('大屏幕连接');
            $table->string('screen_qrcode')->nullable()->after('actived_at')->commit('大屏幕二维码');
            $table->string('line_qrcode')->nullable()->after('actived_at')->commit('排队二维码');
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
            $table->dropColumn('screen_link');
            $table->dropColumn('screen_qrcode');
            $table->dropColumn('line_qrcode');
        });
    }
}
