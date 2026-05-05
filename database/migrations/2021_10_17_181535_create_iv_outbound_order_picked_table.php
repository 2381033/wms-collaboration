<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvOutboundOrderPickedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('iv_outbound_order', function (Blueprint $table) {                
            $table->enum("pick_flag", ["Yes", "No"])->default("No")->after('due_date');
            $table->string("pick_by", 10)->nullable()->after('pick_flag');
            $table->dateTime("pick_date")->nullable()->after('pick_by');
            $table->string("confirmed_by", 10)->nullable()->after('confirmed_flag');
            $table->dateTime("confirmed_date")->nullable()->after('confirmed_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('iv_outbound_order', function (Blueprint $table) {
            $table->dropColumn('pick_flag');
            $table->dropColumn('pick_by');
            $table->dropColumn('pick_date');
            $table->dropColumn('confirmed_by');
            $table->dropColumn('confirmed_date');
        });
    }
}