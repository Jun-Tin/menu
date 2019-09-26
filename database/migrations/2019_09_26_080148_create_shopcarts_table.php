<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopcartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shopcarts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('place_id')->nullable()->commit('座位id');
            $table->integer('menu_id')->nullable()->commit('菜品id');
            $table->integer('number')->nullable()->commit('数量');
            $table->integer('price')->nullable()->commit('价格');
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
        Schema::dropIfExists('shopcarts');
    }
}
