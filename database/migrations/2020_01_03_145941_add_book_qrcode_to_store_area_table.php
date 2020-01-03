<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBookQrcodeToStoreAreaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_area', function (Blueprint $table) {
            $table->string('book_qrcode')->nullable()->after('line_qrcode')->commit('预约二维码');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('store_area', function (Blueprint $table) {
            $table->dropColumn('book_qrcode');
        });
    }
}
