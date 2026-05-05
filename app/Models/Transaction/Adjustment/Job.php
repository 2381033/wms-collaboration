<?php

namespace App\Models\Transaction\Adjustment;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = "iv_adjustment_job";
    protected $fillable = [ "company_id", "branch_id", "principal_id", "adjust_no", "adjust_date", "type_id", "description", "cycle_no", "filename", "confirmed_flag", "confirmed_by", "confirmed_date" ];
    protected $dates = ["adjust_date", "confirmed_date"];
}