<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNameEnAndNameHkToTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('types', function (Blueprint $table) {
            $table->string('name_en')->nullable()->after('name_cn')->comment('英文名称');
            $table->string('name_hk')->nullable()->after('name_cn')->comment('中文名称繁体');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('types', function (Blueprint $table) {
            $table->dropColumn('name_en');
            $table->dropColumn('name_hk');
        });
    }
}
