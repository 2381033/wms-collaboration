<?php

namespace App\Models\Transaction\CY;

use Illuminate\Database\Eloquent\Model;

class Overtime extends Model
{
    protected $table = "cy_overtime";
    protected $fillable = [ 
        "id", 
        "forwarder_id", 
        "overtime_date",
        "job_date",
        "overtime_start",
        "overtime_finish",
        "duration"
    ];
}