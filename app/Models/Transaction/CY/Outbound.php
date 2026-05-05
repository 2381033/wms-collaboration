<?php

namespace App\Models\Transaction\CY;

use Illuminate\Database\Eloquent\Model;

class Outbound extends Model
{
    protected $table = "cy_outbound";
    protected $fillable = [ 
        "id", 
        "branch_id", 
        "job_no",
        "job_date",
        "forwarder_id",
        "serial_id",
        "serial_no",
        "driver_name",
        "vehicle_no",
        "invoice_type",
        "size_id",
        "container_no",
        "received_date",
        "dispatch_date",
        "leadtime",
        "lolo_amount",
        "storage_amount",
        "total_amount",
        "confirmed_flag",
        "confirmed_by",
        "confirmed_date",
        "invoice_no",
        "invoice_flag",
        "invoice_by",
        "invoice_date",
        "user_id"
    ];
}