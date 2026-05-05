<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvPrincipalBranchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iv_principal_branch', function (Blueprint $table) {
            $table->unsignedBigInteger('principal_id');
            $table->unsignedBigInteger('branch_id');
            $table->timestamps();

            $table->foreign('principal_id')->references('id')->on('iv_principal');
            $table->foreign('branch_id')->references('id')->on('mt_branch');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('iv_principal_branch', function (Blueprint $table) {
            $table->dropForeign('iv_principal_branch_principal_id_foreign');
            $table->dropForeign('iv_principal_branch_branch_id_foreign');
        });

        Schema::dropIfExists('iv_principal_branch');
    }
}