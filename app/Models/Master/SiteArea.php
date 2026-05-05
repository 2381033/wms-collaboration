<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class SiteArea extends Model
{
    protected $table = "iv_site_area";
    protected $fillable = [ 
        "company_id", 
        "site_id", 
        "area_name", 
        "active" 
    ];
}