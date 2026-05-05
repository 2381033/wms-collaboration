<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvAdjustmentJobUploadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('iv_adjustment_job', function (Blueprint $table) {
            $table->string('filename')->nullable()->after("cycle_no");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('iv_adjustment_job', function (Blueprint $table) {
            $table->dropColumn('filename');
        });
    }
}
