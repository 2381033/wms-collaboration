<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCyChecklistHeaderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('cy_checklist_header', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger("branch_id");
        //     $table->unsignedBigInteger("forwarder_id");
        //     $table->string("job_no", 15)->nullable();
        //     $table->dateTime("job_date")->nullable();   
        //     $table->enum("job_type", ["Inbound", "Outbound"])->nullable();
        //     $table->string("driver_name", 50)->nullable();
        //     $table->string("vehicle_no", 50)->nullable();
        //     $table->unsignedBigInteger('size_id')->nullable();
        //     $table->unsignedBigInteger("type_id")->nullable();
        //     $table->string("container_no", 30)->nullable();
        //     $table->enum("container_status", ["Empty", "Full"])->nullable();
        //     $table->string("inspected_by", 50)->nullable();
        //     $table->dateTime("inspected_date")->nullable();
        //     $table->enum("confirmed_flag", ["Yes", "No"])->default("No");          
        //     $table->string("confirmed_by", 10)->nullable();
        //     $table->dateTime("confirmed_date")->nullable(); 
        //     $table->string("sign_operation_name")->nullable();
        //     $table->string("sign_operation_path")->nullable();  
        //     $table->string("sign_security_name")->nullable();
        //     $table->string("sign_security_path")->nullable();
        //     $table->string("sign_driver_name")->nullable();
        //     $table->string("sign_driver_path")->nullable();
        //     $table->timestamps();

        //     $table->foreign('branch_id')->references('id')->on('mt_branch');
        //     $table->foreign('forwarder_id')->references('id')->on('mt_forwarder');
        //     $table->foreign('size_id')->references('id')->on('iv_container_size');
        //     $table->foreign('type_id')->references('id')->on('iv_container_type');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('cy_checklist_header', function (Blueprint $table) {
        //     $table->dropForeign('cy_checklist_header_branch_id_foreign');
        //     $table->dropForeign('cy_checklist_header_forwarder_id_foreign');
        //     $table->dropForeign('cy_checklist_header_size_id_foreign');
        //     $table->dropForeign('cy_checklist_header_type_id_foreign');
        // });

        // Schema::dropIfExists('cy_checklist_header');
    }
}