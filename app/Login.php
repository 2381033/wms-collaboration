<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Login extends Model
{
    protected $table = "sm_login";

    protected $fillable = [
        "user_id", 
        "login", 
        "logout",
    ];
}