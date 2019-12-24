<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('store_id')->nullable()->index()->commit('门店id');
            $table->string('name')->nullable()->commit('菜品名称');
            $table->integer('image_id')->nullable()->commit('菜品图片');
            $table->integer('original_price')->nullable()->commit('菜品原始价格');
            $table->integer('special_price')->nullable()->commit('菜品特别价格');
            $table->integer('level')->nullable()->commit('推荐指数');
            $table->enum('type', ['o', 's'])->commit('价格类型');
            $table->enum('category', ['m', 'p'])->commit('数据类型');
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
        Schema::dropIfExists('menus');
    }
}
