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
            $table->integer('store_id')->nullable()->comment('门店id');
            $table->integer('area_id')->nullable()->comment('区域id');
            $table->string('code')->nullable()->comment('编号');
            $table->integer('number')->nullable()->comment('人数');
            $table->string('name')->nullable()->comment('姓名');
            $table->string('phone')->nullable()->comment('手机号');
            $table->integer('status')->nullable()->default(0)->comment('状态');
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
