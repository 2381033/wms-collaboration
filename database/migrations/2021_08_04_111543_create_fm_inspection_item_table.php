<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFmInspectionItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fm_inspection_item', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("group_id");
            $table->string("item_name", 100);
            $table->enum("item_type", ["Expired", "Remarks", "Action"])->nullable();
            $table->enum("active", ["Yes", "No"])->default("Yes");
            $table->timestamps();

            $table->foreign('group_id')->references('id')->on('fm_inspection_group');            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fm_inspection_item', function (Blueprint $table) {
            $table->dropForeign('fm_inspection_item_group_id_foreign');        
        });

        Schema::dropIfExists('fm_inspection_item');
    }
}
