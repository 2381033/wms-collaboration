<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFmChecklistHeaderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fm_checklist_header', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("branch_id");
            $table->string("job_no", 15);
            $table->dateTime("job_date");
            $table->enum("job_type", ["Inbound", "Outbound"])->nullable();
            $table->unsignedBigInteger("size_id")->nullable();
            $table->unsignedBigInteger("type_id")->nullable();
            $table->unsignedBigInteger("driver_id")->nullable();
            $table->string("driver_name", 50)->nullable();
            $table->string("vehicle_no", 50)->nullable();
            $table->string("phone_no", 50)->nullable();
            $table->string("seal_no", 50)->nullable();
            $table->string("container_no", 50)->nullable();
            $table->dateTime("inspection_date")->nullable();
            $table->string("vendor_name")->nullable();
            $table->string("remarks")->nullable();
            $table->decimal("km_start", 18, 2)->default(0);
            $table->decimal("km_end", 18, 2)->default(0);
            $table->string("sign_security_name")->nullable();
            $table->string("sign_security_path")->nullable();
            $table->string("sign_driver_name")->nullable();
            $table->string("sign_driver_path")->nullable();
            $table->timestamps();

            $table->foreign("branch_id")->references("id")->on("mt_branch");
            $table->foreign('size_id')->references('id')->on('iv_container_size');
            $table->foreign('type_id')->references('id')->on('iv_container_type');
            $table->foreign('driver_id')->references('id')->on('fm_driver');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fm_checklist_header', function (Blueprint $table) {
            $table->dropForeign('fm_checklist_header_branch_id_foreign');
            $table->dropForeign('fm_checklist_header_size_id_foreign');
            $table->dropForeign('fm_checklist_header_type_id_foreign');
            $table->dropForeign('fm_checklist_header_driver_id_foreign');
        });

        Schema::dropIfExists('fm_checklist_header');
    }
}