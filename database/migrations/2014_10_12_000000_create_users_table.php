<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->commit('用户名');
            $table->string('email')->nullable()->unique()->commit('邮箱');
            $table->integer('area_code')->nullable()->commit('区号');
            $table->string('phone')->nullable()->unique()->commit('手机号码');
            $table->string('password')->commit('加密密码');
            $table->string('pro_password')->nullable()->commit('明文密码');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
