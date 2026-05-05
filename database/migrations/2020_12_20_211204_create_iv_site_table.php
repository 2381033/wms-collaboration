<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvSiteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iv_site', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('site_name', 50);            
            $table->unsignedBigInteger('type_id');
            $table->unsignedBigInteger('indicator_id');
            $table->unsignedBigInteger('location_id');
            $table->string('address', 250)->nullable();     
            $table->string('zip_code', 10)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('fax', 50)->nullable();
            $table->enum('active', ['Yes', 'No'])->default('Yes');
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('mt_company');
            $table->foreign('type_id')->references('id')->on('iv_site_type');
            $table->foreign('indicator_id')->references('id')->on('iv_site_indicator');            
            $table->foreign('location_id')->references('id')->on('iv_location_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('iv_site', function (Blueprint $table) {
            $table->dropForeign('iv_site_company_id_foreign');
            $table->dropForeign('iv_site_type_id_foreign');
            $table->dropForeign('iv_site_indicator_id_foreign');
            $table->dropForeign('iv_site_location_id_foreign');
        });

        Schema::dropIfExists('iv_site');
    }
}
