<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIvProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iv_product', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('principal_id');
            $table->string('product_code', 30);
            $table->string('product_name', 250);
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('group_id')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->enum('pick_criteria', ['FEFO', 'FIFO', 'LIFO', 'LEFO', 'FMFO', 'BATCH', 'DOCREF'])->nullable();            
            $table->enum('unit_level', [1, 2, 3])->default(1);
            $table->string('puom', 5);
            $table->string('muom', 5);
            $table->string('buom', 5);
            $table->integer('uppp')->default(0);
            $table->integer('muppp')->default(0);
            $table->unsignedBigInteger('manufactur_id')->nullable();    
            $table->enum('batch_flag', ['Yes', 'No'])->default('No');
            $table->enum('expired_flag', ['Yes', 'No'])->default('No');    
            $table->enum('freeze_flag', ['Yes', 'No'])->default('No');        
            $table->decimal('length', 18, 3)->default(0);
            $table->decimal('width', 18, 3)->default(0);            
            $table->decimal('height', 18, 3)->default(0);
            $table->string('dimensions_unit', 5)->nullable(); 
            $table->decimal('volume', 18, 3)->default(0);
            $table->string('volume_unit', 5)->nullable();
            $table->decimal('gross_weight', 18, 3)->default(0);
            $table->decimal('net_weight', 18, 3)->default(0);
            $table->string('weight_unit', 5)->nullable();
            $table->decimal('temperature', 5, 2)->default(0);
            $table->integer('shelf_life')->default(0);
            $table->integer('freeze_day')->default(0);
            $table->decimal('base_price', 18, 3)->default(0);
            $table->enum('active', ['Yes', 'No'])->default('Yes');
            $table->string("user_id", 10)->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('mt_company');
            $table->foreign('principal_id')->references('id')->on('iv_principal');
            $table->foreign('category_id')->references('id')->on('iv_product_category');
            $table->foreign('group_id')->references('id')->on('iv_product_group');
            $table->foreign('brand_id')->references('id')->on('iv_product_brand');
            $table->foreign('manufactur_id')->references('id')->on('iv_manufactur');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('iv_product', function (Blueprint $table) {
            $table->dropForeign('iv_product_company_id_foreign');
            $table->dropForeign('iv_product_principal_id_foreign');
            $table->dropForeign('iv_product_category_id_foreign');
            $table->dropForeign('iv_product_group_id_foreign');
            $table->dropForeign('iv_product_brand_id_foreign');
            $table->dropForeign('iv_product_manufactur_id_foreign');
        });

        Schema::dropIfExists('iv_product');
    }
}
