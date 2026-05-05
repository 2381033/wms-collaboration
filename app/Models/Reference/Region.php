<?php

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $table = "rt_region";
    protected $fillable = [ 
        "id", 
        "country_code", 
        "region_code", 
        "region_name", 
        "active" 
    ];

    public function country() {
        return $this->belongsTo("App\Models\Reference\Country", 'country_code');
    }
}