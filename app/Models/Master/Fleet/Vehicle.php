<?php

namespace App\Models\Master\Fleet;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $table = "fm_vehicle";
    protected $fillable = [ 
        "id", 
        "branch_id", 
        "vehicle_code", 
        "vehicle_no", 
        "type_id", 
        "ownership",  
        "driver_id",
        "status_code",
        "active" 
    ];

    public function document() {
        return $this->belongsToMany('App\Models\Master\Fleet\Document', 'fm_vehicle_document', 'vehicle_id', 'document_id')->withPivot("expired_date")->withTimestamps();
    }
}