<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCyInvoiceDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('cy_invoice_detail', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger("invoice_id");
        //     $table->unsignedBigInteger("outbound_id");
        //     $table->string("outbound_no", 15);
        //     $table->string("job_no", 15);
        //     $table->unsignedBigInteger("serial_id");
        //     $table->string('serial_no', 15);
        //     $table->unsignedBigInteger('size_id')->nullable();
        //     $table->string("container_no", 30)->nullable();
        //     $table->dateTime("received_date")->nullable();
        //     $table->dateTime("dispatch_date")->nullable();
        //     $table->integer("leadtime")->default(0);
        //     $table->decimal("lolo_amount", 18, 3)->default(0);
        //     $table->decimal("storage_amount", 18, 3)->default(0);
        //     $table->decimal("total_amount", 18, 3)->default(0);
        //     $table->timestamps();

        //     $table->foreign('invoice_id')->references('id')->on('cy_invoice_header');
        //     $table->foreign('outbound_id')->references('id')->on('cy_outbound');
        //     $table->foreign('serial_id')->references('id')->on('cy_stock_ledger');
        //     $table->foreign('size_id')->references('id')->on('iv_container_size');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('cy_invoice_detail', function (Blueprint $table) {
        //     $table->dropForeign('cy_invoice_detail_invoice_id_foreign');
        //     $table->dropForeign('cy_invoice_detail_outbound_id_foreign');
        //     $table->dropForeign('cy_invoice_detail_serial_id_foreign');
        //     $table->dropForeign('cy_invoice_detail_size_id_foreign');
        // });

        // Schema::dropIfExists('cy_invoice_detail');
    }
}