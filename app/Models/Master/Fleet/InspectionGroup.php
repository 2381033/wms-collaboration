<?php

namespace App\Models\Master\Fleet;

use Illuminate\Database\Eloquent\Model;

class InspectionGroup extends Model
{
    protected $table = "fm_inspection_group";
    protected $fillable = [ 
        "id", 
        "group_name", 
        "active" 
    ];
}