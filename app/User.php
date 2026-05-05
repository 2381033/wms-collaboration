<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "name", 
        "username", 
        "role_id", 
        "email", 
        "password", 
        "company_id",
        "active"
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        "password", "remember_token",
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        "email_verified_at" => "datetime",
    ];

    public function menu() {
        return $this->belongsToMany("App\Menu", "sm_menu_user", "user_id", "menu_id")
                    ->withPivot("akses", "tambah", "edit", "hapus", "cetak");
    }

    public function role() {
        return $this->belongsTo("App\Role", "role_id", "id");
    }

    public function principal() {
        return $this->belongsToMany("App\Models\Master\Principal", "users_principal", "user_id", "principal_id")->withTimestamps();
    }

    public function site() {
        return $this->belongsToMany("App\Models\Master\Site", "users_site", "user_id", "site_id")->withTimestamps();
    }

    public function branch() {
        return $this->belongsToMany("App\Models\Master\Branch", "sm_user_branch", "user_id", "branch_id")->withTimestamps();
    }
}