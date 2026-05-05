<?php

namespace App\Models\Transaction\Stock;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = "iv_stock_transaction";
    protected $fillable = [ "company_id", "branch_id", "principal_id", "job_no", "serial_no", "srno", "line_no", "job_date", "job_type", "product_id", "product_code", "po_number", "lot_no", "document_ref", "mfg_date", "exp_date", "manufactur_id", "status_id", "site_id", "area_id", "location_id", "location_code", "puom", "muom", "buom", "uppp", "muppp", "pqty", "mqty", "bqty", "qty", "grn_no", "base_unit", "reference_no" ];
    protected $dates = ["mfg_date", "exp_date", "job_date"];
}