<?php

namespace App\Models\Transaction\Export;

use Illuminate\Database\Eloquent\Model;

class OutboundDetail extends Model
{
    protected $table = "ex_outbound_detail";
    protected $fillable = [ 
        "id", 
        "job_id",
        "order_id", 
        "po_number",
        "peb_no",
        "serial_no",
        "quantity",
        "status_flag",
        "user_id"
    ];
}