<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvStockStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iv_stock_status', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('principal_id');
            $table->string('status_name', 50);
            $table->enum('active', ['Yes', 'No'])->default('Yes');
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('mt_company');
            $table->foreign('principal_id')->references('id')->on('iv_principal');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('iv_stock_status', function (Blueprint $table) {
            $table->dropForeign('iv_stock_status_company_id_foreign');
            $table->dropForeign('iv_stock_status_principal_id_foreign');
        });

        Schema::dropIfExists('iv_stock_status');
    }
}
