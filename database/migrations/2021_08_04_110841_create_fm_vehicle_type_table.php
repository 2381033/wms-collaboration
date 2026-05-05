<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFmVehicleTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fm_vehicle_type', function (Blueprint $table) {
            $table->id();
            $table->string("vehicle_type", 20);
            $table->string("description", 50);
            $table->decimal("cbm", 18, 6)->default(0);
            $table->decimal("weight", 18, 6)->default(0);
            $table->integer("pallet_count")->default(0);
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
        Schema::dropIfExists('fm_vehicle_type');
    }
}
