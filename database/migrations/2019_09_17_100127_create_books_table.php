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
            $table->string('name')->nullable()->commit('预约姓名');
            $table->integer('phone')->nullable()->commit('预约电话');
            $table->integer('gender')->nullable()->commit('预约性别');
            $table->integer('date')->nullable()->commit('预约日期');
            $table->integer('meal_time')->nullable()->commit('就餐时间');
            $table->integer('meal_number')->nullable()->commit('就餐人数');
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
