<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCyPaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cy_payment', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("branch_id");
            $table->string("job_no", 15);
            $table->dateTime("job_date");
            $table->unsignedBigInteger("forwarder_id");
            $table->decimal("invoice_amount", 18, 3)->default(0);
            $table->decimal("payment_amount", 18, 3)->default(0);
            $table->dateTime("payment_date");
            $table->enum("confirmed_flag", ["Confirmed", "Open"])->default("Open");          
            $table->string("confirmed_by", 10)->nullable();
            $table->dateTime("confirmed_date")->nullable();   
            $table->string("user_id", 10)->nullable();
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('mt_branch');
            $table->foreign('forwarder_id')->references('id')->on('mt_forwarder');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cy_payment', function (Blueprint $table) {
            $table->dropForeign('cy_payment_branch_id_foreign');
            $table->dropForeign('cy_payment_forwarder_id_foreign');
        });

        Schema::dropIfExists('cy_payment');
    }
}