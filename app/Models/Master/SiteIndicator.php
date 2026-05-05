<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class SiteIndicator extends Model
{
    protected $table = "iv_site_indicator";
    protected $fillable = [ 
        "company_id", 
        "type_id", 
        "indicator_id",
        "indicator_name", 
        "active" 
    ];
}