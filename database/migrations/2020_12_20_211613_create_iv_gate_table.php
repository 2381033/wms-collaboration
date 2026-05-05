<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvGateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iv_gate', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('token_id', 20);
            $table->string('vehicle_no', 15);
            $table->string('driver_name', 50);
            $table->string('transporter_name', 50);
            $table->unsignedBigInteger('principal_id');
            $table->enum('service', ['Inbound', 'Outbound'])->default('Inbound');
            $table->dateTime('gate_in');
            $table->dateTime('gate_out')->nullable();
            $table->enum('closed_flag', ['Yes', 'No', 'Del'])->default('No');
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('mt_company');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('iv_gate', function (Blueprint $table) {
            $table->dropForeign('iv_gate_company_id_foreign');
        });

        Schema::dropIfExists('iv_gate');
    }
}
