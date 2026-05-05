<?php

namespace App\Models\Master\Transport;

use Illuminate\Database\Eloquent\Model;

class GateProcess extends Model
{
    protected $table = "tm_gate_process";
    protected $fillable = [ 
        "id", 
        "gate_id",
        "site_id",
        "gate_in",
        "gate_in_by",
        "check_flag",
        "check_date",
        "check_by",
        "process_start",
        "process_start_by",
        "process_finish",
        "process_finish_by",
        "gate_out",
        "gate_out_by",
        "active" 
    ];
}