<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateIvStockLedgerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iv_stock_ledger', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('principal_id');
            $table->string('serial_no', 15);
            $table->string('srno', 15)->nullable();
            $table->string('job_no', 15);
            $table->dateTime('job_date');
            $table->string('vehicle_no', 30)->nullable();
            $table->integer('line_no')->default(0);
            $table->unsignedBigInteger('product_id');
            $table->string('product_code', 30)->nullable();
            $table->string('po_number', 30)->nullable();
            $table->string('lot_no', 30)->nullable();
            $table->string('document_ref', 30)->nullable();
            $table->dateTime('mfg_date')->nullable();
            $table->dateTime('exp_date')->nullable();
            $table->unsignedBigInteger('manufactur_id')->nullable();
            $table->unsignedBigInteger('status_id')->nullable();
            $table->unsignedBigInteger('site_id')->nullable();
            $table->unsignedBigInteger('area_id')->nullable();
            $table->unsignedBigInteger('location_id')->nullable();
            $table->string('location_code', 15)->nullable();
            $table->string('puom', 5)->nullable();
            $table->string('muom', 5)->nullable();
            $table->string('buom', 5)->nullable();
            $table->integer('uppp')->default(0);
            $table->integer('muppp')->default(0);
            $table->integer('pqty')->default(0);
            $table->integer('mqty')->default(0);
            $table->integer('bqty')->default(0);
            $table->integer('qtyr')->default(0);
            $table->integer('qtys')->default(0);
            $table->integer('qtya')->default(0);
            $table->integer('qtyp')->default(0);
            $table->integer('pallet_qty')->default(0);
            $table->decimal('base_unit', 18, 3)->default(0);
            $table->string('reference_no', 15)->nullable();
            $table->enum('freeze_flag', ['Yes', 'No'])->default('No');
            $table->string('freeze_by', 20)->nullable();
            $table->dateTime('freeze_date')->nullable();
            $table->string('freeze_reason', 150)->nullable();
            $table->string("user_id", 10)->nullable();
            $table->timestamps();            

            $table->foreign('company_id')->references('id')->on('mt_company');
            $table->foreign('principal_id')->references('id')->on('iv_principal');
            $table->foreign('product_id')->references('id')->on('iv_product');
            $table->foreign('site_id')->references('id')->on('iv_site');
            $table->foreign('area_id')->references('id')->on('iv_site_area');
            $table->foreign('location_id')->references('id')->on('iv_location');
            $table->foreign('status_id')->references('id')->on('iv_stock_status');
        });

        DB::statement('ALTER TABLE iv_stock_ledger ADD CONSTRAINT chk_qtys CHECK (qtys >= 0);');
        DB::statement('ALTER TABLE iv_stock_ledger ADD CONSTRAINT chk_qtya CHECK (qtya >= 0);');
        DB::statement('ALTER TABLE iv_stock_ledger ADD CONSTRAINT chk_qtyp CHECK (qtyp >= 0);');
        DB::statement('ALTER TABLE iv_stock_ledger ADD CONSTRAINT chk_qty CHECK (qtys = qtya + qtyp);');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('iv_stock_ledger', function (Blueprint $table) {
            $table->dropForeign('iv_stock_ledger_company_id_foreign');
            $table->dropForeign('iv_stock_ledger_principal_id_foreign');
            $table->dropForeign('iv_stock_ledger_product_id_foreign');
            $table->dropForeign('iv_stock_ledger_manufactur_id_foreign');
            $table->dropForeign('iv_stock_ledger_site_id_foreign');
            $table->dropForeign('iv_stock_ledger_area_id_foreign');
            $table->dropForeign('iv_stock_ledger_location_id_foreign');
            $table->dropForeign('iv_stock_ledger_status_id_foreign');
        });

        Schema::dropIfExists('iv_stock_ledger');
    }
}
