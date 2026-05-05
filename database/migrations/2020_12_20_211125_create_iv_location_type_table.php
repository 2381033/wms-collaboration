<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvLocationTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iv_location_type', function (Blueprint $table) {
            $table->id();
            $table->string('description', 50);
            $table->decimal('cbm', 18,6)->default(0);
            $table->decimal('weight', 18,6)->default(0);
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
        Schema::dropIfExists('iv_location_type');
    }
}
