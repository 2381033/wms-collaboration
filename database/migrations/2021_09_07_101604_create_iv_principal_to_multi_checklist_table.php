<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvPrincipalToMultiChecklistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('iv_principal', function (Blueprint $table) {
            $table->enum('multi_checklist', ["Yes", "No"])->default("Yes")->after('multi_level');
            $table->integer('minimum_point')->default(0)->after('multi_checklist');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('iv_principal', function (Blueprint $table) {
            $table->dropColumn('multi_checklist');
        });
    }
}
