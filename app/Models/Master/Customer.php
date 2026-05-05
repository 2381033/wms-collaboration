<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = "iv_customer";
    protected $fillable = [ 
        "company_id",
        "principal_id", 
        "customer_code",
        "customer_name", 
        "group_id",
        "type_id",
        "address1", 
        "address2", 
        "address3", 
        "address4", 
        "phone", 
        "email", 
        "pic_name", 
        "pic_phone", 
        "store_id",
        "active" 
    ];
}