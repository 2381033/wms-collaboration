<?php

namespace App\Models\Transaction\Fleet;

use Illuminate\Database\Eloquent\Model;

class CheckListDetail extends Model
{
    protected $table = "fm_checklist_detail";
    protected $fillable = [ 
        "id", 
        "check_id",
        "group_id",
        "item_id",
        "item_type",
        "results_flag",
        "action_flag",
        "remarks",
    ];
}