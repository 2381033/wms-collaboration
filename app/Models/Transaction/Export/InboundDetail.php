<?php

namespace App\Models\Transaction\Export;

use Illuminate\Database\Eloquent\Model;

class InboundDetail extends Model
{
    protected $table = "ex_inbound_detail";
    protected $fillable = [ 
        "id", 
        "job_id", 
        "serial_no",
        "pallet_id",
        "quantity",
        "user_id"
    ];
}