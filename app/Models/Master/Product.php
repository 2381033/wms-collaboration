<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = "iv_product";
    protected $fillable = [ 
        "company_id", 
        "principal_id", 
        "product_code", 
        "product_name",
        "category_id", 
        "group_id", 
        "brand_id", 
        "pick_criteria", 
        "unit_level",
        "puom", 
        "muom", 
        "buom", 
        "uppp", 
        "muppp", 
        "manufactur_id", 
        "batch_flag",
        "expired_flag",
        "freeze_flag",
        "length",
        "width", 
        "height", 
        "dimensions_unit", 
        "volume", 
        "volume_unit", 
        "gross_weight", 
        "net_weight", 
        "weight_unit", 
        "temperature", 
        "shelf_life", 
        "freeze_day", 
        "base_price", 
        "active" 
    ];
}