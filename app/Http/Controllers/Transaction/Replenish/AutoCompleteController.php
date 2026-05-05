<?php

namespace App\Http\Controllers\Transaction\Replenish;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Master\Product as MasterProduct;

class AutoCompleteController extends Controller
{
    public $page = 20;

    public function productList(Request $request) {
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;

        if($request->has("q")){
            $search = $request->q;
            if (is_null($search) || empty($search) || strlen($search) < 1) {
                $search_text = "%";
            } else {
                $search_text = "%".$search."%";
            }

            $list = DB::table("iv_location as a")
                        ->select("a.product_id", "b.product_code", "b.product_name")
                        ->join("iv_product as b", "a.product_id", "b.id")
                        ->join("users_principal as c", "a.principal_id", "c.principal_id")
                        ->where("a.company_id", $company_id)
                        ->where("c.user_id", $user_id)
                        ->where("a.principal_id", $request->principal_id)
                        ->where("b.product_name","LIKE", $search_text)
                        ->where("a.status_code", "P")
                        ->take($this->page)
                        ->groupBy("a.product_id", "b.product_code", "b.product_name")
                        ->get();

            foreach ($list as $k) {
                $data[] = array(
                    "product_id" => $k->product_id,
                    "product_code" => $k->product_code,
                    "product_name" =>$k->product_name
                );
            }
        }
        return response()->json($data);
    }

    public function siteList(Request $request) {
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;

        if($request->has("q")){
            $search = $request->q;
            if (is_null($search) || empty($search) || strlen($search) < 1) {
                $search_text = "%";
            } else {
                $search_text = "%".$search."%";
            }

            if (!empty($request->product_from) && !empty($request->product_to)) {
                $product_from = $request->product_from;
                $product_to = $request->product_to;
            } else {
                if (!empty($request->product_from) && empty($request->product_to)) {
                    $product_from = $request->product_from;
                    $product_to = "zzzzzzzzzzzzzzzzzzzzzzzzzzzzzz";
                } else if (empty($request->brand_code_from) && !empty($request->product_to)) {
                    $product_from = "";
                    $product_to = $request->product_to;
                } else {
                    $product_from = "";
                    $product_to = "zzzzzzzzzzzzzzzzzzzzzzzzzzzzzz";
                }
            }

            $list = DB::table("iv_location as a")
                        ->select("a.site_id", "b.site_name")
                        ->leftjoin("iv_site as b", "a.site_id", "b.id")
                        ->join("users_site as c", "a.site_id", "c.site_id")
                        ->join("users_principal as d", "a.principal_id", "d.principal_id")
                        ->join("iv_product as e", "a.product_id", "e.id")
                        ->where("a.company_id", $company_id)
                        ->where("c.user_id", $user_id)
                        ->where("d.user_id", $user_id)
                        ->where("a.principal_id", $request->principal_id)
                        ->whereBetween("e.product_code", [$product_from, $product_to])
                        ->where("b.site_name","LIKE", $search_text)
                        ->where("a.status_code", "P")
                        ->take($this->page)
                        ->groupBy("a.site_id", "b.site_name")
                        ->get();

            foreach ($list as $k) {
                $data[] = array("site_id" => $k->site_id, "site_name" =>$k->site_name);
            }
        }
        return response()->json($data);
    }

    public function areaList(Request $request) {
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;
        $productMax = MasterProduct::max("id");

        if($request->has("q")){
            $search = $request->q;
            if (is_null($search) || empty($search) || strlen($search) < 1) {
                $search_text = "%";
            } else {
                $search_text = "%".$search."%";
            }

            $site = $request->site_id;
            if (is_null($request->site_id) || empty($request->site_id)) {
                $site = "%";
            }

            if (!empty($request->product_from) && !empty($request->product_to)) {
                $product_from = $request->product_from;
                $product_to = $request->product_to;
            } else {
                if (!empty($request->product_from) && empty($request->product_to)) {
                    $product_from = $request->product_from;
                    $product_to = "zzzzzzzzzzzzzzzzzzzzzzzzzzzzzz";
                } else if (empty($request->brand_code_from) && !empty($request->product_to)) {
                    $product_from = "";
                    $product_to = $request->product_to;
                } else {
                    $product_from = "";
                    $product_to = "zzzzzzzzzzzzzzzzzzzzzzzzzzzzzz";
                }
            }

            $list = DB::table("iv_location as a")
                        ->select("a.area_id", "b.area_name")
                        ->leftjoin("iv_site_area as b", "a.area_id", "b.id")
                        ->join("users_site as c", "a.site_id", "c.site_id")
                        ->join("users_principal as d", "a.principal_id", "d.principal_id")
                        ->join("iv_product as e", "a.product_id", "e.id")
                        ->where("a.company_id", $company_id)
                        ->where("c.user_id", $user_id)
                        ->where("d.user_id", $user_id)
                        ->where("a.principal_id", $request->principal_id)
                        ->whereBetween("e.product_code", [$product_from, $product_to])
                        ->where("a.site_id", "LIKE", $site)
                        ->where("b.area_name","LIKE", $search_text)
                        ->where("a.status_code", "P")
                        ->take($this->page)
                        ->groupBy("a.area_id", "b.area_name")
                        ->get();

            foreach ($list as $k) {
                $data[] = array("area_id" => $k->area_id, "area_name" =>$k->area_name);
            }
        }
        return response()->json($data);
    }

    public function locationList(Request $request) {
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;
        $productMax = MasterProduct::max("id");

        if($request->has("q")){
            $search = $request->q;
            if (is_null($search) || empty($search) || strlen($search) < 1) {
                $search_text = "%";
            } else {
                $search_text = "%".$search."%";
            }

            $site = $request->site_id;
            if (is_null($request->site_id) || empty($request->site_id)) {
                $site = "%";
            }

            $area = $request->area_id;
            if (is_null($request->area_id) || empty($request->area_id)) {
                $area = "%";
            }

            if (!empty($request->product_from) && !empty($request->product_to)) {
                $product_from = $request->product_from;
                $product_to = $request->product_to;
            } else {
                if (!empty($request->product_from) && empty($request->product_to)) {
                    $product_from = $request->product_from;
                    $product_to = "zzzzzzzzzzzzzzzzzzzzzzzzzzzzzz";
                } else if (empty($request->brand_code_from) && !empty($request->product_to)) {
                    $product_from = "";
                    $product_to = $request->product_to;
                } else {
                    $product_from = "";
                    $product_to = "zzzzzzzzzzzzzzzzzzzzzzzzzzzzzz";
                }
            }

            $list = DB::table("iv_location as a")
                        ->select("a.id", "a.location_code")
                        ->leftjoin("iv_site_area as b", "a.area_id", "b.id")
                        ->join("users_site as c", "a.site_id", "c.site_id")
                        ->join("users_principal as d", "a.principal_id", "d.principal_id")
                        ->join("iv_product as e", "a.product_id", "e.id")
                        ->where("a.company_id", $company_id)
                        ->where("c.user_id", $user_id)
                        ->where("d.user_id", $user_id)
                        ->where("a.principal_id", $request->principal_id)
                        ->whereBetween("e.product_code", [$product_from, $product_to])
                        ->where("a.site_id", "like", $site)
                        ->where("a.area_id", "like", $area)
                        ->where("a.location_code","LIKE", $search_text)
                        ->where("a.status_code", "P")
                        ->take($this->page)
                        ->groupBy("a.id", "a.location_code")
                        ->get();

            foreach ($list as $k) {
                $data[] = array("location_id" => $k->id, "location_code" =>$k->location_code);
            }
        }
        return response()->json($data);
    }
}
