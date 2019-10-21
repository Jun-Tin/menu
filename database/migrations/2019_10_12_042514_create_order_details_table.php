<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_order')->nullable()->commit('订单编号');
            $table->integer('menu_id')->nullable()->commit('菜品id');
            $table->string('menus_id')->nullable()->commit('菜品组id');
            $table->string('tags_id')->nullable()->commit('标签组id');
            $table->string('fill_price')->nullable()->commit('补差组');
            $table->integer('number')->nullable()->commit('数量');
            $table->integer('price')->nullable()->commit('价格');
            $table->integer('status')->nullable()->default(0)->commit('菜品状态');
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
        Schema::dropIfExists('order_details');
    }
}
