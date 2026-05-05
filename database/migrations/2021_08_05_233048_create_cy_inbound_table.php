<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCyInboundTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('cy_inbound', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger("branch_id");
        //     $table->unsignedBigInteger("forwarder_id");
        //     $table->string("job_no", 15);
        //     $table->dateTime("job_date");
        //     $table->unsignedBigInteger("booking_id");
        //     $table->string("checklist_no", 15)->nullable();
        //     $table->string("booking_no", 15)->nullable();
        //     $table->string("reference_no", 30)->nullable();
        //     $table->unsignedBigInteger("invoice_type");
        //     $table->string("book_driver_name", 50)->nullable();
        //     $table->string("book_vehicle_no", 50)->nullable();
        //     $table->unsignedBigInteger('book_size_id')->nullable();
        //     $table->unsignedBigInteger("book_type_id")->nullable();
        //     $table->enum("book_container_status", ["Empty", "Full"])->nullable();
        //     $table->string("book_container_no", 30)->nullable();
        //     $table->string("driver_name", 50)->nullable();
        //     $table->string("vehicle_no", 50)->nullable();
        //     $table->unsignedBigInteger('size_id')->nullable();
        //     $table->unsignedBigInteger("type_id")->nullable();
        //     $table->enum("container_status", ["Empty", "Full"])->nullable();
        //     $table->string("container_no", 30)->nullable();
        //     $table->enum("confirmed_flag", ["Open", "Cancel", "Confirmed"])->default("Open");            
        //     $table->string("confirmed_by", 10)->nullable();
        //     $table->dateTime("confirmed_date")->nullable();
        //     $table->string("user_id", 10)->nullable();
        //     $table->timestamps();

        //     $table->foreign('branch_id')->references('id')->on('mt_branch');
        //     $table->foreign('forwarder_id')->references('id')->on('mt_forwarder');
        //     $table->foreign('booking_id')->references('id')->on('cy_booking');
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
        // Schema::table('cy_inbound', function (Blueprint $table) {
        //     $table->dropForeign('cy_inbound_branch_id_foreign');
        //     $table->dropForeign('cy_inbound_forwarder_id_foreign');
        //     $table->dropForeign('cy_inbound_booking_id_foreign');
        //     $table->dropForeign('cy_inbound_invoice_type_foreign');
        //     $table->dropForeign('cy_inbound_size_id_foreign');
        //     $table->dropForeign('cy_inbound_type_id_foreign');
        // });

        // Schema::dropIfExists('cy_inbound');
    }
}