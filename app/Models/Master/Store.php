<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $table = "tm_store";
    protected $fillable = [ 
        "company_id", 
        "principal_id", 
        "store_code", 
        "store_name", 
        "country_code", 
        "region_code", 
        "city_code", 
        "address1", 
        "address2", 
        "address3", 
        "address4", 
        "telephone", 
        "email", 
        "pic_name", 
        "pic_phone",
        "active"
    ];
}