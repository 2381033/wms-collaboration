<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExOutboundDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ex_outbound_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("job_id");
            $table->unsignedBigInteger("order_id");
            $table->string('po_number', 30);
            $table->string('peb_no', 30);
            $table->string('serial_no', 50)->nullable();
            $table->integer('quantity')->default(0);
            $table->enum("status_flag", [ "Open", "Confirmed" ])->default("Open");
            $table->string('user_id', 10)->nullable();
            $table->timestamps();

            $table->foreign('job_id')->references('id')->on('ex_outbound_header');
            $table->foreign('order_id')->references('id')->on('ex_outbound_order');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ex_outbound_detail', function (Blueprint $table) {
            $table->dropForeign('ex_outbound_detail_job_id_foreign');
            $table->dropForeign('ex_outbound_detail_order_id_foreign');
        }); 

        Schema::dropIfExists('ex_outbound_detail');
    }
}