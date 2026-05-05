<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCyPaymentDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cy_payment_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("payment_id");
            $table->unsignedBigInteger("invoice_id");
            $table->string("invoice_no", 15);
            $table->decimal("invoice_amount", 18, 3)->default(0);
            $table->decimal("payment_amount", 18, 3)->default(0);
            $table->string("user_id", 10)->nullable();
            $table->timestamps();
            
            $table->foreign('payment_id')->references('id')->on('cy_payment');
            $table->foreign('invoice_id')->references('id')->on('cy_invoice_header');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cy_payment_detail', function (Blueprint $table) {
            $table->dropForeign('cy_payment_detail_payment_id_foreign');
            $table->dropForeign('cy_payment_detail_invoice_id_foreign');
        });

        Schema::dropIfExists('cy_payment_detail');
    }
}
