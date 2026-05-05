<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvOccupancyDailyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('iv_occupancy_daily', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger("company_id");
        //     $table->unsignedBigInteger("principal_id");
        //     $table->datetime("transaction_date");
        //     $table->string("status_code", 1);
        //     $table->integer("qty")->default(0);
        //     $table->timestamps();

        //     $table->foreign("company_id")->references("id")->on("mt_company");
        //     $table->foreign("principal_id")->references("id")->on("iv_principal");
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table("iv_occupancy_daily", function (Blueprint $table) {
        //     $table->dropForeign("iv_occupancy_daily_company_id_foreign");
        //     $table->dropForeign("iv_occupancy_daily_principal_id_foreign");
        // });
        
        // Schema::dropIfExists('iv_occupancy_daily');
    }
}