<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBehaviorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('behaviors', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable()->commit('用户id');
            $table->integer('target_id')->nullable()->commit('目标id');
            $table->enum('category', ['book','order','serving','clean','settle','retreat','cooking','backout'])->commit('工作分类');
            $table->integer('status')->nullable()->default(0)->commit('完成情况;0：未完成,1:完成');
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
        Schema::dropIfExists('behaviors');
    }
}
