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
            $table->integer('user_id')->nullable()->comment('用户id');
            $table->string('name')->nullable()->comment('店铺名称');
            $table->string('address')->nullable()->comment('店铺地址');
            $table->string('image_id')->nullable()->comment('店铺图片id');
            $table->string('phone')->nullable()->comment('店铺电话');
            $table->string('start_time')->nullable()->comment('开始营业时间');
            $table->string('end_time')->nullable()->comment('结束营业时间');
            $table->string('intro')->nullable()->comment('店铺简介');
            $table->string('set_time')->nullable()->comment('限制时间');
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
