<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class StockStatus extends Model
{
    protected $table = "iv_stock_status";
    protected $fillable = [ 
        "company_id", 
        "principal_id", 
        "status_name", 
        "active" 
    ];
}