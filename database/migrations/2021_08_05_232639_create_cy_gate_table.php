<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCyGateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('cy_gate', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger("branch_id");
        //     $table->enum("gate_type", ["In", "Out"]);
        //     $table->string("vehicle_no", 50)->nullable();
        //     $table->string("driver_name", 50)->nullable();
        //     $table->string("container_no", 30)->nullable();
        //     $table->string("booking_no", 15)->nullable();
        //     $table->dateTime("gate_date");
        //     $table->dateTime("gate_in");
        //     $table->dateTime("gate_out")->nullable();
        //     $table->timestamps();

        //     $table->foreign('branch_id')->references('id')->on('mt_branch');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('cy_gate', function (Blueprint $table) {
        //     $table->dropForeign('cy_gate_branch_id_foreign');
        // });

        // Schema::dropIfExists('cy_gate');
    }
}