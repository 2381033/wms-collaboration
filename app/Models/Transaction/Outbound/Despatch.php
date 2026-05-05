<?php

namespace App\Models\Transaction\Outbound;

use Illuminate\Database\Eloquent\Model;

class Despatch extends Model
{
    protected $table = "iv_outbound_despatch";
    protected $fillable = ["company_id", "principal_id", "outbound_id", "job_no", "customer_id", "mode_id", "do_no", "reference_no", "size_id", "carrier_name", "vessel_name", "vehicle_no", "seal_no", "driver_name", "phone", "container_no", "etd", "pqty", "mqty", "bqty", "order_count", "awb_no", "awb_date", "send_date_doc", "store_id", "delivery_type"];
    protected $dates = ["etd", "awb_date"];
}
