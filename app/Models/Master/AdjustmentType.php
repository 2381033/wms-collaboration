<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class AdjustmentType extends Model
{
    protected $table = "iv_adjustment_type";
    protected $fillable = [ 
        "company_id", 
        "type_name", 
        "active" 
    ];
}