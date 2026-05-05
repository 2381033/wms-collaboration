<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExInboundHeaderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ex_inbound_header', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
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
            $table->enum("status_flag", ["Open", "Confirmed"])->default("Open");
            $table->string('user_id', 10)->nullable();
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('mt_branch');
            $table->foreign('forwarder_id')->references('id')->on('mt_forwarder');
            $table->foreign('consignee_id')->references('id')->on('mt_consignee');
            $table->foreign('shipper_id')->references('id')->on('mt_shipper');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ex_inbound_header', function (Blueprint $table) {
            $table->dropForeign('ex_inbound_header_branch_id_foreign');
            $table->dropForeign('ex_inbound_header_forwarder_id_foreign');
            $table->dropForeign('ex_inbound_header_consignee_id_foreign');
            $table->dropForeign('ex_inbound_header_shipper_id_foreign');
        }); 
        
        Schema::dropIfExists('ex_inbound_header');
    }
}