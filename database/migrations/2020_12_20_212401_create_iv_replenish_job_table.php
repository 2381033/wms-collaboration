<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvReplenishJobTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iv_replenish_job', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('principal_id');
            $table->string('replenish_no', 15);
            $table->dateTime('replenish_date');
            $table->unsignedBigInteger('product_id_from')->nullable();
            $table->unsignedBigInteger('product_id_to')->nullable();
            $table->unsignedBigInteger('site_id')->nullable();
            $table->unsignedBigInteger('area_id')->nullable();
            $table->unsignedBigInteger('location_id_from')->nullable();
            $table->unsignedBigInteger('location_code_from')->nullable(); 
            $table->unsignedBigInteger('location_id_to')->nullable();
            $table->unsignedBigInteger('location_code_to')->nullable(); 
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
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('iv_replenish_job', function (Blueprint $table) {
            $table->dropForeign('iv_replenish_job_company_id_foreign');
            $table->dropForeign('iv_replenish_job_principal_id_foreign');
        });

        Schema::dropIfExists('iv_replenish_job');
    }
}
