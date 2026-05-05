<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvPrincipalToSiteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iv_principal_site', function (Blueprint $table) {
            $table->unsignedBigInteger('principal_id');
            $table->unsignedBigInteger('site_id');
            $table->timestamps();

            $table->foreign('principal_id')->references('id')->on('iv_principal');
            $table->foreign('site_id')->references('id')->on('iv_site');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('iv_principal_site', function (Blueprint $table) {
            $table->dropForeign('iv_principal_site_principal_id_foreign');
            $table->dropForeign('iv_principal_site_site_id_foreign');
        });

        Schema::dropIfExists('iv_principal_site');
    }
}
