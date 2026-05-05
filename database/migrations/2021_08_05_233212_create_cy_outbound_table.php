<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCyOutboundTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('cy_outbound', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger("branch_id");
        //     $table->string("job_no", 15);
        //     $table->dateTime("job_date");
        //     $table->unsignedBigInteger("forwarder_id");
        //     $table->unsignedBigInteger("serial_id");
        //     $table->unsignedBigInteger("invoice_type");
        //     $table->string("checklist_no", 15)->nullable();
        //     $table->string('serial_no', 15);
        //     $table->string("driver_name", 50)->nullable();
        //     $table->string("vehicle_no", 50)->nullable();
        //     $table->unsignedBigInteger('size_id')->nullable();
        //     $table->unsignedBigInteger("type_id")->nullable();
        //     $table->string("container_no", 30)->nullable();
        //     $table->dateTime("received_date")->nullable();
        //     $table->dateTime("dispatch_date")->nullable();
        //     $table->integer("leadtime")->default(0);
        //     $table->decimal("lolo_amount", 18, 3)->default(0);
        //     $table->decimal("storage_amount", 18, 3)->default(0);
        //     $table->decimal("total_amount", 18, 3)->default(0);
        //     $table->enum("confirmed_flag", ["Open", "Cancel", "Confirmed"])->default("Open");            
        //     $table->string("confirmed_by", 10)->nullable();
        //     $table->dateTime("confirmed_date")->nullable();   
        //     $table->string("invoice_no", 15)->nullable();
        //     $table->enum("invoice_flag", ["No", "Yes"])->default("No");
        //     $table->dateTime("invoice_date")->nullable();
        //     $table->string("invoice_by", 10)->nullable();
        //     $table->string("user_id", 10)->nullable();
        //     $table->timestamps();

        //     $table->foreign('branch_id')->references('id')->on('mt_branch');
        //     $table->foreign('forwarder_id')->references('id')->on('mt_forwarder');
        //     $table->foreign('serial_id')->references('id')->on('cy_stock_ledger');
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
        // Schema::table('cy_outbound', function (Blueprint $table) {
        //     $table->dropForeign('cy_outbound_branch_id_foreign');
        //     $table->dropForeign('cy_outbound_forwarder_id_foreign');
        //     $table->dropForeign('cy_outbound_serial_id_foreign');
        //     $table->dropForeign('cy_outbound_invoice_type_foreign');
        //     $table->dropForeign('cy_outbound_size_id_foreign');
        //     $table->dropForeign('cy_outbound_type_id_foreign');
        // });

        // Schema::dropIfExists('cy_outbound');
    }
}