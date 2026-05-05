<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvPrincipalVolumeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('iv_principal', function (Blueprint $table) {            
            $table->enum('volume_flag', ['Yes', 'No'])->default("No")->after('multi_level');
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
            $table->dropColumn('volume_flag');
        });
    }
}
