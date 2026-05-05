<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvStocktakeJobTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iv_stocktake_job', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('principal_id');
            $table->string('stocktake_no', 15);
            $table->dateTime('stocktake_date');
            $table->string('description', 150);
            $table->unsignedBigInteger('group_id_from')->nullable();
            $table->unsignedBigInteger('group_id_to')->nullable();
            $table->unsignedBigInteger('brand_id_from')->nullable();
            $table->unsignedBigInteger('brand_id_to')->nullable();
            $table->unsignedBigInteger('product_id_from')->nullable();
            $table->unsignedBigInteger('product_id_to')->nullable();
            $table->unsignedBigInteger('site_id')->nullable();
            $table->unsignedBigInteger('area_id_from')->nullable();
            $table->unsignedBigInteger('area_id_to')->nullable();    
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
        Schema::table('iv_stocktake_job', function (Blueprint $table) {
            $table->dropForeign('iv_stocktake_job_company_id_foreign');
            $table->dropForeign('iv_stocktake_job_principal_id_foreign');
        });

        Schema::dropIfExists('iv_stocktake_job');
    }
}
