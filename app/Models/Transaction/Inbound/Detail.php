<?php

namespace App\Models\Transaction\Inbound;

use Illuminate\Database\Eloquent\Model;

class Detail extends Model
{
    protected $table = "iv_inbound_detail";
    protected $fillable = [ "inbound_id", "company_id", "principal_id", "job_no", "vehicle_no", "product_id", "product_code", "po_number", "lot_no", "document_ref", "mfg_date", "exp_date", "manufactur_id", "site_id", "area_id", "mixed_pallet", "location_from", "location_to", "pallet_id", "puom", "muom", "buom", "uppp", "muppp", "pqty", "mqty", "bqty", "qty", "actual_pqty", "actual_mqty", "actual_bqty", "actual_qty", "discrepancy_pqty", "discrepancy_mqty", "discrepancy_bqty", "discrepancy_qty", "remarks", "base_unit", "product_status", "manual_putaway", "recevied_flag", "recevied_by", "recevied_date", "putaway_flag", "putaway_by", "putaway_date", "confirmed_flag", "confirmed_by", "confirmed_date", "user_id" ];
    protected $dates = ["mfg_date", "exp_date", "received_date", "confirmed_date"];
}