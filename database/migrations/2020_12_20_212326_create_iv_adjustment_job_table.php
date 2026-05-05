<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvAdjustmentJobTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iv_adjustment_job', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('adjust_no', 15);
            $table->dateTime('adjust_date');
            $table->unsignedBigInteger('type_id');
            $table->string('description', 150);
            $table->string('cycle_no', 15)->nullable();
            $table->enum('confirmed_flag', ['Yes', 'No'])->default('No');
            $table->string('confirmed_by', 20)->nullable();
            $table->dateTime('confirmed_date')->nullable();
            $table->string("user_id", 10)->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('mt_company');
            $table->foreign('type_id')->references('id')->on('iv_adjustment_type');
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
            $table->dropForeign('iv_adjustment_job_company_id_foreign');
            $table->dropForeign('iv_adjustment_job_type_id_foreign');
        });

        Schema::dropIfExists('iv_adjustment_job');
    }
}
