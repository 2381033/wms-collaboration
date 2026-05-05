<?php

namespace App\Models\Transaction\CY;

use Illuminate\Database\Eloquent\Model;

class Gate extends Model
{
    protected $table = "cy_gate";
    protected $fillable = [ 
        "id", 
        "branch_id",
        "gate_type", 
        "vehicle_no",
        "driver_name",
        "container_no",
        "booking_no",
        "gate_date",
        "gate_in",
        "gate_out"
    ];
}