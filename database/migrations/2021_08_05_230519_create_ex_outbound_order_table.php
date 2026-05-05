<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExOutboundOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ex_outbound_order', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("job_id");
            $table->unsignedBigInteger("consignee_id");
            $table->string('po_number', 30);
            $table->string('peb_no', 30);
            $table->integer('qty_cargo')->default(0);
            $table->decimal('cbm', 18, 3)->default(0);
            $table->decimal('weight', 18, 3)->default(0);
            $table->integer('total_pallet')->default(0);
            $table->enum("status_flag", [ "Open", "Partial", "Full" ])->default("Open");
            $table->string('user_id', 10)->nullable();
            $table->timestamps();

            $table->foreign('job_id')->references('id')->on('ex_outbound_header');
            $table->foreign('consignee_id')->references('id')->on('mt_consignee');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ex_outbound_order', function (Blueprint $table) {
            $table->dropForeign('ex_outbound_order_job_id_foreign');
            $table->dropForeign('ex_outbound_order_consignee_id_foreign');
        }); 
        
        Schema::dropIfExists('ex_outbound_order');
    }
}