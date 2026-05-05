<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTmVendorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tm_vendor', function (Blueprint $table) {
            $table->id();
            $table->string("vendor_code", 10);
            $table->string("vendor_name", 50);
            $table->enum("active", ["Yes", "No"])->default("Yes");
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
        Schema::dropIfExists('tm_vendor');
    }
}
