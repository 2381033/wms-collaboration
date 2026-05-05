<?php

namespace App\Models\Transaction\Replenish;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = "iv_replenish_job";
    protected $fillable = [ "company_id", "principal_id", "replenish_no", "replenish_date", "product_id_from", "product_id_to", "site_id", "area_id", "location_id_from", "location_code_from", "location_id_to", "location_code_to", "allocated_flag", "allocated_by", "allocated_date", "confirmed_flag", "confirmed_by", "confirmed_date"  ];
    protected $dates = [ "replenish_date" ];
}