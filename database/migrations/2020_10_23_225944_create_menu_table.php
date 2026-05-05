<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_menu', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);            
            $table->integer('parent_id');
            $table->string('url', 100)->nullable();  
            $table->string('icon', 50)->nullable();     
            $table->integer('sort_order')->default(0);
            $table->enum('active', ['Yes', 'No'])->default('Yes'); 
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
        Schema::dropIfExists('sm_menu');
    }
}
