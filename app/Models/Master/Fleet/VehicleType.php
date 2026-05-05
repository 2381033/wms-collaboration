<?php

namespace App\Models\Master\Fleet;

use Illuminate\Database\Eloquent\Model;

class VehicleType extends Model
{
    protected $table = "fm_vehicle_type";
    protected $fillable = [ 
        "id", 
        "vehicle_type", 
        "description", 
        "cbm", 
        "weight", 
        "pallet_count",  
        "active" 
    ];
}