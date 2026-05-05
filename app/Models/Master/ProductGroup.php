<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class ProductGroup extends Model
{
    protected $table = "iv_product_group";
    protected $fillable = [ 
        "company_id", 
        "principal_id", 
        "group_code", 
        "group_name", 
        "active" 
    ];
}