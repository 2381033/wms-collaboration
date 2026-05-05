<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTmGateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tm_gate', function (Blueprint $table) {
            $table->id();
            $table->string("job_no", 15);
            $table->dateTime("job_date");
            $table->unsignedBigInteger("principal_id");            
            $table->enum("gate_type", ["Inbound", "Outbound"]);
            $table->unsignedBigInteger("vendor_id");
            $table->string("driver_name", 50);
            $table->string("phone", 50)->nullable();
            $table->unsignedBigInteger("size_id");
            $table->unsignedBigInteger("type_id");
            $table->string("vehicle_no", 30);
            $table->string("container_no", 30);
            $table->string("seal_no", 30);
            $table->enum("pick_flag", ["Single", "Multi"]);
            $table->string("document_no", 30)->nullable();
            $table->dateTime("dispatch_date")->nullable();
            $table->enum("status_flag", ["Yes", "No"])->default("No");
            $table->string('status_by', 10)->nullable();
            $table->dateTime('status_date')->nullable();
            $table->string("user_id", 10)->nullable();
            $table->string("sign_operation_name")->nullable();
            $table->string("sign_operation_path")->nullable();  
            $table->string("sign_security_name")->nullable();
            $table->string("sign_security_path")->nullable();
            $table->string("sign_driver_name")->nullable();
            $table->string("sign_driver_path")->nullable();
            $table->timestamps();

            $table->foreign('principal_id')->references('id')->on('iv_principal');
            $table->foreign('vendor_id')->references('id')->on('tm_vendor');
            $table->foreign('size_id')->references('id')->on('iv_container_size');
            $table->foreign('type_id')->references('id')->on('iv_container_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tm_gate', function (Blueprint $table) {
            $table->dropForeign('tm_gate_principal_id_foreign');
            $table->dropForeign('tm_gate_vendor_id_foreign');
            $table->dropForeign('tm_gate_size_id_foreign');
            $table->dropForeign('tm_gate_type_id_foreign');
        });

        Schema::dropIfExists('tm_gate');
    }
}
