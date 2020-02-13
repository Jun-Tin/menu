<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_tag', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('menu_id')->nullable()->comment('菜品id');
            $table->integer('target_id')->nullable()->comment('标签id');
            $table->integer('pid')->nullable()->comment('父级id');
            $table->integer('fill_price')->nullable()->comment('补差价');
            $table->integer('order_number')->nullable()->comment('排序号');
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
        Schema::dropIfExists('menu_tag');
    }
}
