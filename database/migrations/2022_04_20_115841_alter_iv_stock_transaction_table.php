<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterIvStockTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('iv_stock_transaction', function (Blueprint $table) {  
            $table->unsignedBigInteger('branch_id')->nullable()->after('id');

            $table->foreign('branch_id')->references('id')->on('mt_branch');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('iv_stock_transaction', function (Blueprint $table) {
            $table->dropColumn('branch_id');
        });
    }
}