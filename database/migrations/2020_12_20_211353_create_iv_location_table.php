<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iv_location', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('site_id');
            $table->unsignedBigInteger('area_id');
            $table->string('location_code', 15);
            $table->string('location_name', 50);
            $table->string('status_code', 1);
            $table->unsignedBigInteger('type_id');
            $table->string('location_aisle', 5)->nullable();
            $table->integer('location_column')->default(0);
            $table->integer('location_level')->default(0);
            $table->unsignedBigInteger('principal_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();            
            $table->decimal('reorder_qty', 18,2)->default(0);
            $table->decimal('reorder_level', 18,2)->default(0);
            $table->unsignedBigInteger('uom_id')->nullable();
            $table->enum('active', ['Yes', 'No'])->default('Yes');
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('mt_company');
            $table->foreign('site_id')->references('id')->on('iv_site');
            $table->foreign('area_id')->references('id')->on('iv_site_area');
            $table->foreign('type_id')->references('id')->on('iv_location_type');
            $table->foreign('status_code')->references('status_code')->on('iv_location_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('iv_location', function (Blueprint $table) {
            $table->dropForeign('iv_location_company_id_foreign');
            $table->dropForeign('iv_location_type_id_foreign');
            $table->dropForeign('iv_location_site_id_foreign');
            $table->dropForeign('iv_location_area_id_foreign');
            $table->dropForeign('iv_location_status_code_foreign');
        });

        Schema::dropIfExists('iv_location');
    }
}
