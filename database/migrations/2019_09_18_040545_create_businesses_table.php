<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusinessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('store_id')->nullable()->commit('门店id');
            $table->integer('category')->nullable()->commit('分类；早市：1， 午市：2， 晚市：3');
            $table->integer('start_time')->nullable()->commit('开始时间段');
            $table->integer('end_time')->nullable()->commit('结束时间段');
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
        Schema::dropIfExists('businesses');
    }
}
