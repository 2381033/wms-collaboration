<?php

namespace App\Models\Transaction\CycleCount;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $table = "iv_cyclecount_batch";
    protected $fillable = [ "company_id", "principal_id", "cyclecount_id", "serial_id", "serial_no", "job_no", "job_date", "vehicle_no", "line_no", "product_id", "product_code", "po_number", "lot_no", "document_ref", "mfg_date", "exp_date", "manufactur_id", "status_id", "site_id", "area_id", "location_id", "location_code", "puom", "muom", "buom", "uppp", "muppp", "qtyr", "qtys", "qtya", "qtyp", "pallet_qty", "base_unit", "reference_no", "confirmed_flag", "confirmed_by", "confirmed_date" ];
    protected $dates = ["mfg_date", "exp_date", "confirmed_date"];
}