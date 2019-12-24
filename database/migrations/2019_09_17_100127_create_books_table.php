<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('store_id')->nullable()->index()->commit('门店id');
            $table->integer('place_id')->nullable()->commit('座位id');
            $table->string('name')->nullable()->commit('预约姓名');
            $table->integer('area_code')->nullable()->commit('区号');
            $table->string('phone')->nullable()->commit('预约电话');
            $table->integer('gender')->nullable()->commit('预约性别');
            $table->integer('date')->nullable()->commit('预约日期');
            $table->string('meal_time')->nullable()->commit('就餐时刻');
            $table->integer('meal_number')->nullable()->commit('就餐人数');
            $table->integer('type')->nullable()->commit('时刻段；1：早市，2：午市，3：晚市');
            $table->integer('lock_in')->nullable()->commit('锁定开始时间');
            $table->integer('lock_out')->nullable()->commit('锁定结束时间');
            $table->integer('status')->nullable()->default(0)->commit('使用情况；0：未开始，1：正在使用，2：已结束，3：取消、没到');
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
        Schema::dropIfExists('books');
    }
}
