<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvInboundVehicleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iv_inbound_vehicle', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('inbound_id');
            $table->unsignedBigInteger('principal_id');
            $table->string('job_no', 15)->nullable();
            $table->string('vehicle_no', 30);
            $table->string('transporter_name', 50);
            $table->string('driver_name', 50);
            $table->string('container_no', 50)->nullable();
            $table->string('seal_no', 50)->nullable();
            $table->string('awb_no', 50)->nullable();
            $table->unsignedBigInteger('type_id');
            $table->unsignedBigInteger('size_id');
            $table->enum('confirmed_flag', ['Yes', 'No'])->default('No');
            $table->string("user_id", 10)->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('mt_company');
            $table->foreign('inbound_id')->references('id')->on('iv_inbound_job');
            $table->foreign('principal_id')->references('id')->on('iv_principal');
            $table->foreign('type_id')->references('id')->on('iv_container_type');
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
        Schema::table('iv_inbound_vehicle', function (Blueprint $table) {
            $table->dropForeign('iv_inbound_vehicle_inbound_id_foreign');
            $table->dropForeign('iv_inbound_vehicle_company_id_foreign');
            $table->dropForeign('iv_inbound_vehicle_principal_id_foreign');
            $table->dropForeign('iv_inbound_vehicle_type_id_foreign');
            $table->dropForeign('iv_inbound_vehicle_size_id_foreign');
        });

        Schema::dropIfExists('iv_inbound_vehicle');
    }
}
