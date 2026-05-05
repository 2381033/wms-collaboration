<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExInboundDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ex_inbound_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("job_id");
            $table->string('serial_no', 50)->nullable();
            $table->integer('pallet_id')->default(0);
            $table->integer('quantity')->default(0);
            $table->string('user_id', 10)->nullable();
            $table->timestamps();

            $table->foreign('job_id')->references('id')->on('ex_inbound_header');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ex_inbound_detail', function (Blueprint $table) {
            $table->dropForeign('ex_inbound_detail_job_id_foreign');
        });

        Schema::dropIfExists('ex_inbound_detail');
    }
}