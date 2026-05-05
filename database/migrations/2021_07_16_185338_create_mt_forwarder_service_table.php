<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMtForwarderServiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('mt_forwarder_service', function (Blueprint $table) {
        //     $table->unsignedBigInteger('forwarder_id');
        //     $table->unsignedBigInteger('service_id');
        //     $table->timestamps();

        //     $table->foreign('forwarder_id')->references('id')->on('mt_forwarder');
        //     $table->foreign('service_id')->references('id')->on('mt_service');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('mt_forwarder_service', function (Blueprint $table) {
        //     $table->dropForeign('mt_forwarder_service_forwarder_id_foreign');
        //     $table->dropForeign('mt_forwarder_service_service_id_foreign');
        // });

        // Schema::dropIfExists('mt_forwarder_service');
    }
}
