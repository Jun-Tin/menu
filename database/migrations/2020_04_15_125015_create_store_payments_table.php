<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStorePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('store_id')->nullable()->comment('门店id');
            $table->integer('payment_id')->nullable()->comment('支付类型id');
            $table->string('client_id')->nullable()->comment('应用id');
            $table->string('client_secret')->nullable()->comment('应用密钥');
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
        Schema::dropIfExists('store_payments');
    }
}
