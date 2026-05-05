<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRtRegionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rt_region', function (Blueprint $table) {
            $table->id();
            $table->string('country_code', 3);
            $table->string('region_code', 20)->unique();
            $table->string('region_name', 100);
            $table->enum('active', ['Yes', 'No'])->default('Yes');
            $table->timestamps();

            $table->foreign('country_code')->references('country_code')->on('rt_country');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rt_region', function (Blueprint $table) {
            $table->dropForeign('rt_region_country_code_foreign');
        });

        Schema::dropIfExists('rt_region');
    }
}
