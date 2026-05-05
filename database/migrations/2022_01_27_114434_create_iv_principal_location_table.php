<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvPrincipalLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iv_principal_location', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("principal_id");
            $table->unsignedBigInteger("site_id");
            $table->unsignedBigInteger("area_id");
            $table->unsignedBigInteger("location_id");
            $table->string("location_code", 15);
            $table->timestamps();

            $table->foreign('principal_id')->references('id')->on('iv_principal');
            $table->foreign('site_id')->references('id')->on('iv_site');
            $table->foreign('area_id')->references('id')->on('iv_site_area');
            $table->foreign('location_id')->references('id')->on('iv_location');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('iv_principal_location', function (Blueprint $table) {
            $table->dropForeign('iv_principal_location_principal_id_foreign');
            $table->dropForeign('iv_principal_location_site_id_foreign');
            $table->dropForeign('iv_principal_location_area_id_foreign');
            $table->dropForeign('iv_principal_location_location_id_foreign');
        });

        Schema::dropIfExists('iv_principal_location');
    }
}