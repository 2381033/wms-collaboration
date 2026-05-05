<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExOutboundHeaderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {    
        Schema::create('ex_outbound_header', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->string('job_no', 15);
            $table->dateTime('job_date');
            $table->unsignedBigInteger('forwarder_id');
            $table->unsignedBigInteger('size_id');
            $table->string('container_no', 15);
            $table->string('surveyor_name', 50)->nullable();
            $table->string('destination', 100);
            $table->string('vessel_name', 50)->nullable();
            $table->string('voyage_no', 50)->nullable();
            $table->integer('qty_cargo')->default(0);
            $table->decimal('cbm', 18, 3)->default(0);
            $table->decimal('weight', 18, 3)->default(0);
            $table->integer('total_pallet')->default(0);
            $table->enum("status_flag", ["Open", "Confirmed"])->default("Open");
            $table->string("user_process", 10)->nullable();
            $table->string('user_id', 10)->nullable();
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('mt_branch');
            $table->foreign('forwarder_id')->references('id')->on('mt_forwarder');
            $table->foreign('size_id')->references('id')->on('iv_container_size');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ex_outbound_header', function (Blueprint $table) {
            $table->dropForeign('ex_outbound_header_branch_id_foreign');
            $table->dropForeign('ex_outbound_header_forwarder_id_foreign');
            $table->dropForeign('ex_outbound_header_size_id_foreign');
        }); 

        Schema::dropIfExists('ex_outbound_header');
    }
}