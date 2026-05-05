<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class ContainerSize extends Model
{
    protected $table = "iv_container_size";
    protected $fillable = [ 
        "company_id", 
        "size_name", 
        "active" 
    ];

    public function forwarder() {
        return $this->belongsToMany('App\Models\Master\Export\Forwarder', 'mt_forwarder_size', 'forwarder_id', 'size_id')->withTimestamps();
    }
}