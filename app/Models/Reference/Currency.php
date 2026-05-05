<?php

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $table = "rt_currency";
    protected $fillable = [ 
        "company_id", 
        "currency_code", 
        "currency_name", 
        "active" 
    ];
}