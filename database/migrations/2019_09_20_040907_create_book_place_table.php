<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookPlaceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('book_place', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('book_id')->nullable()->commit('预约id');
            $table->integer('place_id')->nullable()->commit('座位id');
            $table->string('date')->nullable()->commit('日期');
            $table->integer('time')->nullable()->commit('时刻');
            $table->integer('type')->nullable()->commit('时刻段；1：早市，2：午市，3：晚市');
            $table->integer('meal_number')->nullable()->commit('就餐人数');
            $table->integer('status')->nullable()->commit('使用情况；0：未开始，1：正在使用，2：已结束，3：取消、没到');
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
        Schema::dropIfExists('book_place');
    }
}
