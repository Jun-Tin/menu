<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lines', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('store_id')->nullable()->commit('门店id');
            $table->integer('area_id')->nullable()->commit('区域id');
            $table->string('code')->nullable()->commt('编号');
            $table->integer('number')->nullable()->commit('人数');
            $table->string('name')->nullable()->commit('姓名');
            $table->string('phone')->nullable()->commit('手机号');
            $table->integer('status')->nullable()->default(0)->commit('状态');
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
        Schema::dropIfExists('lines');
    }
}
