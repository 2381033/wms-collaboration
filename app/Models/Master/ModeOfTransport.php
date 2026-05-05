<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class ModeOfTransport extends Model
{
    protected $table = "iv_mode";
    protected $fillable = [ 
        "company_id", 
        "mode_name", 
        "active" 
    ];
}