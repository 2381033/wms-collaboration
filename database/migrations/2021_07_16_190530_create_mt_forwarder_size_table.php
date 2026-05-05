<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMtForwarderSizeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('mt_forwarder_size', function (Blueprint $table) {
        //     $table->unsignedBigInteger('forwarder_id');
        //     $table->unsignedBigInteger('size_id');
        //     $table->decimal("rate_amount", 18, 2)->default(0);
        //     $table->timestamps();

        //     $table->foreign('forwarder_id')->references('id')->on('mt_forwarder');
        //     $table->foreign('size_id')->references('id')->on('iv_container_size');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('mt_forwarder_size', function (Blueprint $table) {
        //     $table->dropForeign('mt_forwarder_size_forwarder_id_foreign');
        //     $table->dropForeign('mt_forwarder_size_size_id_foreign');
        // });

        // Schema::dropIfExists('mt_forwarder_size');
    }
}
