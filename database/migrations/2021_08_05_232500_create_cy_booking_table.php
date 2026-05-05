<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCyBookingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('cy_booking', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger("branch_id");
        //     $table->string("booking_no", 15);
        //     $table->dateTime("booking_date");
        //     $table->unsignedBigInteger("forwarder_id");
        //     $table->unsignedBigInteger("invoice_type")->nullable();
        //     $table->string("reference_no", 30)->nullable();
        //     $table->string("vehicle_no", 50)->nullable();
        //     $table->string("driver_name", 50)->nullable();
        //     $table->unsignedBigInteger('size_id')->nullable();
        //     $table->unsignedBigInteger('type_id')->nullable();
        //     $table->enum("container_status", ["Empty", "Full"])->nullable();
        //     $table->string("container_no", 30)->nullable();
        //     $table->enum("status_flag", ["Open", "Cancel", "Confirmed"])->default("Open");
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
        // Schema::table('cy_booking', function (Blueprint $table) {
        //     $table->dropForeign('cy_booking_branch_id_foreign');
        //     $table->dropForeign('cy_booking_forwarder_id_foreign');
        //     $table->dropForeign('cy_booking_invoice_type_foreign');
        //     $table->dropForeign('cy_booking_size_id_foreign');
        //     $table->dropForeign('cy_booking_type_id_foreign');
        // });

        // Schema::dropIfExists('cy_booking');
    }
}