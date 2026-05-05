<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFmDocumentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fm_document', function (Blueprint $table) {
            $table->id();
            $table->string("document_name", 50);
            $table->integer("alert_1")->default(0);
            $table->integer("alert_2")->default(0);
            $table->integer("alert_3")->default(0);
            $table->integer("alert_4")->default(0);
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
        Schema::dropIfExists('fm_document');
    }
}
