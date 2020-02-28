<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCurrenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name_cn')->nullable()->comment('货币—简体');
            $table->string('name_hk')->nullable()->comment('货币—繁体');
            $table->string('name_en')->nullable()->comment('货币—英语');
            $table->string('unit')->nullable()->comment('货币名称');
            $table->string('code')->nullable()->comment('国际标准代码');
            $table->tinyInteger('show')->nullable()->default(1)->comment('是否显示：【0：不显示，1：显示】');
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
        Schema::dropIfExists('currencies');
    }
}
