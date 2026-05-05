<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $table = "iv_site";
    protected $fillable = [ 
        "company_id", 
        "site_name", 
        "type_id", 
        "indicator_id", 
        "location_id", 
        "address", 
        "zip_code", 
        "phone", 
        "fax", 
        "active" 
    ];

    public function user() {
        return $this->belongsToMany('App\User', 'users_site');
    }

    public function principal() {
        return $this->belongsToMany('App\Models\Master\Principal', 'iv_principal_site', 'principal_id', 'site_id')->withTimestamps();
    }
}