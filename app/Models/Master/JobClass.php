<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class JobClass extends Model
{
    protected $table = "iv_job_class";
    protected $fillable = [ 
        "company_id", 
        "class_name", 
        "active" 
    ];

    public function inbound_job() {
        return $this->hasMany('App\Models\Transaction\Inbound\Job', 'class_id');
    }

    public function outbound_job() {
        return $this->hasMany('App\Models\Transaction\Outbound\Job', 'class_id');
    }
}
