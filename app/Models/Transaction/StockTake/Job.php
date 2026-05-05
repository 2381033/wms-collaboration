<?php

namespace App\Models\Transaction\StockTake;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = "iv_stocktake_job";
    protected $fillable = [ 
        "company_id", 
        "principal_id", 
        "stocktake_no", 
        "stocktake_date", 
        "description", 
        "group_id_from", 
        "group_id_to", 
        "brand_id_from", 
        "brand_id_to", 
        "product_id_from", 
        "product_id_to", 
        "site_id", 
        "area_id_from", 
        "area_id_to", 
        "confirmed_flag", 
        "confirmed_by", 
        "confirmed_date",
        "user_id"  
    ];
    protected $dates = [ "stocktake_date" ];
}