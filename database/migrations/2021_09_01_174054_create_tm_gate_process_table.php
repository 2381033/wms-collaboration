<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTmGateProcessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tm_gate_process', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("gate_id");
            $table->unsignedBigInteger("site_id");
            $table->dateTime("gate_in")->nullable();
            $table->string("gate_in_by", 10)->nullable();
            $table->enum("check_flag", ["Yes", "No"])->default("No");
            $table->dateTime("check_date")->nullable();
            $table->string("check_by", 10)->nullable();
            $table->dateTime("process_start")->nullable();
            $table->string("process_start_by", 10)->nullable();
            $table->dateTime("process_finish")->nullable();
            $table->string("process_finish_by", 10)->nullable();
            $table->dateTime("gate_out")->nullable();
            $table->string("gate_out_by", 10)->nullable();
            $table->timestamps();

            $table->foreign('gate_id')->references('id')->on('tm_gate');
            $table->foreign('site_id')->references('id')->on('iv_site');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tm_gate_process', function (Blueprint $table) {
            $table->dropForeign('tm_gate_process_gate_id_foreign');
            $table->dropForeign('tm_gate_process_site_id_foreign');
        });

        Schema::dropIfExists('tm_gate_process');
    }
}
