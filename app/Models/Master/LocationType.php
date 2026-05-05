<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class LocationType extends Model
{
    protected $table = "iv_location_type";
    protected $fillable = [ 
        "description", 
        "cbm", 
        "weight", 
        "active" 
    ];
}