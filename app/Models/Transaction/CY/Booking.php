<?php

namespace App\Models\Transaction\CY;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = "cy_booking";
    protected $fillable = [ 
        "id", 
        "branch_id", 
        "booking_no",
        "booking_date",
        "forwarder_id",
        "reference_no",
        "driver_name",
        "vehicle_no",
        "invoice_type",
        "size_id",
        "type_id",
        "container_status",
        "container_no",
        "status_flag",
        "user_id"
    ];
}