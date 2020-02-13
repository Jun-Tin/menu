<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNameToMenuTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('menu_tag', function (Blueprint $table) {
            $table->string('name')->nullable()->after('target_id')->comment('标签名称');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('menu_tag', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }
}
