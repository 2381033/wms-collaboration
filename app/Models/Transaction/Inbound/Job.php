<?php

namespace App\Models\Transaction\Inbound;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = "iv_inbound_job";
    protected $fillable = [ "company_id", "branch_id", "principal_id", "job_no", "job_date", "class_id", "mode_id", "description", "reference_no", "reference_other", "eta", "ata", "token_id", "grn_no", "remarks", "entry_date", "received_date", "allocated_date", "unloading_start", "unloading_finish", "confirmed_date", "received_flag", "received_by", "confirmed_flag", "confirmed_by", "user_id" ];
    protected $dates = ["eta", "entry_date", "unloading_start", "unloading_finish"];

    public function job_class() {
        return $this->belongsTo('App\Models\Master\JobClass', 'class_id');
    }
}