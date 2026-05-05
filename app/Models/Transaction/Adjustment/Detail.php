<?php

namespace App\Models\Transaction\Adjustment;

use Illuminate\Database\Eloquent\Model;

class Detail extends Model
{
    protected $table = "iv_adjustment_detail";
    protected $fillable = [ "company_id", "principal_id", "adjust_id", "status_flag", "adjust_type", "job_no", "serial_id", "serial_no", "product_id", "product_code", "po_number", "lot_no", "document_ref", "mfg_date", "exp_date", "manufactur_id", "status_id", "site_id", "area_id", "location_id", "location_code", "puom", "muom", "buom", "uppp", "muppp", "pqty", "mqty", "bqty", "qty", "actual_pqty", "actual_mqty", "actual_bqty", "actual_qty", "pallet_qty", "base_unit", "picked_flag", "picked_by", "picked_date", "confirmed_flag", "confirmed_by", "confirmed_date" ];
    protected $dates = [ "mfg_date", "exp_date", "picked_date", "confirmed_date"];
}