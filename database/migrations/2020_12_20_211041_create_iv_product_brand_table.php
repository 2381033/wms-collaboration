<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvProductBrandTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iv_product_brand', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('principal_id');
            $table->unsignedBigInteger('group_id');
            $table->string('brand_code', 10);
            $table->string('brand_name', 50);
            $table->enum('active', ['Yes', 'No'])->default('Yes');
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('mt_company');
            $table->foreign('principal_id')->references('id')->on('iv_principal');
            $table->foreign('group_id')->references('id')->on('iv_product_group');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('iv_product_brand', function (Blueprint $table) {
            $table->dropForeign('iv_product_brand_company_id_foreign');
            $table->dropForeign('iv_product_brand_principal_id_foreign');
            $table->dropForeign('iv_product_brand_group_id_foreign');
        });

        Schema::dropIfExists('iv_product_brand');
    }
}
