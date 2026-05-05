<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvStorageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iv_storage', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('principal_id');
            $table->string('foc', 5)->nullable();
            $table->string('currency_code', 5);
            $table->decimal('cpu', 18, 3)->default(0);
            $table->decimal('cpu_add', 18, 3)->default(0);
            $table->decimal('quota', 18, 3)->default(0);
            $table->decimal('cpu_ext', 18, 3)->default(0);
            $table->decimal('flat_rate', 18, 3)->default(0);
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
        Schema::table('iv_storage', function (Blueprint $table) {
            $table->dropForeign('iv_storage_company_id_foreign');
            $table->dropForeign('iv_storage_principal_id_foreign');
        });

        Schema::dropIfExists('iv_storage');
    }
}
