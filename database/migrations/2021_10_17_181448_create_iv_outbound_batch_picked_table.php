<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvOutboundBatchPickedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('iv_outbound_batch', function (Blueprint $table) {                
            $table->enum("pick_flag", ["Yes", "No"])->default("No")->after('pallet_qty');
            $table->string("pick_by", 10)->nullable()->after('pick_flag');
            $table->dateTime("pick_date")->nullable()->after('pick_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('iv_outbound_batch', function (Blueprint $table) {
            $table->dropColumn('pick_flag');
            $table->dropColumn('pick_by');
            $table->dropColumn('pick_date');
        });
    }
}