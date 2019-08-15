<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAreacodesTabel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('areacodes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('acname_en')->nullable()->commit('英文名称');
            $table->string('acname_cn')->nullable()->commit('中文名称');
            $table->string('codename')->nullable()->commit('缩写');
            $table->integer('acnumber')->nullable()->commit('区号');
            $table->integer('order_number')->nullable()->commit('序列号');
            $table->integer('show')->nullable()->default(1)->commit('是否显示：0、不显示；1、显示');            
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
        Schema::dropIfExists('areacodes');
    }
}
