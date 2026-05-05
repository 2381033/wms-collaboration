<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvAdjustmentDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iv_adjustment_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('principal_id');
            $table->unsignedBigInteger('adjust_id');            
            $table->enum('status_flag', ['Exist', 'New'])->default('Exist');
            $table->enum('adjust_type', ['Plus', 'Minus'])->default('Plus');
            $table->string('job_no', 15)->nullable();
            $table->unsignedBigInteger('serial_id')->nullable();
            $table->string('serial_no', 15)->nullable();
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
            $table->integer('pallet_qty')->default(0);
            $table->decimal('base_unit', 18, 3)->default(0);
            $table->enum('picked_flag', ['Yes', 'No'])->default('No');
            $table->string('picked_by', 20)->nullable();
            $table->dateTime('picked_date')->nullable();
            $table->enum('confirmed_flag', ['Yes', 'No'])->default('No');
            $table->string('confirmed_by', 20)->nullable();
            $table->dateTime('confirmed_date')->nullable();
            $table->string("user_id", 10)->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('mt_company');
            $table->foreign('principal_id')->references('id')->on('iv_principal');
            $table->foreign('product_id')->references('id')->on('iv_product');
            $table->foreign('adjust_id')->references('id')->on('iv_adjustment_job');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('iv_adjustment_detail', function (Blueprint $table) {
            $table->dropForeign('iv_adjustment_detail_company_id_foreign');
            $table->dropForeign('iv_adjustment_detail_principal_id_foreign');
            $table->dropForeign('iv_adjustment_detail_product_id_foreign');
            $table->dropForeign('iv_adjustment_detail_adjust_id_foreign');
        });

        Schema::dropIfExists('iv_adjustment_detail');
    }
}
