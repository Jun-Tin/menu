<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('store_id')->nullable()->after('email')->commit('门店id');
            $table->integer('gender')->nullable()->after('coins')->comment('性别');
            $table->string('birthday')->nullable()->after('gender')->comment('生日');
            $table->string('post')->nullable()->after('birthday')->comment('岗位');
            $table->integer('entry_time')->nullable()->after('remember_token')->comment('入职时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('gender');
            $table->dropColumn('birthday');
            $table->dropColumn('post');
            $table->dropColumn('entry_time');
        });
    }
}
