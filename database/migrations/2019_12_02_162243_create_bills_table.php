<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable()->commit('标题');
            $table->string('order')->nullable()->commit('订单编号');
            $table->integer('user_id')->nullable()->commit('用户id');
            $table->integer('execute')->nullable()->commit('执行动作；0：减少，1：增加');
            $table->integer('number')->nullable()->commit('金币数量');
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
        Schema::dropIfExists('bills');
    }
}
