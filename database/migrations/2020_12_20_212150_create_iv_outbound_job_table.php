<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvOutboundJobTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iv_outbound_job', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('principal_id');
            $table->string('job_no', 15);
            $table->dateTime('job_date');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('mode_id');
            $table->string('description', 150);
            $table->string('reference_no', 50)->nullable();
            $table->string('reference_other', 50)->nullable();
            $table->dateTime('etd')->nullable();
            $table->dateTime('ata')->nullable();
            $table->string('token_id', 20)->nullable();
            $table->string('remarks', 150)->nullable();
            $table->dateTime('loading_start')->nullable();
            $table->dateTime('loading_finish')->nullable();
            $table->dateTime('entry_date')->nullable();
            $table->enum('allocated_flag', ['Yes', 'No'])->default('No');
            $table->string('allocated_by', 20)->nullable();
            $table->dateTime('allocated_date')->nullable();
            $table->enum('confirmed_flag', ['Yes', 'No'])->default('No');
            $table->string('confirmed_by', 20)->nullable();
            $table->dateTime('confirmed_date')->nullable();
            $table->string("user_id", 10)->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('mt_company');
            $table->foreign('principal_id')->references('id')->on('iv_principal');
            $table->foreign('class_id')->references('id')->on('iv_job_class');
            $table->foreign('mode_id')->references('id')->on('iv_mode');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('iv_outbound_job', function (Blueprint $table) {
            $table->dropForeign('iv_outbound_job_company_id_foreign');
            $table->dropForeign('iv_outbound_job_principal_id_foreign');
            $table->dropForeign('iv_outbound_job_class_id_foreign');
            $table->dropForeign('iv_outbound_job_mode_id_foreign');
        });

        Schema::dropIfExists('iv_outbound_job');
    }
}
