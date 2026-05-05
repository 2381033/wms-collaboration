<?php

namespace App\Models\Transaction\CY;

use Illuminate\Database\Eloquent\Model;

class ChecklistHeader extends Model
{
    protected $table = "cy_checklist_header";
    protected $fillable = [ 
        "id", 
        "branch_id", 
        "forwarder_id",
        "job_no",
        "job_date",
        "job_type",
        "driver_name",
        "vehicle_no",
        "size_id",
        "type_id",
        "container_no",
        "container_status",
        "inspected_by",
        "inspected_date",
        "sign_operation_name",
        "sign_operation_path",
        "sign_security_name",
        "sign_security_path",
        "sign_driver_name",
        "sign_driver_path",
        "confirmed_flag",
        "confirmed_by",
        "confirmed_date",
    ];
}