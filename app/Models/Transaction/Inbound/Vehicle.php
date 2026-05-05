<?php

namespace App\Models\Transaction\Inbound;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $table = "iv_inbound_vehicle";
    protected $fillable = [ "company_id", "inbound_id", "principal_id", "job_no", "vehicle_no", "transporter_name", "driver_name", "container_no", "seal_no", "awb_no", "type_id", "size_id", "confirmed_flag", "user_id" ];
}