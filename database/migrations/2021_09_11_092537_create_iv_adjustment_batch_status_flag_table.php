<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvAdjustmentBatchStatusFlagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('iv_adjustment_batch', function (Blueprint $table) {
            $table->enum('status_flag', ["Yes", "No"])->default("No")->after('reference_no');
            $table->string('status_by', 10)->nullable()->after("status_flag");
            $table->dateTime('status_date')->nullable()->after("status_by");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('iv_adjustment_batch', function (Blueprint $table) {
            $table->dropColumn('status_flag');
            $table->dropColumn('status_by');
            $table->dropColumn('status_date');
        });
    }
}
