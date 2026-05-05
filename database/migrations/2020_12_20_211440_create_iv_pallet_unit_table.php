<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvPalletUnitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iv_pallet_unit', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('principal_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('type_id');
            $table->string('uom', 5);
            $table->integer('pallet_qty')->default(0);
            $table->integer('base_qty')->default(0);
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('mt_company');
            $table->foreign('principal_id')->references('id')->on('iv_principal');
            $table->foreign('product_id')->references('id')->on('iv_product');
            $table->foreign('type_id')->references('id')->on('iv_location_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('iv_pallet_unit', function (Blueprint $table) {
            $table->dropForeign('iv_pallet_unit_company_id_foreign');
            $table->dropForeign('iv_pallet_unit_principal_id_foreign');
            $table->dropForeign('iv_pallet_unit_product_id_foreign');
            $table->dropForeign('iv_pallet_unit_type_id_foreign');
        });

        Schema::dropIfExists('iv_pallet_unit');
    }
}
