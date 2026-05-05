<?php

namespace App\Models\Master\Fleet;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $table = "fm_document";
    protected $fillable = [ 
        "id", 
        "document_name", 
        "alert_1",
        "alert_2",
        "alert_3",
        "alert_4",
        "active" 
    ];

    public function vehicle() {
        return $this->belongsToMany('App\Models\Master\Fleet\Vehicle', 'fm_vehicle_document', 'vehicle_id', 'document_id')->withTimestamps();
    }
}