<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopcartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shopcarts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('place_id')->nullable()->comment('座位id');
            $table->integer('menu_id')->nullable()->comment('菜品id');
            $table->string('menus_id')->nullable()->comment('内容id');
            $table->string('tags_id')->nullable()->comment('标签组id');
            $table->string('fill_price')->nullable()->comment('差价集合');
            $table->integer('number')->nullable()->default(1)->comment('数量');
            $table->double('price', 8, 2)->nullable()->comment('总价格');
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
        Schema::dropIfExists('shopcarts');
    }
}
