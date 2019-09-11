<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackageGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_group', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('package_id')->nullable()->commit('套餐id');
            $table->integer('pid')->nullable()->commit('父级id');
            $table->integer('target_id')->nullable()->commit('标签id');
            $table->integer('fill_price')->nullable()->commit('补差价');
            $table->integer('order_number')->nullable()->commit('排序号');
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
        Schema::dropIfExists('package_group');
    }
}
