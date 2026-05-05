<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCyStockTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('cy_stock_transaction', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger('branch_id');
        //     $table->unsignedBigInteger("forwarder_id");
        //     $table->unsignedBigInteger("booking_id")->nullable();
        //     $table->unsignedBigInteger("inbound_id")->nullable();
        //     $table->string("booking_no", 15)->nullable();
        //     $table->string('job_no', 15);
        //     $table->dateTime('job_date');
        //     $table->enum("job_type", ["Inbound", "Outbound"])->nullable();
        //     $table->string('serial_no', 15);
        //     $table->string("reference_no", 30)->nullable();
        //     $table->string('vehicle_no', 30)->nullable();
        //     $table->string("driver_name", 50)->nullable();
        //     $table->unsignedBigInteger("invoice_type");
        //     $table->unsignedBigInteger('size_id')->nullable();
        //     $table->unsignedBigInteger("type_id")->nullable();
        //     $table->enum("container_status", ["Empty", "Full"])->nullable();
        //     $table->string('container_no', 30)->nullable();
        //     $table->integer('qty')->default(1);
        //     $table->string('reference_job', 15);
        //     $table->string("user_id", 10)->nullable();
        //     $table->timestamps();            

        //     $table->foreign('branch_id')->references('id')->on('mt_branch');
        //     $table->foreign('forwarder_id')->references('id')->on('mt_forwarder');
        //     $table->foreign('invoice_type')->references('id')->on('cy_invoice_type');
        //     $table->foreign('size_id')->references('id')->on('iv_container_size');
        //     $table->foreign('type_id')->references('id')->on('iv_container_type');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('cy_stock_transaction', function (Blueprint $table) {
        //     $table->dropForeign('cy_stock_transaction_branch_id_foreign');
        //     $table->dropForeign('cy_stock_transaction_forwarder_id_foreign');
        //     $table->dropForeign('cy_stock_transaction_invoice_type_foreign');
        //     $table->dropForeign('cy_stock_transaction_size_id_foreign');
        //     $table->dropForeign('cy_stock_transaction_type_id_foreign');
        // });

        // Schema::dropIfExists('cy_stock_transaction');
    }
}