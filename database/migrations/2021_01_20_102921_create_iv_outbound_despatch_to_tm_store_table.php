<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvOutboundDespatchToTmStoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('iv_outbound_despatch', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->nullable()->after('send_date_doc');
            $table->foreign('store_id')->references('id')->on('tm_store');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('iv_outbound_despatch', function (Blueprint $table) {
            $table->dropForeign('iv_outbound_despatch_store_id_foreign');
            $table->dropColumn('store_id');
        });
    }
}
