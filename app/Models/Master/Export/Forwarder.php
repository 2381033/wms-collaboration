<?php

namespace App\Models\Master\Export;

use Illuminate\Database\Eloquent\Model;

class Forwarder extends Model
{
    protected $table = "mt_forwarder";
    protected $fillable = [
        "branch_id",
        "forwarder_name",
        "storage_amount",
        "adm_amount",
        "active"
    ];

    public function service()
    {
        return $this->belongsToMany('App\Models\Master\Service', 'mt_forwarder_service', 'forwarder_id', 'service_id')->withTimestamps();
    }

    public function container_size()
    {
        return $this->belongsToMany('App\Models\Master\ContainerSize', 'mt_forwarder_size', 'forwarder_id', 'size_id')->withPivot("rate_amount")->withTimestamps();
    }
}
