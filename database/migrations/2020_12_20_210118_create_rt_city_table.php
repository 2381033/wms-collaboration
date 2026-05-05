<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRtCityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rt_city', function (Blueprint $table) {
            $table->id();
            $table->string('country_code', 3);
            $table->string('region_code', 20);
            $table->string('city_code', 20)->unique();
            $table->string('city_name', 100);
            $table->enum('active', ['Yes', 'No'])->default('Yes');
            $table->timestamps();

            $table->foreign('country_code')->references('country_code')->on('rt_country');
            $table->foreign('region_code')->references('region_code')->on('rt_region');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return vocode
     */
    public function down()
    {
        Schema::table('rt_city', function (Blueprint $table) {
            $table->dropForeign('rt_city_country_code_foreign');
            $table->dropForeign('rt_city_region_code_foreign');
        });

        Schema::dropIfExists('rt_city');
    }
}
