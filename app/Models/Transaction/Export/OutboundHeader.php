<?php

namespace App\Models\Transaction\Export;

use Illuminate\Database\Eloquent\Model;

class OutboundHeader extends Model
{
    protected $table = "ex_outbound_header";
    protected $fillable = [ 
        "id", 
        "branch_id", 
        "job_no",
        "job_date",
        "forwarder_id",
        "size_id",
        "container_no",
        "vessel_name",
        "surveyor_name",
        "destination",
        "voyage_no",
        "qty_cargo",
        "cbm",
        "weight",
        "total_pallet",
        "remarks",
        "status_flag",
        "user_id"
    ];
}