<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvOutboundDespatchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iv_outbound_despatch', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('principal_id');
            $table->unsignedBigInteger('outbound_id');            
            $table->string('job_no', 15);
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('mode_id');     
            $table->string('do_no', 15)->nullable(); 
            $table->string('reference_no', 30)->nullable(); 
            $table->string('carrier_name', 50)->nullable();
            $table->string('vessel_name', 50)->nullable();
            $table->string('vehicle_no', 15)->nullable();  
            $table->string('seal_no', 30)->nullable();
            $table->string('driver_name', 50)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('container_no', 30)->nullable();
            $table->dateTime('etd')->nullable();
            $table->integer('pqty')->default(0);
            $table->integer('mqty')->default(0);
            $table->integer('bqty')->default(0);
            $table->integer('order_count')->default(0);
            $table->string('awb_no', 30)->nullable();
            $table->string('awb_date', 30)->nullable();
            $table->string('send_date_doc', 30)->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('mt_company');
            $table->foreign('outbound_id')->references('id')->on('iv_outbound_job');
            $table->foreign('principal_id')->references('id')->on('iv_principal');
            $table->foreign('customer_id')->references('id')->on('iv_customer');
            $table->foreign('mode_id')->references('id')->on('iv_mode');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('iv_outbound_despatch', function (Blueprint $table) {
            $table->dropForeign('iv_outbound_despatch_outbound_id_foreign');
            $table->dropForeign('iv_outbound_despatch_company_id_foreign');
            $table->dropForeign('iv_outbound_despatch_principal_id_foreign');
            $table->dropForeign('iv_outbound_despatch_customer_id_foreign');
            $table->dropForeign('iv_outbound_despatch_mode_id_foreign');
        });

        Schema::dropIfExists('iv_outbound_despatch');
    }
}
