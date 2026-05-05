<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTmGateChecklistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tm_gate_checklist', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("gate_id");
            $table->unsignedBigInteger("process_id")->nullable();
            $table->unsignedBigInteger("group_id");
            $table->unsignedBigInteger("item_id");
            $table->enum("item_type", ["Expired", "Remarks", "Action"])->nullable();
            $table->enum("results_flag", ["Yes", "No"])->nullable();
            $table->enum("action_flag", ["Proper", "Less", "Alert"])->nullable();
            $table->string("remarks")->nullable();
            $table->enum("status_flag", ["Yes", "No"])->default("No");
            $table->string("user_id", 10)->nullable();
            $table->timestamps();

            $table->foreign('gate_id')->references('id')->on('tm_gate');
            // $table->foreign('process_id')->references('id')->on('tm_gate_process');
            $table->foreign('group_id')->references('id')->on('fm_inspection_group');
            $table->foreign('item_id')->references('id')->on('fm_inspection_item');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tm_gate_checklist', function (Blueprint $table) {
            $table->dropForeign('tm_gate_checklist_gate_id_foreign');
            // $table->dropForeign('tm_gate_checklist_process_id_foreign');
            $table->dropForeign('tm_gate_checklist_group_id_foreign');
            $table->dropForeign('tm_gate_checklist_item_id_foreign');
        });

        Schema::dropIfExists('tm_gate_checklist');
    }
}