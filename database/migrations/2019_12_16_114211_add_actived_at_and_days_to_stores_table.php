<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddActivedAtAndDaysToStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->timestamp('actived_at')->nullable()->after('active')->comment('上线日期');
            $table->integer('days')->nullable()->after('active')->default(0)->comment('上线天数');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('actived_at');
            $table->dropColumn('days');
        });
    }
}
