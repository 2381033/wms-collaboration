<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTmStoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tm_store', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('principal_id');
            $table->string("store_code", 30);
            $table->string("store_name", 30);
            $table->string('country_code', 3);
            $table->string('region_code', 20);
            $table->string('city_code', 20);
            $table->string('address1', 200)->nullable();
            $table->string('address2', 200)->nullable();
            $table->string('address3', 200)->nullable();  
            $table->string('address4', 200)->nullable();
            $table->string("postal_code", 10)->nullable();
            $table->string("telephone", 20)->nullable();
            $table->string("email", 100)->nullable();
            $table->string("pic_name", 50)->nullable();
            $table->string("pic_phone", 20)->nullable();
            $table->string("user_id", 10)->nullable();
            $table->enum('active', ['Yes', 'No'])->default('Yes');
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('mt_company');
            $table->foreign('principal_id')->references('id')->on('iv_principal');
            $table->foreign('country_code')->references('country_code')->on('rt_country');
            $table->foreign('region_code')->references('region_code')->on('rt_region');
            $table->foreign('city_code')->references('city_code')->on('rt_city');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tm_store', function (Blueprint $table) {
            $table->dropForeign('tm_store_company_id_foreign');
            $table->dropForeign('tm_store_principal_id_foreign');
            $table->dropForeign('tm_store_country_code_foreign');
            $table->dropForeign('tm_store_region_code_foreign');
            $table->dropForeign('tm_store_city_code_foreign');
        });

        Schema::dropIfExists('tm_store');
    }
}
