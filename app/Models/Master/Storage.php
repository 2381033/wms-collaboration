<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Storage extends Model
{
    protected $table = "iv_storage";
    protected $fillable = [ 
        "company_id", 
        "principal_id", 
        "foc", 
        "currency_id",
        "currency_code", 
        "cpu", 
        "quota", 
        "cpu_ext", 
        "flat_rate", 
        "remarks",
        "active"
    ];
}