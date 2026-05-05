<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvSiteIndicatorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iv_site_indicator', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('type_id');
            $table->string('indicator_name', 50);
            $table->enum('active', ['Yes', 'No'])->default('Yes');
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('mt_company');
            $table->foreign('type_id')->references('id')->on('iv_site_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('iv_site_indicator', function (Blueprint $table) {
            $table->dropForeign('iv_site_indicator_company_id_foreign');
            $table->dropForeign('iv_site_indicator_type_id_foreign');
        });
        Schema::dropIfExists('iv_site_indicator');
    }
}
