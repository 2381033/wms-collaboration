<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCyChecklistDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('cy_checklist_detail', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger("checklist_id");
        //     $table->unsignedBigInteger("check_id");
        //     $table->string("remarks")->nullable();
        //     $table->string("filename")->nullable();
        //     $table->string("path")->nullable();
        //     $table->timestamps();

        //     $table->foreign('checklist_id')->references('id')->on('cy_checklist_header');
        //     $table->foreign('check_id')->references('id')->on('cy_checklist');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('cy_checklist_detail', function (Blueprint $table) {
        //     $table->dropForeign('cy_checklist_detail_checklist_id_foreign');
        //     $table->dropForeign('cy_checklist_detail_check_id_foreign');
        // });

        // Schema::dropIfExists('cy_checklist_detail');
    }
}