<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackageMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_menu', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('package_id')->nullable()->commit('套餐id');
            $table->integer('menu_id')->nullable()->commit('菜品id');
            $table->integer('fill_price')->nullable()->commit('补差价');
            $table->softDeletes();
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
        Schema::dropIfExists('package_menu');
    }
}
