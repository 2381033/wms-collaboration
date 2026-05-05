<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvInboundGrnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iv_inbound_grn', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('inbound_id');
            $table->unsignedBigInteger('principal_id');
            $table->string('job_no', 15);
            $table->integer('grn_no');
            $table->dateTime('grn_date')->nullable();
            $table->string('product_code', 30)->nullable();
            $table->string('po_number', 30)->nullable();
            $table->string('lot_no', 30)->nullable();
            $table->string('document_ref', 30)->nullable();
            $table->dateTime('mfg_date')->nullable();
            $table->dateTime('exp_date')->nullable();
            $table->string('puom', 5)->nullable();
            $table->string('muom', 5)->nullable();
            $table->string('buom', 5)->nullable();
            $table->integer('uppp')->default(0);
            $table->integer('muppp')->default(0);
            $table->integer('pqty')->default(0);
            $table->integer('mqty')->default(0);
            $table->integer('bqty')->default(0);
            $table->integer('qty')->default(0);
            $table->decimal('volume', 18, 3)->default(0);
            $table->decimal('gross_weight', 18, 3)->default(0);
            $table->decimal('net_weight', 18, 3)->default(0);
            $table->decimal('base_unit', 18, 3)->default(0);
            $table->enum('product_status', ['Goods', 'Damage'])->default('Goods');
            $table->enum('confirmed_flag', ['Yes', 'No'])->default('No');
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('mt_company');
            $table->foreign('inbound_id')->references('id')->on('iv_inbound_job');
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
        Schema::table('iv_inbound_grn', function (Blueprint $table) {
            $table->dropForeign('iv_inbound_grn_company_id_foreign');
            $table->dropForeign('iv_inbound_grn_principal_id_foreign');
        });

        Schema::dropIfExists('iv_inbound_grn');
    }
}
