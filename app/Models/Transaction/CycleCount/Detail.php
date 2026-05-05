<?php

namespace App\Models\Transaction\CycleCount;

use Illuminate\Database\Eloquent\Model;

class Detail extends Model
{
    protected $table = "iv_cyclecount_detail";
    protected $fillable = [ "company_id", "principal_id", "cyclecount_id", "job_no", "serial_id", "serial_no", "product_id", "product_code", "po_number", "lot_no", "document_ref", "mfg_date", "exp_date", "manufactur_id", "status_id", "site_id", "area_id", "location_id", "location_code", "puom", "muom", "buom", "uppp", "muppp", "pqty", "mqty", "bqty", "qty", "actual_pqty", "actual_mqty", "actual_bqty", "actual_qty", "actual_lot_no", "actual_mfg_date", "actual_exp_date", "pallet_qty", "base_unit", "confirmed_flag", "confirmed_by", "confirmed_date" ];
    protected $dates = [ "mfg_date", "exp_date", "confirmed_date"];
}