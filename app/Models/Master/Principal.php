<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Principal extends Model
{
    protected $table = "iv_principal";
    protected $fillable = [ 
        "id", 
        "company_id", 
        "principal_name", 
        "short_name", 
        "interface_mode", 
        "address1", 
        "address2", 
        "address3", 
        "address4", 
        "phone", 
        "email", 
        "pic_name", 
        "pic_phone", 
        "pallet_capacity_racking",
        "pallet_capacity_bulk",
        "multi_level",
        "multi_checklist",
        "active" 
    ];

    public function users() {
        return $this->belongsToMany('App\User', 'users_principal');
    }

    public function site() {
        return $this->belongsToMany('App\Models\Master\Site', 'iv_principal_site', 'principal_id', 'site_id')->withTimestamps();
    }

    public function branch() {
        return $this->belongsToMany('App\Models\Master\Branch', 'iv_principal_branch', 'principal_id', 'branch_id')->withTimestamps();
    }
}