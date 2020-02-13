<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order')->unique()->nullable()->comment('订单编号');
            $table->integer('place_id')->nullable()->comment('座位id');
            $table->string('price')->nullable()->comment('订单价格');
            $table->integer('number')->nullable()->comment('订单总商品');
            $table->integer('status')->nullable()->default(0)->comment('订单状态');
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
        Schema::dropIfExists('orders');
    }
}
