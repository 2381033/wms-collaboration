<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvEmailPrincipalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('iv_email_principal', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger("company_id");
        //     $table->unsignedBigInteger("principal_id");
        //     $table->string("description", 100);
        //     $table->string("subject", 200);
        //     $table->text("email_to");
        //     $table->text("email_cc");
        //     $table->text("email_bcc");
        //     $table->enum("active", ["Yes", "No"])->default("Yes");
        //     $table->timestamps();

        //     $table->foreign('company_id')->references('id')->on('mt_company');
        //     $table->foreign('principal_id')->references('id')->on('iv_principal');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('iv_email_principal', function (Blueprint $table) {
        //     $table->dropForeign('iv_email_principal_company_id_foreign');
        //     $table->dropForeign('iv_email_principal_principal_id_foreign');
        // });

        // Schema::dropIfExists('iv_email_principal');
    }
}
