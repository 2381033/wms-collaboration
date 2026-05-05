<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExStockLedgerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ex_stock_ledger', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("branch_id");
            $table->string('job_no', 15);
            $table->dateTime('job_date');
            $table->string('po_number', 30);
            $table->string('vehicle_no', 10);
            $table->unsignedBigInteger('forwarder_id');
            $table->unsignedBigInteger('consignee_id');
            $table->unsignedBigInteger('shipper_id');
            $table->string('destination', 100);
            $table->string('peb_no', 30);
            $table->string('pic_name', 100);
            $table->integer('qty_cargo')->default(0);
            $table->decimal('cbm', 18, 3)->default(0);
            $table->decimal('weight', 18, 3)->default(0);
            $table->integer('total_pallet')->default(0);
            $table->string('serial_no', 50)->nullable();
            $table->integer('pallet_id')->default(0);
            $table->integer('quantity')->default(0);
            $table->enum("status_flag", ["Inbound", "Book", "Outbound"])->default("Inbound");
            $table->string('user_id', 10)->nullable();
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('mt_branch');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ex_stock_ledger', function (Blueprint $table) {
            $table->dropForeign('ex_stock_ledger_branch_id_foreign');
        }); 

        Schema::dropIfExists('ex_stock_ledger');
    }
}