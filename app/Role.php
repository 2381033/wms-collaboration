<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = "sm_role";

    protected $fillable = [
        "role_name", 
        "active"
    ];
}