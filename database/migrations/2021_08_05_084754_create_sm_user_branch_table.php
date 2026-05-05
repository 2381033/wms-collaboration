<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmUserBranchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('sm_user_branch', function (Blueprint $table) {
        //     $table->unsignedBigInteger('user_id');
        //     $table->unsignedBigInteger('branch_id');            
        //     $table->timestamps();

        //     $table->foreign('user_id')->references('id')->on('users');
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
        // Schema::table('sm_user_branch', function (Blueprint $table) {
        //     $table->dropForeign('sm_user_branch_user_id_foreign');
        //     $table->dropForeign('sm_user_branch_branch_id_foreign');
        // });

        // Schema::dropIfExists('sm_user_branch');
    }
}
