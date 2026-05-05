<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvHandlingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iv_handling', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('principal_id');
            $table->string('job_type', 5);
            $table->string('foc', 5)->nullable();
            $table->decimal('cpu', 18, 3)->default(0);
            $table->decimal('cpu_lowest', 18, 3)->default(0);
            $table->decimal('cpu_middle', 18, 3)->default(0);
            $table->decimal('cpu_ext', 18, 3)->default(0);
            $table->decimal('quota', 18, 3)->default(0);
            $table->string('foc_return', 5)->nullable();
            $table->decimal('cpu_return', 18, 3)->default(0);
            $table->decimal('quota_return', 18, 3)->default(0);
            $table->string('remarks', 150)->nullable();
            $table->enum('active', ['Yes', 'No'])->default('Yes');
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('mt_company');
            $table->foreign('principal_id')->references('id')->on('iv_principal');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('iv_handling', function (Blueprint $table) {
            $table->dropForeign('iv_handling_company_id_foreign');
            $table->dropForeign('iv_handling_principal_id_foreign');
        });

        Schema::dropIfExists('iv_handling');
    }
}
