<?php

namespace App\Models\Transaction\Outbound;

use Illuminate\Database\Eloquent\Model;

class Detail extends Model
{
    protected $table = "iv_outbound_detail";
    protected $fillable = [ "outbound_id", "order_id", "company_id", "principal_id", "customer_id", "job_no", "order_no", "product_id", "product_code", "lot_no", "document_ref", "site_id", "area_id", "location_from_id", "location_from", "location_to_id", "location_to", "puom", "muom", "buom", "uppp", "muppp", "pqty", "mqty", "bqty", "qty", "picking_flag", "picking_by", "picking_date", "confirmed_flag", "confirmed_by", "confirmed_date", "user_id" ];
    protected $dates = ["picking_date", "confirmed_date"];
}