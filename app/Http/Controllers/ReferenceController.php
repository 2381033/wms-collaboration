<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReferenceController extends Controller
{
    public function region(Request $request) {
        $list = DB::table("rt_region")
                    ->where("country_code", $request->country_code)
                    ->where("active", "Yes")
                    ->get(["region_code", "region_name"]);

        $data = [
            "region_list"=>$list
        ];

        return response()->json($data);
    }

    public function city(Request $request) {
        $list = DB::table("rt_city")
                    ->where("country_code", $request->country_code)
                    ->where("region_code", $request->region_code)
                    ->where("active", "Yes")
                    ->get(["city_code", "city_name"]);

        $data = [
            "city_list"=>$list
        ];

        return response()->json($data);
    }
}