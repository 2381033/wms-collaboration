<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class LocationStatus extends Model
{
    protected $table = "iv_location_status";
    protected $fillable = [
        "status_code", 
        "status_name", 
        "active" 
    ];
}