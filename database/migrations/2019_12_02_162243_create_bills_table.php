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
            $table->string('title')->nullable()->comment('标题');
            $table->string('order')->nullable()->unique()->comment('订单编号');
            $table->string('operate')->nullable()->comment('操作方');
            $table->string('accept')->nullable()->comment('接收方');
            $table->integer('execute')->nullable()->comment('执行动作；0：减少，1：增加');
            $table->integer('type')->nullable()->comment('执行对象：0：系统，1：用户');
            $table->integer('number')->nullable()->comment('金币数量');
            $table->integer('method')->nullable()->comment('支付方式');
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
