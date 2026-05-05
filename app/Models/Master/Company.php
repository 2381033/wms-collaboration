<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = "mt_company";
    protected $fillable = [ 
        "initial", 
        "company_code", 
        "company_name", 
        "active" 
    ];
}