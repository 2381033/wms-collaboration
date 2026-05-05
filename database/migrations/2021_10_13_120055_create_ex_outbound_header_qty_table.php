<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExOutboundHeaderQtyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ex_outbound_header', function (Blueprint $table) {    
            $table->string('remarks')->nullable()->after('total_pallet');
            $table->enum('error_flag', ["No", "Yes"])->default("No")->after('remarks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ex_outbound_header', function (Blueprint $table) {
            $table->dropColumn('remarks');
            $table->dropColumn('error_flag');
        });
    }
}
