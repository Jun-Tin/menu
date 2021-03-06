<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePeriodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('periods', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable()->comment('周期标题');
            $table->integer('number')->nullable()->default(0)->comment('金币数目');
            $table->integer('days')->nullable()->default(0)->comment('周期数目');
            $table->integer('show')->nullable()->default(0)->comment('是否显示');
            $table->integer('order_number')->nullable()->default(0)->comment('排序号');
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
        Schema::dropIfExists('periods');
    }
}
