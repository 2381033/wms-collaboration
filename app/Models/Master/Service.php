<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = "mt_service";
    protected $fillable = [ 
        "company_id", 
        "service_name", 
        "active" 
    ];

    public function forwarder() {
        return $this->belongsToMany('App\Models\Master\Export\Forwarder', 'mt_forwarder_service', 'forwarder_id', 'service_id')->withTimestamps();
    }
}