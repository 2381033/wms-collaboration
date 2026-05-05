<?php

namespace App\Models\Transaction\CycleCount;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = "iv_cyclecount_job";
    protected $fillable = [ "company_id", "principal_id", "cyclecount_no", "cyclecount_date", "description", "group_id_from", "group_id_to", "brand_id_from", "brand_id_to", "product_id_from", "product_id_to", "site_id", "area_id", "location_id_from", "location_code_from", "location_id_to", "location_code_to", "confirmed_flag", "confirmed_by", "confirmed_date" ];
    protected $dates = [ "cyclecount_date" ];
}