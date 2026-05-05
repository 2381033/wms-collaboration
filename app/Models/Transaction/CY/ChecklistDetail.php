<?php

namespace App\Models\Transaction\CY;

use Illuminate\Database\Eloquent\Model;

class ChecklistDetail extends Model
{
    protected $table = "cy_checklist_detail";
    protected $fillable = [ 
        "id", 
        "checklist_id", 
        "check_id",
        "remarks",
        "filename",
        "path",
    ];
}