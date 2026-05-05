<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvCustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iv_customer', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('principal_id');
            $table->string('customer_code', 20);
            $table->string('customer_name', 100);
            $table->unsignedBigInteger('group_id')->nullable();
            $table->unsignedBigInteger('type_id')->nullable();
            $table->string('address1', 200)->nullable();
            $table->string('address2', 200)->nullable();
            $table->string('address3', 200)->nullable();  
            $table->string('address4', 200)->nullable();
            $table->string('zip_code', 10)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email', 50)->nullable();
            $table->string('pic_name', 50)->nullable();
            $table->string('pic_phone', 50)->nullable();
            $table->enum('active', ['Yes', 'No'])->default('Yes');
            $table->string("user_id", 10)->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('mt_company');
            $table->foreign('principal_id')->references('id')->on('iv_principal');
            $table->foreign('group_id')->references('id')->on('iv_customer_group');
            $table->foreign('type_id')->references('id')->on('iv_customer_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('iv_customer', function (Blueprint $table) {
            $table->dropForeign('iv_customer_company_id_foreign');
            $table->dropForeign('iv_customer_principal_id_foreign');
            $table->dropForeign('iv_customer_group_id_foreign');
            $table->dropForeign('iv_customer_type_id_foreign');
        });

        Schema::dropIfExists('iv_customer');
    }
}
