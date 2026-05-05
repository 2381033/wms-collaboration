<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCyStockLedgerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('cy_stock_ledger', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger('branch_id');
        //     $table->unsignedBigInteger('forwarder_id');
        //     $table->unsignedBigInteger("booking_id")->nullable();
        //     $table->unsignedBigInteger("inbound_id")->nullable();
        //     $table->string("booking_no", 15)->nullable();
        //     $table->string('serial_no', 15);
        //     $table->string('job_no', 15);
        //     $table->dateTime('job_date');
        //     $table->string("reference_no", 30)->nullable();
        //     $table->string('vehicle_no', 30)->nullable();
        //     $table->string("driver_name", 50)->nullable();
        //     $table->unsignedBigInteger("invoice_type");
        //     $table->unsignedBigInteger('size_id')->nullable();
        //     $table->unsignedBigInteger("type_id")->nullable();
        //     $table->enum("container_status", ["Empty", "Full"])->nullable();
        //     $table->string('container_no', 30)->nullable();
        //     $table->integer('qtys')->default(1);
        //     $table->integer('qtya')->default(1);
        //     $table->integer('qtyp')->default(0);
        //     $table->string("user_id", 10)->nullable();
        //     $table->timestamps();            

        //     $table->foreign('branch_id')->references('id')->on('mt_branch');
        //     $table->foreign('forwarder_id')->references('id')->on('mt_forwarder');
        //     $table->foreign('invoice_type')->references('id')->on('cy_invoice_type');
        //     $table->foreign('size_id')->references('id')->on('iv_container_size');
        //     $table->foreign('type_id')->references('id')->on('iv_container_type');
        // });
        
        // DB::statement('ALTER TABLE cy_stock_ledger ADD CONSTRAINT chk_qtys CHECK (qtys >= 0);');
        // DB::statement('ALTER TABLE cy_stock_ledger ADD CONSTRAINT chk_qtya CHECK (qtya >= 0);');
        // DB::statement('ALTER TABLE cy_stock_ledger ADD CONSTRAINT chk_qtyp CHECK (qtyp >= 0);');
        // DB::statement('ALTER TABLE cy_stock_ledger ADD CONSTRAINT chk_qty CHECK (qtys = qtya + qtyp);');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('cy_stock_ledger', function (Blueprint $table) {
        //     $table->dropForeign('cy_stock_ledger_branch_id_foreign');
        //     $table->dropForeign('cy_stock_ledger_forwarder_id_foreign');
        //     $table->dropForeign('cy_stock_ledger_invoice_type_foreign');
        //     $table->dropForeign('cy_stock_ledger_size_id_foreign');
        //     $table->dropForeign('cy_stock_ledger_type_id_foreign');
        // });

        // Schema::dropIfExists('cy_stock_ledger');
    }
}