<?php

namespace App\Models\Transaction\Stock;

use Illuminate\Database\Eloquent\Model;

class Ledger extends Model
{
    protected $table = "iv_stock_ledger";
    protected $fillable = [ "company_id", "branch_id",  "principal_id", "serial_no", "srno", "job_no", "job_date", "vehicle_no", "line_no", "product_id", "product_code", "po_number", "lot_no", "document_ref", "mfg_date", "exp_date", "manufactur_id", "status_id", "site_id", "area_id", "location_id", "location_code", "puom", "muom", "buom", "uppp", "muppp", "pqty", "mqty", "bqty", "qtyr", "qtys", "qtya", "qtyp", "pallet_qty", "base_unit", "reference_no", "freeze_flag", "freeze_by", "freeze_date", "freeze_reason", "status" ];
    protected $dates = ["mfg_date", "exp_date", "freeze_date"];
}