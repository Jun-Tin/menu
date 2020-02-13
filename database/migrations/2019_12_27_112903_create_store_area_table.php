<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreAreaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_area', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('store_id')->nullable()->comment('门店id');
            $table->string('screen_link')->nullable()->comment('大屏幕连接');
            $table->string('screen_qrcode')->nullable()->comment('大屏幕二维码');
            $table->string('line_qrcode')->nullable()->comment('排队二维码');
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
        Schema::dropIfExists('store_area');
    }
}
