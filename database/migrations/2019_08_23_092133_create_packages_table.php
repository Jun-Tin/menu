<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('store_id')->nullable()->index()->commit('门店id');
            $table->string('name')->nullable()->commit('套餐名称');
            $table->integer('image_id')->nullable()->commit('套餐图片');
            $table->integer('original_price')->nullable()->commit('套餐原始价格');
            $table->integer('special_price')->nullable()->commit('套餐特别价格');
            $table->integer('level')->nullable()->commit('推荐指数');
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
        Schema::dropIfExists('packages');
    }
}
