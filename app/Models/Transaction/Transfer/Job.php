<?php

namespace App\Models\Transaction\Transfer;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = "iv_transfer_job";
    protected $fillable = [ 
        "company_id", 
        "branch_id", 
        "principal_id", 
        "job_no", 
        "job_date",
        "site_id", 
        // "class_id", 
        // "mode_id", 
        "description", 
        "entry_flag", 
        "entry_by", 
        "entry_date", 
        "confirmed_flag", 
        "confirmed_by", 
        "confirmed_date",
        "user_id" 
    ];
    protected $dates = ["job_date", "entry_date", "confirmed_date"];
}