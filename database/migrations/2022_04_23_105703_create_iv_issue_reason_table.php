<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvIssueReasonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iv_issue_reason', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("principal_id");
            $table->string('job_no', 15);
            $table->dateTime('job_date');
            $table->unsignedBigInteger("outbound_id");
            $table->string("order_no", 30);
            $table->integer("rating");
            $table->unsignedBigInteger("issue_id")->nullable();
            $table->string("notes")->nullable();
            $table->string("user_id", 10)->nullable();
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
        Schema::dropIfExists('iv_issue_reason');
    }
}