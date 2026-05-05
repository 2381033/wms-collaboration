<?php

namespace App\Models\Transaction\CY;

use Illuminate\Database\Eloquent\Model;

class Inbound extends Model
{
    protected $table = "cy_inbound";
    protected $fillable = [ 
        "id", 
        "branch_id", 
        "forwarder_id",
        "job_no",
        "job_date",
        "booking_id",
        "booking_no",
        "reference_no",
        "driver_name",
        "vehicle_no",
        "size_id",
        "container_status",
        "container_no",
        "invoice_type",
        "confirmed_flag",
        "confirmed_by",
        "confirmed_date",
        "user_id"
    ];
}