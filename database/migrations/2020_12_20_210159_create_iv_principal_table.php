<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvPrincipalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iv_principal', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('principal_name', 150);
            $table->string('short_name', 50);
            $table->string('interface_mode', 5)->default('FMCG');
            $table->string('address1', 200)->nullable();
            $table->string('address2', 200)->nullable();
            $table->string('address3', 200)->nullable();  
            $table->string('address4', 200)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email', 50)->nullable();
            $table->string('pic_name', 50)->nullable();
            $table->string('pic_phone', 50)->nullable();
            $table->integer('despatch_no')->default(0);
            $table->integer('grn_no')->default(0);
            $table->integer('cycle_no')->default(0);            
            $table->enum('pallet_capacity_racking', ['Yes', 'No'])->default('Yes');
            $table->enum('pallet_capacity_bulk', ['Yes', 'No'])->default('Yes');
            $table->enum('multi_level', ['Yes', 'No'])->default('Yes');
            $table->unsignedBigInteger('site_bad')->nullable();
            $table->enum('active', ['Yes', 'No'])->default('Yes');  
            $table->string("user_id", 10)->nullable();
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
        Schema::table('iv_principal', function (Blueprint $table) {
            $table->dropForeign('iv_principal_company_id_foreign');
        });

        Schema::dropIfExists('iv_principal');
    }
}
