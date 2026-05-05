<?php

namespace App\Models\Transaction\Export;

use Illuminate\Database\Eloquent\Model;

class OutboundOrder extends Model
{
    protected $table = "ex_outbound_order";
    protected $fillable = [ 
        "id", 
        "job_id", 
        "consignee_id",
        "shipper_id",
        "po_number",
        "peb_no",
        "qty_cargo",
        "cbm",
        "weight",
        "total_pallet",
        "status_flag",
        "user_id"
    ];
}