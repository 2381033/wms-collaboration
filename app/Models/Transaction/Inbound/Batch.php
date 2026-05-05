<?php

namespace App\Models\Transaction\Inbound;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $table = "iv_inbound_batch";
    protected $fillable = ["inbound_id", "packing_id", "company_id", "principal_id", "serial_no", "job_no", "vehicle_no", "product_id", "product_code", "po_number", "lot_no", "document_ref", "mfg_date", "exp_date", "manufactur_id", "site_id", "area_id", "location_id", "location_code", "pallet_id", "manual_putaway", "puom", "muom", "buom", "uppp", "muppp", "pqty", "mqty", "bqty", "qty", "pallet_qty", "base_unit", "product_status", "confirmed_flag", "confirmed_by", "confirmed_date", "crossdock_flag", "remarks"];
    protected $dates = ["mfg_date", "exp_date", "confirmed_date"];
}
