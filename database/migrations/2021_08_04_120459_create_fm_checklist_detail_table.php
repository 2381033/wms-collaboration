<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFmChecklistDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fm_checklist_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("check_id");
            $table->unsignedBigInteger("group_id");
            $table->unsignedBigInteger("item_id");
            $table->enum("item_type", ["Expired", "Remarks", "Action"])->nullable();
            $table->enum("results_flag", ["Yes", "No"])->nullable();
            $table->enum("action_flag", ["Proper", "Less", "Alert"])->nullable();
            $table->string("remarks")->nullable();
            $table->timestamps();

            $table->foreign('check_id')->references('id')->on('fm_checklist_header');
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
        Schema::table('fm_checklist_detail', function (Blueprint $table) {
            $table->dropForeign('fm_checklist_detail_check_id_foreign');
            $table->dropForeign('fm_checklist_detail_group_id_foreign');
            $table->dropForeign('fm_checklist_detail_item_id_foreign');
        });
        
        Schema::dropIfExists('fm_checklist_detail');
    }
}