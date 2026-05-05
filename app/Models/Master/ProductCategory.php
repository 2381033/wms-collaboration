<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $table = "iv_product_category";
    protected $fillable = [ 
        "company_id", 
        "principal_id", 
        "category_name", 
        "active" 
    ];
}