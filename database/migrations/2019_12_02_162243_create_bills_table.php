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
            $table->string('title')->nullable()->commit('标题');
            $table->string('order')->nullable()->commit('订单编号');
            $table->string('operate')->nullable()->commit('操作方');
            $table->string('accept')->nullable()->commit('接收方');
            $table->integer('execute')->nullable()->commit('执行动作；0：减少，1：增加');
            $table->integer('type')->nullable()->commit('分类：0：系统，1：用户');
            $table->integer('number')->nullable()->commit('金币数量');
            $table->integer('method')->nullable()->commit('支付方式');
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
