<?php

namespace App\Models\Transaction\Stock;

use Illuminate\Database\Eloquent\Model;

class Occupancy extends Model
{
    protected $table = "iv_occupancy_daily";
    protected $fillable = ["company_id", "principal_id", "transaction_date", "in", "out", "status_code", "qty"];
}
