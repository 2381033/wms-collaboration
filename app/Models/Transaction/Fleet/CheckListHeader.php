<?php

namespace App\Models\Transaction\Fleet;

use Illuminate\Database\Eloquent\Model;

class CheckListHeader extends Model
{
    protected $table = "fm_checklist_header";
    protected $fillable = [ 
        "id", 
        "branch_id",
        "job_no",
        "job_date",
        "job_type",
        "size_id",
        "type_id",
        "vehicle_no",
        "driver_name",
        "phone_no",
        "seal_no",
        "container_no",
        "inspection_date",
        "vendor_name",
        "remarks",
        "km_start",
        "km_end",
        "sign_security_name",
        "sign_security_path",
        "sign_driver_name",
        "sign_driver_path",
    ];
}