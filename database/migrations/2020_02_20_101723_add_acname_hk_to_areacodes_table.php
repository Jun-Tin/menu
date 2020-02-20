<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAcnameHkToAreacodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('areacodes', function (Blueprint $table) {
            $table->string('acname_hk')->nullable()->after('acname_cn')->comment('中文名称繁体');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('areacodes', function (Blueprint $table) {
            $table->dropColumn('acname_hk');
        });
    }
}
