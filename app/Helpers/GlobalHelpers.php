<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class GlobalHelpers {
    public static function checkLogin() {
        session()->put("previous-route", Route::current()->getName());

        if (!Auth::check()) {
            return false;
        }

        return true;
    }

    public static function customTanggal($date, $date_format){
        return \Carbon\Carbon::createFromFormat('Y-m-d', $date)->format($date_format);    
    }

    public static function isAccess($menu) {
        $access = Auth::user()->menu->where("url", $menu)->first();
        
        if (isset($access)) {
            if ($access->pivot->akses == "Yes") {
                return true;
            }
        }

        return false;
    }
}