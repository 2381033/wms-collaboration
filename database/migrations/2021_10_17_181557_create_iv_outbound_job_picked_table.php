<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvOutboundJobPickedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('iv_outbound_job', function (Blueprint $table) {                
            $table->enum("loading_flag", ["Yes", "No"])->default("No")->after('allocated_date');
            $table->string("loading_by", 10)->nullable()->after('loading_flag');
            $table->dateTime("loading_date")->nullable()->after('loading_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('iv_outbound_job', function (Blueprint $table) {
            $table->dropColumn('loading_flag');
            $table->dropColumn('loading_by');
            $table->dropColumn('loading_date');
        });
    }
}
