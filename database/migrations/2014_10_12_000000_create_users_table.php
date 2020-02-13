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
            $table->string('name')->comment('用户名');
            $table->string('email')->nullable()->unique()->comment('邮箱');
            $table->integer('area_code')->nullable()->comment('区号');
            $table->string('phone')->nullable()->comment('手机号码');
            $table->integer('image_id')->nullable()->comment('图片id');
            $table->string('password')->comment('加密密码');
            $table->string('pro_password')->nullable()->comment('明文密码');
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
