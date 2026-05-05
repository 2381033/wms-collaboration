<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvCyclecountBatchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iv_cyclecount_batch', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('principal_id');
            $table->unsignedBigInteger('cyclecount_id');
            $table->unsignedBigInteger('serial_id');
            $table->string('serial_no', 15);
            $table->string('job_no', 15);
            $table->dateTime('job_date');
            $table->string('vehicle_no', 30)->nullable();
            $table->integer('line_no')->default(0);
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
            $table->integer('qtyr')->default(0);
            $table->integer('qtys')->default(0);
            $table->integer('qtya')->default(0);
            $table->integer('qtyp')->default(0);
            $table->integer('pallet_qty')->default(0);
            $table->decimal('base_unit', 18, 3)->default(0);
            $table->string('reference_no', 15)->nullable();
            $table->enum('confirmed_flag', ['Yes', 'No'])->default('No');
            $table->string('confirmed_by', 20)->nullable();
            $table->dateTime('confirmed_date')->nullable();
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
        Schema::table('iv_cyclecount_batch', function (Blueprint $table) {
            $table->dropForeign('iv_cyclecount_batch_company_id_foreign');
            $table->dropForeign('iv_cyclecount_batch_principal_id_foreign');
            $table->dropForeign('iv_cyclecount_batch_product_id_foreign');
            $table->dropForeign('iv_cyclecount_batch_site_id_foreign');
            $table->dropForeign('iv_cyclecount_batch_area_id_foreign');
            $table->dropForeign('iv_cyclecount_batch_location_id_foreign');
        });

        Schema::dropIfExists('iv_cyclecount_batch');
    }
}
