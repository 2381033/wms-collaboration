<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFmVehicleDocumentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fm_vehicle_document', function (Blueprint $table) {
            $table->unsignedBigInteger('vehicle_id');
            $table->unsignedBigInteger('document_id');
            $table->dateTime("expired_date")->nullable();
            $table->timestamps();

            $table->foreign('vehicle_id')->references('id')->on('fm_vehicle');
            $table->foreign('document_id')->references('id')->on('fm_document');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fm_vehicle_document', function (Blueprint $table) {
            $table->dropForeign('fm_vehicle_document_vehicle_id_foreign');
            $table->dropForeign('fm_vehicle_document_document_id_foreign');
        });

        Schema::dropIfExists('fm_vehicle_document');
    }
}
