<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class ProductBrand extends Model
{
    protected $table = "iv_product_brand";
    protected $fillable = [ 
        "company_id", 
        "principal_id", 
        "group_id", 
        "brand_code", 
        "brand_name", 
        "active" 
    ];
}