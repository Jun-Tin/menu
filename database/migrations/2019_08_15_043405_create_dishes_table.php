<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDishesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dishes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('store_id')->nullable()->index()->commit('门店id');
            $table->string('name')->nullable()->commit('菜品名称');
            $table->integer('image_id')->nullable()->commit('菜品图片');
            $table->integer('original_price')->nullable()->commit('菜品原始价格');
            $table->integer('special_price')->nullable()->commit('菜品原始价格');
            $table->softDeletes();
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
        Schema::dropIfExists('dishes');
    }
}
