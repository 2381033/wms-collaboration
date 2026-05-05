<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCyInvoiceTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('cy_invoice_type', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger("company_id");
        //     $table->string("type_name", 50);
        //     $table->enum("invoice_flag", ["Yes", "No"])->default("No");
        //     $table->enum("free_flag", ["Yes", "No"])->default("No");
        //     $table->integer("free_storage")->default(0);
        //     $table->enum("active", ["Yes", "No"])->default("Yes");
        //     $table->timestamps();

        //     $table->foreign('company_id')->references('id')->on('mt_company');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('cy_invoice_type', function (Blueprint $table) {
        //     $table->dropForeign('cy_invoice_type_company_id_foreign');
        // });

        // Schema::dropIfExists('cy_invoice_type');
    }
}