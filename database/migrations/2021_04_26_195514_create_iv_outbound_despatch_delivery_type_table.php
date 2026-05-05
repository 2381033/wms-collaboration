<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvOutboundDespatchDeliveryTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('iv_outbound_despatch', function (Blueprint $table) {            
            $table->enum('delivery_type', ['Reguler', 'Ritase', 'Lumpsum'])->default('reguler')->after('send_date_doc');
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
            $table->dropColumn('delivery_type');
        });
    }
}
