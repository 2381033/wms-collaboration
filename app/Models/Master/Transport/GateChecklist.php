<?php

namespace App\Models\Master\Transport;

use Illuminate\Database\Eloquent\Model;

class GateChecklist extends Model
{
    protected $table = "tm_gate_checklist";
    protected $fillable = [ 
        "id", 
        "gate_id",
        "process_id",
        "group_id",
        "item_id",
        "item_type",
        "results_flag",
        "action_flag",
        "remarks",
        "status_flag",
        "user_id"
    ];
}