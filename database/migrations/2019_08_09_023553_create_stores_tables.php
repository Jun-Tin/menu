<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoresTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable()->commit('用户id');
            $table->string('name')->nullable()->commit('店铺名称');
            $table->string('address')->nullable()->commit('店铺地址');
            $table->string('image_id')->nullable()->commit('店铺图片id');
            $table->string('phone')->nullable()->commit('店铺电话');
            $table->string('start_time')->nullable()->commit('开始营业时间');
            $table->string('end_time')->nullable()->commit('结束营业时间');
            $table->string('intro')->nullable()->commit('店铺简介');
            $table->string('set_time')->nullable()->commit('限制时间');
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
        Schema::dropIfExists('stores');
    }
}
