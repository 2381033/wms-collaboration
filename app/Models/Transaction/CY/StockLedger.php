<?php

namespace App\Models\Transaction\CY;

use Illuminate\Database\Eloquent\Model;

class StockLedger extends Model
{
    protected $table = "cy_stock_ledger";
    protected $fillable = [ 
        "id", 
        "branch_id", 
        "forwarder_id",
        "booking_id",
        "inbound_id",
        "booking_no",
        "serial_no",
        "job_no",
        "job_date",
        "reference_no",
        "driver_name",
        "container_no",
        "vehicle_no",
        "size_id",
        "container_status",
        "container_no",
        "qtys",
        "qtya",
        "qtyp",
        "user_id"
    ];
}