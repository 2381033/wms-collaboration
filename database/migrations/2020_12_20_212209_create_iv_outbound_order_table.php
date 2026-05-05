<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvOutboundOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iv_outbound_order', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('outbound_id');
            $table->unsignedBigInteger('principal_id');
            $table->unsignedBigInteger('customer_id');
            $table->string('job_no', 15);
            $table->string('order_no', 30);
            $table->string('po_number', 30);
            $table->dateTime('order_date');
            $table->dateTime('due_date');
            $table->enum('confirmed_flag', ['Yes', 'No'])->default('No');
            $table->string("user_id", 10)->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('mt_company');
            $table->foreign('outbound_id')->references('id')->on('iv_outbound_job');
            $table->foreign('principal_id')->references('id')->on('iv_principal');
            $table->foreign('customer_id')->references('id')->on('iv_customer');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('iv_outbound_order', function (Blueprint $table) {
            $table->dropForeign('iv_outbound_order_outbound_id_foreign');
            $table->dropForeign('iv_outbound_order_company_id_foreign');
            $table->dropForeign('iv_outbound_order_principal_id_foreign');
            $table->dropForeign('iv_outbound_order_customer_id_foreign');
        });

        Schema::dropIfExists('iv_outbound_order');
    }
}
