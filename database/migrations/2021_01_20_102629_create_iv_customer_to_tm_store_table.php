<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvCustomerToTmStoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('iv_customer', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->nullable()->after('pic_phone');
            $table->foreign('store_id')->references('id')->on('tm_store');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('iv_customer', function (Blueprint $table) {
            $table->dropForeign('iv_customer_store_id_foreign');
            $table->dropColumn('store_id');
        });
    }
}
