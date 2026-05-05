<?php

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Model;

class UoM extends Model
{
    protected $table = "rt_uom";
    protected $fillable = [ 
        "code", 
        "uom_name", 
        "active" 
    ];
}