<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvTransferJobTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iv_transfer_job', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('principal_id');
            $table->string('job_no', 15);
            $table->dateTime('job_date');
            $table->string('description', 250);    
            $table->enum('entry_flag', ['Yes', 'No'])->default('No');
            $table->string('entry_by', 20)->nullable();
            $table->dateTime('entry_date')->nullable();        
            $table->enum('confirmed_flag', ['Yes', 'No'])->default('No');
            $table->string('confirmed_by', 20)->nullable();
            $table->dateTime('confirmed_date')->nullable();   
            $table->string("user_id", 10)->nullable();  
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('mt_company');
            $table->foreign('principal_id')->references('id')->on('iv_principal');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('iv_transfer_job', function (Blueprint $table) {
            $table->dropForeign('iv_transfer_job_company_id_foreign');
            $table->dropForeign('iv_transfer_job_principal_id_foreign');
        });

        Schema::dropIfExists('iv_transfer_job');
    }
}
