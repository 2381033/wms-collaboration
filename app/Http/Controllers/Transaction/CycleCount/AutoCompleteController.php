<?php

namespace App\Http\Controllers\Transaction\CycleCount;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AutoCompleteController extends Controller
{
    public $page = 20;

    public function productGroupList(Request $request) {
        $data = [];
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;
        
        if($request->has("search")){
            $search = $request->search;
            if (is_null($search) || empty($search) || strlen($search) < 1) {
                $search_text = "%";
            } else {
                $search_text = "%".$search."%";
            }

            $list = DB::table("iv_product as a")
                        ->select("a.group_id", "c.group_code", "c.group_name")
                        ->join("iv_stock_ledger as b", "a.id", "b.product_id")
                        ->join("iv_product_group as c", "a.group_id", "c.id")
                        ->join("users_principal as d", "a.principal_id", "d.principal_id")
                        ->where("a.company_id", $company_id)
                        ->where("d.user_id", $user_id)
                        ->where("a.principal_id", $request->principal_id)
                        ->where("c.group_name","LIKE",$search_text)
                        ->where("a.active", "Yes")
                        ->where("b.qtya", ">", 0)
                        ->where("b.freeze_flag", "No")
                        ->groupby("a.group_id", "c.group_code", "c.group_name")
                        ->take($this->page)
                        ->get();
                    
            foreach ($list as $k) {
                $data[] = array(
                    "group_id" => $k->group_id, 
                    "group_code" => $k->group_code, 
                    "group_name" =>$k->group_name
                );
            }
        }
        return response()->json($data);
    }

    public function productBrandList(Request $request) {
        $data = [];
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;
        
        if($request->has("search")){
            $search = $request->search;
            if (is_null($search) || empty($search) || strlen($search) < 1) {
                $search_text = "%";
            } else {
                $search_text = "%".$search."%";
            }

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

            $list = DB::table("iv_product as a")
                        ->select("a.brand_id", "c.brand_code", "c.brand_name")
                        ->join("iv_stock_ledger as b", "a.id", "b.product_id")
                        ->join("iv_product_brand as c", "a.brand_id", "c.id")
                        ->join("iv_product_group as d", "a.group_id", "d.id")
                        ->join("users_principal as e", "a.principal_id", "e.principal_id")
                        ->where("a.company_id", $company_id)
                        ->where("e.user_id", $user_id)
                        ->where("a.principal_id", $request->principal_id)
                        ->where("c.brand_name","LIKE",$search_text)
                        ->where("a.active", "Yes")
                        ->where("b.qtya", ">", 0)
                        ->where("b.freeze_flag", "No")
                        ->whereBetween("d.group_code", [ $group_from, $group_to])
                        ->groupby("a.brand_id", "c.brand_code", "c.brand_name")
                        ->take($this->page)
                        ->get();
                    
            foreach ($list as $k) {
                $data[] = array(
                    "brand_id" => $k->brand_id, 
                    "brand_code" => $k->brand_code, 
                    "brand_name" =>$k->brand_name
                );
            }
        }
        return response()->json($data);
    }

    public function productStockList(Request $request) {
        $data = [];
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;
        
        if($request->has("search")){
            $search = $request->search;
            if (is_null($search) || empty($search) || strlen($search) < 1) {
                $search_text = "%";
            } else {
                $search_text = "%".$search."%";
            }

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

            $list = DB::table("iv_product as a")
                        ->select("a.id", "a.product_code", "a.product_name", "a.puom", "a.muom", "a.buom", "a.uppp", "a.muppp")
                        ->join("iv_stock_ledger as b", "a.id", "b.product_id")
                        ->join("users_principal as c", "a.principal_id", "c.principal_id")
                        ->join("iv_product_group as d", "a.group_id", "d.id")
                        ->join("iv_product_brand as e", "a.brand_id", "e.id")
                        ->where("a.company_id", $company_id)
                        ->where("c.user_id", $user_id)
                        ->where("a.principal_id", $request->principal_id)
                        ->where("a.product_name","LIKE",$search_text)
                        ->where("a.active", "Yes")
                        ->where("b.qtya", ">", 0)
                        ->where("b.freeze_flag", "No")
                        ->whereBetween("d.group_code", [ $group_from, $group_to])
                        ->whereBetween("e.brand_code", [ $brand_from, $brand_to])
                        ->take($this->page)
                        ->groupby("a.id", "a.product_code", "a.product_name", "a.puom", "a.muom", "a.buom", "a.uppp", "a.muppp")
                        ->get();
                    
            foreach ($list as $k) {
                $data[] = array(
                    "product_id" =>$k->id, 
                    "product_code" =>$k->product_code,
                    "product_name" => $k->product_name                    
                );
            }
        }
        return response()->json($data);
    }

