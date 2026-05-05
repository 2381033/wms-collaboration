<?php

namespace App\Http\Controllers\Transaction\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AutoCompleteController extends Controller
{    
    public $page = 20;
    
    public function getProductGroup(Request $request) {
        $company_id = Auth::user()->company_id;    

        $group_list = DB::table("iv_product_group as a")
                    ->select("a.*")
                    ->where("a.company_id", $company_id)
                    ->where("a.principal_id", $request->principal_id)
                    ->where("a.active", "Yes")
                    ->get();

        $data = [
            "group_list" => $group_list
        ];

        return response()->json($data);
    }

    public function getProductBrand(Request $request) {
        $company_id = Auth::user()->company_id;    

        if (!empty($request->group_code_from) && !empty($request->group_code_to)) {
            $group_from = $request->group_code_from;
            $group_to = $request->group_code_to;
        } else { 
            if (!empty($request->group_code_from) && empty($request->group_code_to)) {
                $group_from = $request->group_code_from;
                $group_to = "zzzzzzzzzz";
            } else if (empty($request->group_code_from) && !empty($request->group_code_to)) {
                $group_from = "";
                $group_to = $request->group_code_to;
            } else {
                $group_from = "";
                $group_to = "zzzzzzzzzz";
            }
        }

        $brand_list = DB::table("iv_product_brand as a")
                    ->select("a.*", "b.group_code", "b.group_name")
                    ->join("iv_product_group as b", "a.group_id", "b.id")
                    ->where("a.company_id", $company_id)
                    ->where("a.principal_id", $request->principal_id)
                    ->whereBetween("b.group_code", [$group_from, $group_to])
                    ->where("a.active", "Yes")
                    ->get();

        $data = [
            "brand_list" => $brand_list
        ];

        return response()->json($data);
    }

    public function getProduct(Request $request) {
        $company_id = Auth::user()->company_id;    

        if (!empty($request->group_code_from) && !empty($request->group_code_to)) {
            $group_from = $request->group_code_from;
            $group_to = $request->group_code_to;
        } else { 
            if (!empty($request->group_code_from) && empty($request->group_code_to)) {
                $group_from = $request->group_code_from;
                $group_to = "zzzzzzzzzz";
            } else if (empty($request->group_code_from) && !empty($request->group_code_to)) {
                $group_from = "";
                $group_to = $request->group_code_to;
            } else {
                $group_from = "";
                $group_to = "zzzzzzzzzz";
            }
        }

        if (!empty($request->brand_code_from) && !empty($request->brand_code_to)) {
            $brand_from = $request->brand_code_from;
            $brand_to = $request->brand_code_to;
        } else { 
            if (!empty($request->brand_code_from) && empty($request->brand_code_to)) {
                $brand_from = $request->brand_code_from;
                $brand_to = "zzzzzzzzzz";
            } else if (empty($request->brand_code_from) && !empty($request->brand_code_to)) {
                $brand_from = "";
                $brand_to = $request->brand_code_to;
            } else {
                $brand_from = "";
                $brand_to = "zzzzzzzzzz";
            }
        }

        if($request->has('search')){
            $search = $request->search;
            if (is_null($search) || empty($search) || strlen($search) < 1) {
                $search_text = "%";
            } else {
                $search_text = "%".$search."%";
            }

            $list = DB::table("iv_product as a")
                        ->select("a.*", "c.brand_name", "b.group_name")
                        ->join("iv_product_group as b", "a.group_id", "b.id")
                        ->join("iv_product_brand as c", "a.brand_id", "c.id")
                        ->where("a.company_id", $company_id)
                        ->where("a.principal_id", $request->principal_id)
                        ->whereBetween("b.group_code", [$group_from, $group_to])
                        ->whereBetween("c.brand_code", [$brand_from, $brand_to])
                        ->where("a.active", "Yes")                  
                        ->where(function($query) use($search_text) {
                            $query->where("a.product_code", "LIKE", $search_text)
                                ->orWhere("a.product_name","LIKE",$search_text);
                        })
                        ->take($this->page)
                        ->get();
        }

        $response = array();                
        foreach ($list as $k) {
            $response[] = array( 
                "product_code" =>$k->product_code, 
                "product_name" =>$k->product_name, 
                "group_name" =>$k->group_name, 
                "brand_name" =>$k->brand_name, 
            );
        }

        return response()->json($response);
        exit;
    }
    
    public function getArea(Request $request) {
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;

        if (!empty($request->site_id) && isset($request->site_id)) {
            $list = DB::table("iv_site_area as a")
                        ->leftjoin("iv_site as b", "a.site_id", "b.id")
                        ->join("users_site as c", "a.site_id", "c.site_id")
                        ->where("a.company_id", $company_id)
                        ->where("c.user_id", $user_id)
                        ->where("a.site_id", $request->site_id)
                        ->where("a.active", "Yes")
                        ->get(["a.id", "a.area_name", "a.site_id", "b.site_name"]);
        } else {
            $list = DB::table("iv_site_area as a")
                        ->leftjoin("iv_site as b", "a.site_id", "b.id")
                        ->join("users_site as c", "a.site_id", "c.site_id")
                        ->where("a.company_id", $company_id)
                        ->where("c.user_id", $user_id)
                        ->where("a.active", "Yes")
                        ->get(["a.id", "a.area_name", "a.site_id", "b.site_name"]);
        }

        $data = [
            "area_list" => $list
        ];

        return response()->json($data);
    }
    
    public function getLocation(Request $request) {
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;

        $site_id = "%";
        $area_id = "%";

        if (!empty($request->site_id) && isset($request->site_id)) {
            $site_id = $request->site_id;
        }

        if (!empty($request->area_id) && isset($request->area_id)) {
            $area_id = $request->area_id;
        }

        if($request->has('search')){
            $search = $request->search;
            if (is_null($search) || empty($search) || strlen($search) < 1) {
                $search_text = "%";
            } else {
                $search_text = "%".$search."%";
            }

            $list = DB::table("iv_location as a")
                        ->leftjoin("iv_site as b", "a.site_id", "b.id")
                        ->leftjoin("iv_site_area as c", "a.area_id", "c.id")
                        ->join("users_site as d", "a.site_id", "d.site_id")
                        ->join("iv_principal_site as e", "a.site_id", "e.site_id")
                        ->where("a.company_id", $company_id)
                        ->where("d.user_id", $user_id)
                        ->where("e.principal_id", $request->principal_id)
                        ->where(DB::raw("COALESCE(a.site_id, 0)"), "LIKE", $site_id)
                        ->where(DB::raw("COALESCE(a.area_id, 0)"), "LIKE", $area_id)
                        ->where("a.active", "Yes")
                        ->where('a.location_code','LIKE',$search_text)
                        ->take($this->page)
                        ->get(["a.location_code", "a.area_id", "a.site_id", "c.area_name", "b.site_name"]);
        }

        $response = array();                
        foreach ($list as $k) {
            $response[] = array( 
                "location_code" =>$k->location_code, 
                "site_name" =>$k->site_name, 
                "area_name" =>$k->area_name, 
                "site_id" =>$k->site_id, 
                "area_id" =>$k->area_id, 
            );
        }

        return response()->json($response);
        exit;
    }
}