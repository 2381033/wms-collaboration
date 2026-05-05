<?php

namespace App\Models\Transaction\Outbound;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $table = "iv_outbound_batch";
    protected $fillable = [ "outbound_id", "picking_id", "serial_id", "company_id", "principal_id", "customer_id", "order_no", "serial_no", "job_no", "product_id", "product_code", "po_number", "lot_no", "document_ref", "reference_no", "mfg_date", "exp_date", "site_id", "area_id", "location_id", "location_code", "puom", "muom", "buom", "uppp", "muppp", "pqty", "mqty", "bqty", "qty", "base_unit", "pallet_qty", "confirmed_flag", "confirmed_by", "confirmed_date" ];
    protected $dates = ["mfg_date", "exp_date", "confirmed_date"];
}