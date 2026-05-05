<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Manufactur extends Model
{
    protected $table = "iv_manufactur";
    protected $fillable = [ 
        "company_id", 
        "principal_id", 
        "manufactur_code", 
        "manufactur_name", 
        "active" 
    ];
}