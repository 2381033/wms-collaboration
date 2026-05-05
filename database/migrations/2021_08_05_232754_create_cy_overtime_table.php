<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCyOvertimeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cy_overtime', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("branch_id");
            $table->unsignedBigInteger("forwarder_id");
            $table->dateTime("overtime_date");
            $table->string("job_no", 15);
            $table->dateTime("overtime_start");
            $table->dateTime("overtime_finish");
            $table->decimal("duration", 5, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cy_overtime');
    }
}