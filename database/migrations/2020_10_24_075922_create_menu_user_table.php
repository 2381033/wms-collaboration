<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_menu_user', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('menu_id');
            $table->enum('akses', ['Yes', 'No'])->default('Yes');
            $table->enum('tambah', ['Yes', 'No'])->default('Yes');
            $table->enum('edit', ['Yes', 'No'])->default('Yes');
            $table->enum('hapus', ['Yes', 'No'])->default('Yes');
            $table->enum('cetak', ['Yes', 'No'])->default('Yes');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('menu_id')->references('id')->on('sm_menu');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sm_menu_user', function (Blueprint $table) {
            $table->dropForeign('sm_menu_user_user_id_foreign');
            $table->dropForeign('sm_menu_user_menu_id_foreign');
        });

        Schema::dropIfExists('sm_menu_user');
    }
}
