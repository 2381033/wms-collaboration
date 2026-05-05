<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $table = "iv_location";
    protected $fillable = [ 
        "company_id", 
        "site_id", 
        "area_id", 
        "location_code", 
        "location_name", 
        "type_id", 
        "status_code", 
        "location_aisle", 
        "location_column", 
        "location_level", 
        "principal_id", 
        "product_id", 
        "reorder_qty", 
        "reorder_level", 
        "uom_id", 
        "active" 
    ];
}