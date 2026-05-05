<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class ContainerType extends Model
{
    protected $table = "iv_container_type";
    protected $fillable = [ 
        "company_id", 
        "type_name", 
        "active" 
    ];
}