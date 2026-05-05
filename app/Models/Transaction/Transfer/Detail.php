<?php

namespace App\Models\Transaction\Transfer;

use Illuminate\Database\Eloquent\Model;

class Detail extends Model
{
    protected $table = "iv_transfer_detail";
    protected $fillable = [ 
        "company_id", 
        "principal_id", 
        "transfer_id", 
        "job_no", 
        "serial_id", 
        "serial_no", 
        "product_id", 
        "product_code", 
        "po_number", 
        "lot_no", 
        "document_ref", 
        "mfg_date", 
        "exp_date", 
        "manufactur_id", 
        "status_id", 
        "puom", 
        "muom", 
        "buom", 
        "uppp", 
        "muppp", 
        "pqty", 
        "mqty", 
        "bqty", 
        "qty", 
        "actual_pqty", 
        "actual_mqty", 
        "actual_bqty", 
        "actual_qty", 
        "base_unit", 
        "site_id", 
        "area_id", 
        "location_id", 
        "location_code", 
        "dest_site_id", 
        "dest_area_id", 
        "dest_location_id", 
        "dest_location_code", 
        "pallet_qty", 
        "srno", 
        "picked_flag", 
        "picked_by", 
        "picked_date", 
        "confirmed_flag", 
        "confirmed_by", 
        "confirmed_date",
        "user_id" 
    ];
    protected $dates = ["mfg_date", "exp_date", "picked_date", "confirmed_date"];
}