    public function siteStockList(Request $request) {
        $data = [];
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;
        
        if($request->has("search")){
            $search = $request->search;
            if (is_null($search) || empty($search) || strlen($search) < 1) {
                $search_text = "%";
            } else {
                $search_text = "%".$search."%";
            }

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

            if (!empty($request->product_code_from) && !empty($request->product_code_to)) {
                $product_from = $request->product_code_from;
                $product_to = $request->product_code_to;
            } else { 
                if (!empty($request->product_code_from) && empty($request->product_code_to)) {
                    $product_from = $request->product_code_from;
                    $product_to = "zzzzzzzzzzzzzzzzzzzzzzzzzzzzzz";
                } else if (empty($request->brand_code_from) && !empty($request->product_code_to)) {
                    $product_from = "";
                    $product_to = $request->product_code_to;
                } else {
                    $product_from = "";
                    $product_to = "zzzzzzzzzzzzzzzzzzzzzzzzzzzzzz";
                }
            }

            $list = DB::table("iv_stock_ledger as a")
                        ->select("c.id", "c.site_name")
                        ->join("users_site as b", "a.site_id", "b.site_id")
                        ->leftjoin("iv_site as c", "a.site_id", "c.id")
                        ->join("iv_product as d", "a.product_id", "d.id")
                        ->join("iv_product_group as e", "d.group_id", "e.id")
                        ->join("iv_product_brand as f", "d.brand_id", "f.id")
                        ->where("a.company_id", $company_id)
                        ->where("b.user_id", $user_id)
                        ->where("a.principal_id", $request->principal_id)
                        ->where("d.product_name","LIKE",$search_text)
                        ->where("c.active", "Yes")
                        ->where("a.qtya", ">", 0)
                        ->where("a.freeze_flag", "No")
                        ->whereBetween("e.group_code", [ $group_from, $group_to])
                        ->whereBetween("f.brand_code", [ $brand_from, $brand_to])
                        ->whereBetween("d.product_code", [ $product_from, $product_to])
                        ->take($this->page)
                        ->groupby("c.id", "c.site_name")
                        ->get();
                    
            foreach ($list as $k) {
                $data[] = array(
                    "site_id" =>$k->id, 
                    "site_name" =>$k->site_name                    
                );
            }
        }
        return response()->json($data);
    }

