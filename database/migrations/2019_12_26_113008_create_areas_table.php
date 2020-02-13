<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('areas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('store_id')->nullable()->comment('门店id');
            $table->string('name')->nullable()->comment('名称');
            $table->integer('section_left')->nullable()->comment('左区间');
            $table->integer('section_right')->nullable()->comment('右区间');
            $table->integer('section_number')->nullable()->comment('区间数');
            $table->string('sign')->nullable()->comment('标识');
            $table->string('show')->nullable()->comment('显示');
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
        Schema::dropIfExists('areas');
    }
}
