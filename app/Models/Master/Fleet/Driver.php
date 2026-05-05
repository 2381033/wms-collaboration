<?php

namespace App\Models\Master\Fleet;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $table = "fm_driver";
    protected $fillable = [ 
        "id", 
        "branch_id", 
        "driver_name", 
        "phone", 
        "join_date", 
        "sim_no", 
        "sim_date",  
        "active" 
    ];
}