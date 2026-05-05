<?php

namespace App\Models\Transaction\CY;

use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    protected $table = "cy_checklist";
    protected $fillable = [ 
        "id", 
        "check_name", 
        "active",
    ];
}