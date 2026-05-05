<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvTransferDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iv_transfer_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('transfer_id');
            $table->unsignedBigInteger('principal_id');
            $table->string('job_no', 15);
            $table->unsignedBigInteger('serial_id');
            $table->string('serial_no', 15);
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
            $table->integer('actual_pqty')->default(0);
            $table->integer('actual_mqty')->default(0);
            $table->integer('actual_bqty')->default(0);
            $table->integer('actual_qty')->default(0);
            $table->unsignedBigInteger('dest_site_id')->nullable();
            $table->unsignedBigInteger('dest_area_id')->nullable();
            $table->unsignedBigInteger('dest_location_id')->nullable();
            $table->string('dest_location_code', 15)->nullable();
            $table->integer('pallet_qty')->default(0);
            $table->decimal('base_unit', 18, 3)->default(0);
            $table->string('srno', 15)->nullable();
            $table->enum('picked_flag', ['Yes', 'No'])->default('No');
            $table->string('picked_by', 20)->nullable();
            $table->dateTime('picked_date')->nullable();
            $table->enum('confirmed_flag', ['Yes', 'No'])->default('No');
            $table->string('confirmed_by', 20)->nullable();
            $table->dateTime('confirmed_date')->nullable();
            $table->string("user_id", 10)->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('mt_company');
            $table->foreign('transfer_id')->references('id')->on('iv_transfer_job');
            $table->foreign('principal_id')->references('id')->on('iv_principal');
            $table->foreign('product_id')->references('id')->on('iv_product');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('iv_transfer_detail', function (Blueprint $table) {
            $table->dropForeign('iv_transfer_detail_company_id_foreign');
            $table->dropForeign('iv_transfer_detail_transfer_id_foreign');
            $table->dropForeign('iv_transfer_detail_principal_id_foreign');
            $table->dropForeign('iv_transfer_detail_product_id_foreign');
        });

        Schema::dropIfExists('iv_transfer_detail');
    }
}
