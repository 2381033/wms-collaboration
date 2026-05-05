<?php

namespace App\Models\Transaction\StockTake;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = "iv_stocktake_job";
    protected $fillable = [
        "company_id",
        "branch_id",
        "principal_id",
        "stocktake_no",
        "stocktake_date",
        "description",
        "block",
        "confirmed_flag",
        "confirmed_by",
        "confirmed_date",
        "user_id"
    ];
    protected $dates = ["stocktake_date"];
}
