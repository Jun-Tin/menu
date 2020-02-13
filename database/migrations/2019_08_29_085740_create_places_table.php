<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('places', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('store_id')->nullable()->index()->comment('门店id');
            $table->string('name')->nullable()->comment('座位名称');
            $table->integer('number')->nullable()->comment('座位人数');
            $table->integer('floor')->nullable()->comment('楼层');
            $table->integer('image_id')->nullable()->comment('图片id');
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
        Schema::dropIfExists('places');
    }
}
