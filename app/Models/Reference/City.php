<?php

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = "rt_city";
    protected $fillable = [ 
        "country_code", 
        "region_code", 
        "city_code",
        "city_name", 
        "active" 
    ];
}