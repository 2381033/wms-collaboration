<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Model;

class Gate extends Model
{
    protected $table = "iv_gate";
    protected $fillable = [ "company_id", "token_id", "vehicle_no", "driver_name", "transporter_name", "principal_id", "service", "gate_in", "gate_out" ];
}