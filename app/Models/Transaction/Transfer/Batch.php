<?php

namespace App\Models\Transaction\Transfer;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $table = "iv_transfer_batch";
    protected $fillable = [ "company_id", "principal_id", "transfer_id", "line_id", "job_no", "job_type", "serial_id", "serial_no", "product_id", "product_code", "po_number", "lot_no", "document_ref", "mfg_date", "exp_date", "manufactur_id", "status_id", "site_id", "area_id", "location_id", "location_code", "puom", "muom", "buom", "uppp", "muppp", "pqty", "mqty", "bqty", "qty", "base_unit", "reference_no", "pallet_qty", "srno", "confirmed_flag", "confirmed_by", "confirmed_date" ];
    protected $dates = ["mfg_date", "exp_date", "confirmed_date"];
}