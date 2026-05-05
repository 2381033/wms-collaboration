<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class CustomerType extends Model
{
    protected $table = "iv_customer_type";
    protected $fillable = [ 
        "company_id", 
        "principal_id", 
        "type_name", 
        "active" 
    ];
}