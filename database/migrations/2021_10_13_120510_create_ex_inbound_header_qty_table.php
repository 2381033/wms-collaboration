<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExInboundHeaderQtyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ex_inbound_header', function (Blueprint $table) {                
            $table->integer('qty_actual')->default(0)->after('qty_cargo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ex_inbound_header', function (Blueprint $table) {
            $table->dropColumn('qty_actual');
        });
    }
}
