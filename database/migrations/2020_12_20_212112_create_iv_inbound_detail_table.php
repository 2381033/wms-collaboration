<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvInboundDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iv_inbound_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('inbound_id');
            $table->unsignedBigInteger('principal_id');
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
            $table->enum("mixed_pallet", ["Yes", "No"])->default("No");
            $table->string('location_from', 15)->nullable();
            $table->string('location_to', 15)->nullable();
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
            $table->integer('actual_pqty')->default(0);
            $table->integer('actual_mqty')->default(0);
            $table->integer('actual_bqty')->default(0);
            $table->integer('actual_qty')->default(0);
            $table->integer('discrepancy_pqty')->default(0);
            $table->integer('discrepancy_mqty')->default(0);
            $table->integer('discrepancy_bqty')->default(0);
            $table->integer('discrepancy_qty')->default(0);
            $table->string('remarks', 250)->nullable();
            $table->decimal('base_unit', 18, 3)->default(0);
            $table->enum('product_status', ['Goods', 'Damage'])->default('Goods');
            $table->enum('manual_putaway', ['Yes', 'No'])->default('No');
            $table->enum('received_flag', ['Yes', 'No'])->default('No');
            $table->string('received_by', 20)->nullable();
            $table->dateTime('received_date')->nullable();
            $table->enum('putaway_flag', ['Yes', 'No'])->default('No');
            $table->string('putaway_by', 20)->nullable();
            $table->dateTime('putaway_date')->nullable();
            $table->enum('confirmed_flag', ['Yes', 'No'])->default('No');
            $table->string('confirmed_by', 20)->nullable();
            $table->dateTime('confirmed_date')->nullable();
            $table->string("user_id", 10)->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('mt_company');
            $table->foreign('inbound_id')->references('id')->on('iv_inbound_job');
            $table->foreign('principal_id')->references('id')->on('iv_principal');
            $table->foreign('product_id')->references('id')->on('iv_product');
            $table->foreign('manufactur_id')->references('id')->on('iv_manufactur');
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
        Schema::table('iv_inbound_detail', function (Blueprint $table) {
            $table->dropForeign('iv_inbound_detail_inbound_id_foreign');
            $table->dropForeign('iv_inbound_detail_company_id_foreign');
            $table->dropForeign('iv_inbound_detail_principal_id_foreign');
            $table->dropForeign('iv_inbound_detail_product_id_foreign');
            $table->dropForeign('iv_inbound_detail_manufactur_id_foreign');
            $table->dropForeign('iv_inbound_detail_status_id_foreign');
        });

        Schema::dropIfExists('iv_inbound_detail');
    }
}
