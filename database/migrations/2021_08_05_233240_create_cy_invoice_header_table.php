<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCyInvoiceHeaderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('cy_invoice_header', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger("branch_id");
        //     $table->string("job_no", 15);
        //     $table->dateTime("job_date");
        //     $table->unsignedBigInteger("forwarder_id");            
        //     $table->unsignedBigInteger("forwarder_payment");
        //     $table->decimal("amount", 18, 3)->default(0);
        //     $table->decimal("adm_amount", 18, 3)->default(0);        
        //     $table->enum("tax_flag", ["Yes", "No"])->default("Yes");    
        //     $table->decimal("tax_amount", 18, 3)->default(0);   
        //     $table->decimal("invoice_amount", 18, 3)->default(0);
        //     $table->enum("review_flag", ["No", "Yes"])->default("Yes"); 
        //     $table->enum("confirmed_flag", ["Open", "Cancel", "Confirmed"])->default("Open");          
        //     $table->string("confirmed_by", 10)->nullable();
        //     $table->dateTime("confirmed_date")->nullable();   
        //     $table->enum("payment_flag", ["No", "Yes"])->default("No");          
        //     $table->string("payment_by", 10)->nullable();
        //     $table->dateTime("payment_date")->nullable();   
        //     $table->decimal("payment_amount", 18, 3)->default(0);
        //     $table->string("user_id", 10)->nullable();
        //     $table->timestamps();

        //     $table->foreign('branch_id')->references('id')->on('mt_branch');
        //     $table->foreign('forwarder_id')->references('id')->on('mt_forwarder');
        //     $table->foreign('forwarder_payment')->references('id')->on('mt_forwarder');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('cy_invoice_header', function (Blueprint $table) {
        //     $table->dropForeign('cy_invoice_header_branch_id_foreign');
        //     $table->dropForeign('cy_invoice_header_forwarder_id_foreign');
        //     $table->dropForeign('cy_invoice_header_forwarder_payment_foreign');
        // });

        // Schema::dropIfExists('cy_invoice_header');
    }
}