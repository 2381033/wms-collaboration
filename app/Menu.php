<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = "sm_menu";

    protected $fillable = [
        "id", 
        "name", 
        "parent_id", 
        "url", 
        "icon", 
        "active"
    ];

    public function children() {
        return $this->hasMany("App\Menu", "parent_id", "id");
    }

    public function user() {
        return $this->belongsToMany("App\User", "sm_menu_user")
                    ->withPivot("akses", "tambah", "edit", "hapus", "cetak");
    }
}
