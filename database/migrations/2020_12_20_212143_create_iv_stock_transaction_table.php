<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvStockTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iv_stock_transaction', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('principal_id');
            $table->string('job_no', 15);
            $table->string('serial_no', 15);
            $table->string('srno', 15)->nullable();
            $table->integer('line_no')->default(0);
            $table->dateTime('job_date');
            $table->string('job_type', 5);
            $table->unsignedBigInteger('product_id');
            $table->string('product_code', 30)->nullable();
            $table->string('po_number', 30)->nullable();
            $table->string('lot_no', 30)->nullable();
            $table->string('document_ref', 30)->nullable();
            $table->dateTime('mfg_date')->nullable();
            $table->dateTime('exp_date')->nullable();
            $table->unsignedBigInteger('manufactur_id')->nullable();
            $table->unsignedBigInteger('status_id')->nullable();
            $table->unsignedBigInteger('site_id')->nullable();
            $table->unsignedBigInteger('area_id')->nullable();
            $table->unsignedBigInteger('location_id')->nullable();
            $table->string('location_code', 15)->nullable();
            $table->string('puom', 5)->nullable();
            $table->string('muom', 5)->nullable();
            $table->string('buom', 5)->nullable();
            $table->integer('uppp')->default(0);
            $table->integer('muppp')->default(0);
            $table->integer('pqty')->default(0);
            $table->integer('mqty')->default(0);
            $table->integer('bqty')->default(0);
            $table->integer('qty')->default(0);
            $table->integer('grn_no')->default(0);
            $table->decimal('base_unit', 18, 3)->default(0);
            $table->string('reference_no', 15)->nullable();
            $table->string("user_id", 10)->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('mt_company');
            $table->foreign('principal_id')->references('id')->on('iv_principal');
            $table->foreign('product_id')->references('id')->on('iv_product');
            $table->foreign('site_id')->references('id')->on('iv_site');
            $table->foreign('area_id')->references('id')->on('iv_site_area');
            $table->foreign('location_id')->references('id')->on('iv_location');
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
            $table->dropForeign('iv_stock_transaction_company_id_foreign');
            $table->dropForeign('iv_stock_transaction_principal_id_foreign');
            $table->dropForeign('iv_stock_transaction_product_id_foreign');
            $table->dropForeign('iv_stock_transaction_site_id_foreign');
            $table->dropForeign('iv_stock_transaction_area_id_foreign');
            $table->dropForeign('iv_stock_transaction_location_id_foreign');
        });

        Schema::dropIfExists('iv_stock_transaction');
    }
}
