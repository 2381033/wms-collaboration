<?php

namespace App\Models\Master\Fleet;

use Illuminate\Database\Eloquent\Model;

class InspectionItem extends Model
{
    protected $table = "fm_inspection_item";
    protected $fillable = [ 
        "id", 
        "group_id",
        "item_name",
        "item_type", 
        "active" 
    ];
}