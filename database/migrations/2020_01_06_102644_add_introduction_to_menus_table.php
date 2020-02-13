<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIntroductionToMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->string('introduction')->nullable()->after('name')->comment('菜品介绍');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('introduction');
        });
    }
}
