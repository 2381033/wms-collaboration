<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFmDriverTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fm_driver', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("branch_id");
            $table->string("driver_name", 100);
            $table->string("phone", 20)->nullable();
            $table->dateTime("join_date")->nullable();
            $table->string("sim_no", 20)->nullable();
            $table->dateTime("sim_date")->nullable();
            $table->enum("active", ["Yes", "No"])->default("Yes");
            $table->timestamps();

            $table->foreign("branch_id")->references("id")->on("mt_branch");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fm_driver', function (Blueprint $table) {
            $table->dropForeign('fm_driver_branch_id_foreign');
        });

        Schema::dropIfExists('fm_driver');
    }
}
