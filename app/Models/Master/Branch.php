<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $table = "mt_branch";
    protected $fillable = [ 
        "id", 
        "branch_name",
        "initial_name",
        "active" 
    ];

    public function users() {
        return $this->belongsToMany('App\User', 'sm_user_branch');
    }

    public function principal() {
        return $this->belongsToMany('App\Models\Master\Principal', 'iv_principal_branch', 'principal_id', 'branch_id')->withTimestamps();
    }
}