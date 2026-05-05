<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMtForwarderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('mt_forwarder', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('forwarder_name', 200);
        //     $table->decimal("storage_amount", 18, 2)->default(0);
        //     $table->decimal("adm_amount", 18, 2)->default(0);
        //     $table->enum("active", ["Yes", "No"])->default("Yes");
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('mt_forwarder');
    }
}