    public function siteAreaStockList(Request $request) {
        $data = [];
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;
        
        if($request->has("search")){
            $search = $request->search;
            if (is_null($search) || empty($search) || strlen($search) < 1) {
                $search_text = "%";
            } else {
                $search_text = "%".$search."%";
            }

            $site_id = $request->site_id;
            if (is_null($request->site_id) || empty($request->site_id)) {
                $site_id = "%";
            }

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

            if (!empty($request->product_code_from) && !empty($request->product_code_to)) {
                $product_from = $request->product_code_from;
                $product_to = $request->product_code_to;
            } else { 
                if (!empty($request->product_code_from) && empty($request->product_code_to)) {
                    $product_from = $request->product_code_from;
                    $product_to = "zzzzzzzzzzzzzzzzzzzzzzzzzzzzzz";
                } else if (empty($request->brand_code_from) && !empty($request->product_code_to)) {
                    $product_from = "";
                    $product_to = $request->product_code_to;
                } else {
                    $product_from = "";
                    $product_to = "zzzzzzzzzzzzzzzzzzzzzzzzzzzzzz";
                }
            }

            $list = DB::table("iv_stock_ledger as a")
                        ->select("c.id", "c.area_name")
                        ->join("users_site as b", "a.site_id", "b.site_id")
                        ->leftjoin("iv_site_area as c", "a.area_id", "c.id")
                        ->join("iv_product as d", "a.product_id", "d.id")
                        ->join("iv_product_group as e", "d.group_id", "e.id")
                        ->join("iv_product_brand as f", "d.brand_id", "f.id")
                        ->where("a.company_id", $company_id)
                        ->where("b.user_id", $user_id)
                        ->where("a.principal_id", $request->principal_id)
                        ->where("a.site_id", "LIKE", $site_id)
                        ->where("d.product_name","LIKE",$search_text)
                        ->where("c.active", "Yes")
                        ->where("a.qtya", ">", 0)
                        ->where("a.freeze_flag", "No")
                        ->whereBetween("e.group_code", [ $group_from, $group_to])
                        ->whereBetween("f.brand_code", [ $brand_from, $brand_to])
                        ->whereBetween("d.product_code", [ $product_from, $product_to])
                        ->take($this->page)
                        ->groupby("c.id", "c.area_name")
                        ->get();
                    
            foreach ($list as $k) {
                $data[] = array(
                    "area_id" =>$k->id, 
                    "area_name" =>$k->area_name                    
                );
            }
        }
        return response()->json($data);
    }

    public function locationStockList(Request $request) {
        $data = [];
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;
        
        if($request->has("search")){
            $search = $request->search;
            if (is_null($search) || empty($search) || strlen($search) < 1) {
                $search_text = "%";
            } else {
                $search_text = "%".$search."%";
            }

            $site_id = $request->site_id;
            if (is_null($request->site_id) || empty($request->site_id)) {
                $site_id = "%";
            }

            $area_id = $request->area_id;
            if (is_null($request->area_id) || empty($request->area_id)) {
                $area_id = "%";
            }

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

            if (!empty($request->product_code_from) && !empty($request->product_code_to)) {
                $product_from = $request->product_code_from;
                $product_to = $request->product_code_to;
            } else { 
                if (!empty($request->product_code_from) && empty($request->product_code_to)) {
                    $product_from = $request->product_code_from;
                    $product_to = "zzzzzzzzzzzzzzzzzzzzzzzzzzzzzz";
                } else if (empty($request->brand_code_from) && !empty($request->product_code_to)) {
                    $product_from = "";
                    $product_to = $request->product_code_to;
                } else {
                    $product_from = "";
                    $product_to = "zzzzzzzzzzzzzzzzzzzzzzzzzzzzzz";
                }
            }

            $list = DB::table("iv_stock_ledger as a")
                        ->select("a.location_id", "c.location_code")
                        ->join("users_site as b", "a.site_id", "b.site_id")
                        ->join("iv_location as c", "a.location_id", "c.id")
                        ->join("iv_product as d", "a.product_id", "d.id")
                        ->join("iv_product_group as e", "d.group_id", "e.id")
                        ->join("iv_product_brand as f", "d.brand_id", "f.id")
                        ->where("a.company_id", $company_id)
                        ->where("b.user_id", $user_id)
                        ->where("a.principal_id", $request->principal_id)
                        ->where("a.site_id", "LIKE", $site_id)
                        ->where("a.area_id", "LIKE", $area_id)
                        ->where("d.product_name","LIKE",$search_text)
                        ->where("c.active", "Yes")
                        ->where("a.qtya", ">", 0)
                        ->where("a.freeze_flag", "No")
                        ->whereBetween("e.group_code", [ $group_from, $group_to])
                        ->whereBetween("f.brand_code", [ $brand_from, $brand_to])
                        ->whereBetween("d.product_code", [ $product_from, $product_to])
                        ->take($this->page)
                        ->groupby("a.location_id", "c.location_code")
                        ->get();
                    
            foreach ($list as $k) {
                $data[] = array(
                    "location_id" =>$k->location_id, 
                    "location_code" =>$k->location_code                    
                );
            }
        }
        return response()->json($data);
    }
}