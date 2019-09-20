<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('places', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('store_id')->nullable()->index()->commit('门店id');
            $table->integer('pid')->nullable()->commit('父级id');
            $table->string('name')->nullable()->commit('座位名称');
            $table->integer('number')->nullable()->commit('座位人数');
            $table->integer('floor')->nullable()->commit('楼层');
            $table->integer('image_id')->nullable()->commit('图片id');
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
        Schema::dropIfExists('places');
    }
}
