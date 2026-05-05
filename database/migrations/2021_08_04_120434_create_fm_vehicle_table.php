<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFmVehicleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fm_vehicle', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("branch_id");
            $table->string("vehicle_code", 20);
            $table->string("vehicle_no", 20);
            $table->unsignedBigInteger("type_id");
            $table->enum("ownership", ["Yes", "No"])->default("Yes");
            $table->unsignedBigInteger("driver_id")->nullable();
            $table->enum("status_code", ["Available", "Running", "Maintenance"])->default("Available");
            $table->enum("active", ["Yes", "No"])->default("Yes");
            $table->timestamps();

            $table->foreign("branch_id")->references("id")->on("mt_branch");
            $table->foreign("type_id")->references("id")->on("fm_vehicle_type");
            $table->foreign("driver_id")->references("id")->on("fm_driver");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fm_vehicle', function (Blueprint $table) {
            $table->dropForeign('fm_vehicle_branch_id_foreign');
            $table->dropForeign('fm_vehicle_type_id_foreign');
            $table->dropForeign('fm_vehicle_driver_id_foreign');
        });

        Schema::dropIfExists('fm_vehicle');
    }
}