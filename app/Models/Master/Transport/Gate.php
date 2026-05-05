<?php

namespace App\Models\Master\Transport;

use Illuminate\Database\Eloquent\Model;

class Gate extends Model
{
    protected $table = "tm_gate";
    protected $fillable = [ 
        "id", 
        "job_no",
        "job_date",
        "principal_id",
        "gate_type",
        "vendor_id",
        "size_id",
        "type_id",
        "vehicle_no",
        "container_no",
        "seal_no",
        "driver_name",        
        "phone",
        "pick_flag",
        "document_no",
        "dispatch_date",
        "status_flag",
        "user_id",
        "active" 
    ];
}