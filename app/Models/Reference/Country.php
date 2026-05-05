<?php

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = "rt_country";
    protected $fillable = [ 
        "id", 
        "country_code", 
        "country_name", 
        "active" 
    ];

    public function region() {
        return $this->hasMany("App\Models\Reference\Region");
    }
}
