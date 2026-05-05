<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvInboundBatchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iv_inbound_batch', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('inbound_id');
            $table->unsignedBigInteger('packing_id');
            $table->unsignedBigInteger('principal_id');
            $table->string('serial_no', 15);
            $table->string('job_no', 15);
            $table->string('vehicle_no', 30);
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
            $table->integer('pallet_id')->default(0);
            $table->string('puom', 5)->nullable();
            $table->string('muom', 5)->nullable();
            $table->string('buom', 5)->nullable();
            $table->integer('uppp')->default(0);
            $table->integer('muppp')->default(0);
            $table->integer('pqty')->default(0);
            $table->integer('mqty')->default(0);
            $table->integer('bqty')->default(0);
            $table->integer('qty')->default(0);
            $table->integer('pallet_qty')->default(0);
            $table->decimal('base_unit', 18, 3)->default(0);
            $table->enum('manual_putaway', ['Yes', 'No'])->default('No');
            $table->enum('product_status', ['Goods', 'Damage'])->default('Goods');
            $table->enum('confirmed_flag', ['Yes', 'No'])->default('No');
            $table->enum('crossdock_flag', ['Yes', 'No'])->default('No');
            $table->string('confirmed_by', 20)->nullable();
            $table->dateTime('confirmed_date')->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('mt_company');
            $table->foreign('inbound_id')->references('id')->on('iv_inbound_job');
            $table->foreign('principal_id')->references('id')->on('iv_principal');
            $table->foreign('product_id')->references('id')->on('iv_product');
            $table->foreign('manufactur_id')->references('id')->on('iv_manufactur');
            $table->foreign('site_id')->references('id')->on('iv_site');
            $table->foreign('area_id')->references('id')->on('iv_site_area');
            $table->foreign('location_id')->references('id')->on('iv_location');
            $table->foreign('status_id')->references('id')->on('iv_stock_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('iv_inbound_batch', function (Blueprint $table) {
            $table->dropForeign('iv_inbound_batch_company_id_foreign');
            $table->dropForeign('iv_inbound_batch_inbound_id_foreign');
            $table->dropForeign('iv_inbound_batch_principal_id_foreign');
            $table->dropForeign('iv_inbound_batch_product_id_foreign');
            $table->dropForeign('iv_inbound_batch_manufactur_id_foreign');
            $table->dropForeign('iv_inbound_batch_site_id_foreign');
            $table->dropForeign('iv_inbound_batch_area_id_foreign');
            $table->dropForeign('iv_inbound_batch_location_id_foreign');
            $table->dropForeign('iv_inbound_batch_status_id_foreign');
        });

        Schema::dropIfExists('iv_inbound_batch');
    }
}
