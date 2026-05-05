<?php

namespace App\Models\Transaction\Outbound;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = "iv_outbound_job";
    protected $fillable = [ 
        "company_id", 
        "branch_id", 
        "principal_id", 
        "job_no", 
        "job_date", 
        "class_id", 
        "mode_id", 
        "description", 
        "reference_no", 
        "reference_other", 
        "etd", 
        "ata", 
        "token_id", 
        "remarks", 
        "entry_date", 
        "allocated_flag", 
        "allocated_date", 
        "loading_start", 
        "loading_finish", 
        "confirmed_date", 
        "confirmed_flag", 
        "confirmed_by",
        "user_id" 
    ];
    protected $dates = ["etd", "ata", "entry_date", "loading_start", "loading_finish"];

    public function job_class() {
        return $this->belongsTo('App\Models\Master\JobClass', 'class_id');
    }
}