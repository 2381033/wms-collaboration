<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvOutboundBatchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iv_outbound_batch', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('outbound_id');
            $table->unsignedBigInteger('picking_id');
            $table->unsignedBigInteger('serial_id');
            $table->unsignedBigInteger('principal_id');
            $table->unsignedBigInteger('customer_id');
            $table->string('order_no', 30);
            $table->string('serial_no', 15);
            $table->string('job_no', 15);
            $table->unsignedBigInteger('product_id');
            $table->string('product_code', 30)->nullable();
            $table->string('po_number', 30)->nullable();
            $table->string('lot_no', 30)->nullable();
            $table->string('document_ref', 30)->nullable();
            $table->string('reference_no', 30)->nullable();
            $table->dateTime('mfg_date')->nullable();
            $table->dateTime('exp_date')->nullable();
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
            $table->decimal('base_unit', 18, 3)->default(0);
            $table->integer('pallet_qty')->default(0);
            $table->enum('confirmed_flag', ['Yes', 'No'])->default('No');
            $table->string('confirmed_by', 20)->nullable();
            $table->dateTime('confirmed_date')->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('mt_company');
            $table->foreign('outbound_id')->references('id')->on('iv_outbound_job');
            $table->foreign('principal_id')->references('id')->on('iv_principal');
            $table->foreign('product_id')->references('id')->on('iv_product');
            $table->foreign('customer_id')->references('id')->on('iv_customer');
            $table->foreign('site_id')->references('id')->on('iv_site');
            $table->foreign('area_id')->references('id')->on('iv_site_area');
            $table->foreign('location_id')->references('id')->on('iv_location');
            $table->foreign('serial_id')->references('id')->on('iv_stock_ledger');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('iv_outbound_batch', function (Blueprint $table) {
            $table->dropForeign('iv_outbound_batch_outbound_id_foreign');
            $table->dropForeign('iv_outbound_batch_company_id_foreign');
            $table->dropForeign('iv_outbound_batch_principal_id_foreign');
            $table->dropForeign('iv_outbound_batch_customer_id_foreign');
            $table->dropForeign('iv_outbound_batch_product_id_foreign');
            $table->dropForeign('iv_outbound_batch_site_id_foreign');
            $table->dropForeign('iv_outbound_batch_area_id_foreign');
            $table->dropForeign('iv_outbound_batch_location_id_foreign');
            $table->dropForeign('iv_outbound_batch_serial_id_foreign');
        });

        Schema::dropIfExists('iv_outbound_batch');
    }
}
