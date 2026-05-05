<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExOutboundOrderShipperTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ex_outbound_order', function (Blueprint $table) {                
            $table->unsignedBigInteger('shipper_id')->nullable()->after('consignee_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ex_outbound_order', function (Blueprint $table) {
            $table->dropColumn('shipper_id');
        });
    }
}
