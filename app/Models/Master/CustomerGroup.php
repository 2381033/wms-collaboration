<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class CustomerGroup extends Model
{
    protected $table = "iv_customer_group";
    protected $fillable = [ 
        "company_id", 
        "principal_id", 
        "group_name", 
        "active" 
    ];
}