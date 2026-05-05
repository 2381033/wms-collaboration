<?php

namespace App\Models\Transaction\Export;

use Illuminate\Database\Eloquent\Model;

class StockLedger extends Model
{
    protected $table = "ex_stock_ledger";
    protected $fillable = [ 
        "id", 
        "branch_id", 
        "job_no",
        "job_date",
        "vehicle_no",
        "po_number",
        "forwarder_id",
        "shipper_id",
        "consignee_id",
        "destination",
        "peb_no",
        "pic_name",
        "qty_cargo",
        "cbm",
        "weight",
        "total_pallet",
        "serial_no",
        "pallet_id",
        "quantity",
        "status_flag",
        "user_id"
    ];
}