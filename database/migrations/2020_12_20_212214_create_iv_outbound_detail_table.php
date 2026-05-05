<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvOutboundDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iv_outbound_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('outbound_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('principal_id');
            $table->unsignedBigInteger('customer_id');
            $table->string('job_no', 15);
            $table->string('order_no', 30);
            $table->unsignedBigInteger('product_id');
            $table->string('product_code', 30)->nullable();
            $table->string('lot_no', 30)->nullable();
            $table->string('document_ref', 30)->nullable();
            $table->unsignedBigInteger('site_id')->nullable();
            $table->unsignedBigInteger('area_id')->nullable();
            $table->unsignedBigInteger('location_from_id')->nullable();
            $table->string('location_from', 15)->nullable();
            $table->unsignedBigInteger('location_to_id')->nullable();
            $table->string('location_to', 15)->nullable();
            $table->string('puom', 5)->nullable();
            $table->string('muom', 5)->nullable();
            $table->string('buom', 5)->nullable();
            $table->integer('uppp')->default(0);
            $table->integer('muppp')->default(0);
            $table->integer('pqty')->default(0);
            $table->integer('mqty')->default(0);
            $table->integer('bqty')->default(0);
            $table->integer('qty')->default(0);
            $table->enum('picking_flag', ['Yes', 'No'])->default('No');
            $table->string('picking_by', 20)->nullable();
            $table->dateTime('picking_date')->nullable();
            $table->enum('confirmed_flag', ['Yes', 'No'])->default('No');
            $table->string('confirmed_by', 20)->nullable();
            $table->dateTime('confirmed_date')->nullable();
            $table->string("user_id", 10)->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('mt_company');
            $table->foreign('outbound_id')->references('id')->on('iv_outbound_job');
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
        Schema::table('iv_outbound_detail', function (Blueprint $table) {
            $table->dropForeign('iv_outbound_detail_outbound_id_foreign');
            $table->dropForeign('iv_outbound_detail_company_id_foreign');
            $table->dropForeign('iv_outbound_detail_principal_id_foreign');
            $table->dropForeign('iv_outbound_detail_product_id_foreign');
        });

        Schema::dropIfExists('iv_outbound_detail');
    }
}
