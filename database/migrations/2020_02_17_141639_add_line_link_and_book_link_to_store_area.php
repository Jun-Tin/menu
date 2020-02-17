<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLineLinkAndBookLinkToStoreArea extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_area', function (Blueprint $table) {
            $table->string('line_link')->nullable()->after('screen_qrcode')->comment('排队连接');
            $table->string('book_link')->nullable()->after('line_qrcode')->comment('预约链接');
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
            $table->dropColumn('line_link');
            $table->dropColumn('book_link');
        });
    }
}
