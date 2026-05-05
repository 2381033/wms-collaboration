<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class SiteType extends Model
{
    protected $table = "iv_site_type";
    protected $fillable = [ 
        "company_id", 
        "type_name", 
        "active" 
    ];
}