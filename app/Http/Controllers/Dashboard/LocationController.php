<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    public function index() {
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;

        $site_list = DB::table("iv_site as a")
                        ->join("users_site as b", "a.id", "b.site_id")
                        ->where("a.company_id", $company_id)
                        ->where("b.user_id", $user_id)
                        ->where("a.active", "Yes")
                        // ->where("a.id", 4)
                        ->get();

        if ( count($site_list) == 0 ) {
            $site_id = 0;
        } else {
            $site_id = $site_list->first()->id;
        }

        $area_list = DB::table("iv_site_area as a")
                        ->where("a.company_id", $company_id)
                        ->where("a.active", "Yes")
                        ->where("a.site_id", $site_id)
                        // ->where("a.id", 10)
                        ->get();

        if ( count($area_list) == 0 ) {
            $area_id = 0;
        } else {
            $area_id = $area_list->first()->id;
        }

        $location_list = DB::table("iv_location as a")
                        ->select("a.*", DB::raw("CASE WHEN c.qtys > 0 THEN 'Full' ELSE b.status_name END as status_name"))
                        ->join("iv_location_status as b", "a.status_code", "b.status_code")
                        ->leftjoin("iv_stock_ledger as c", function($query) {
                            $query->on("a.company_id", "c.company_id")
                                  ->on("a.id", "c.location_id")
                                  ->where("c.qtys", ">", 0);
                        })
                        ->where("a.company_id", $company_id)
                        ->where("a.site_id", $site_id)
                        ->where("a.area_id", $area_id)
                        ->where("a.active", "Yes")
                        ->orderBy("a.location_code", "asc")
                        ->get();
                
        $aisle_list = DB::table("iv_location as a")
                        ->select("a.location_aisle")
                        ->where("a.company_id", $company_id)
                        ->where("a.site_id", $site_id)
                        ->where("a.area_id", $area_id)
                        ->where("a.active", "Yes")
                        ->groupBy("a.location_aisle")
                        ->get();
        
        $data = [
            "site_list" => $site_list,
            "area_list" => $area_list,
            "aisle_list" => $aisle_list,
            "location_list" => $location_list,
        ];

        return view("dashboard.location.index", $data);
    }

    public function refresh(Request $request) {
        
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;

        $site_list = DB::table("iv_site as a")
                        ->join("users_site as b", "a.id", "b.site_id")
                        ->where("a.company_id", $company_id)
                        ->where("b.user_id", $user_id)
                        ->where("a.active", "Yes")
                        ->get();
                        
        $site_id = $request->site_id;
        
        $area_list = DB::table("iv_site_area as a")
                        ->where("a.company_id", $company_id)
                        ->where("a.active", "Yes")
                        ->where("a.site_id", $site_id)
                        ->get();
                        
        $area_id = $request->area_id;
                
        $location_list = DB::table("iv_location as a")
                        ->select("a.*", DB::raw("CASE WHEN c.qtys > 0 THEN 'Full' ELSE b.status_name END as status_name"))
                        ->join("iv_location_status as b", "a.status_code", "b.status_code")
                        ->leftjoin("iv_stock_ledger as c", function($query) {
                            $query->on("a.company_id", "c.company_id")
                                  ->on("a.id", "c.location_id")
                                  ->where("c.qtys", ">", 0);
                        })
                        ->where("a.company_id", $company_id)
                        ->where("a.site_id", $site_id)
                        ->where("a.area_id", $area_id)
                        ->where("a.active", "Yes")
                        ->orderBy("a.location_code", "asc")
                        ->get();
                
        $aisle_list = DB::table("iv_location as a")
                        ->select("a.location_aisle")
                        ->where("a.company_id", $company_id)
                        ->where("a.site_id", $site_id)
                        ->where("a.area_id", $area_id)
                        ->where("a.active", "Yes")
                        ->groupBy("a.location_aisle")
                        ->get();
        
        $data = [
            "site_list" => $site_list,
            "area_list" => $area_list,
            "aisle_list" => $aisle_list,
            "location_list" => $location_list,
            "site_id" => $site_id,
            "area_id" => $area_id
        ];

        return view("dashboard.location.index", $data);
    }

    public function getLocationDetail($id) {
        $company_id = Auth::user()->company_id;

        $reserved_list = DB::table("iv_inbound_batch as a")
                        ->select(
                            "c.principal_name",
                            "a.job_no", 
                            "a.product_code", 
                            "b.product_name", 
                            "a.lot_no", 
                            "a.mfg_date", 
                            "a.exp_date", 
                            "a.puom", 
                            "a.muom", 
                            "a.buom",
                            DB::raw("convert((a.qty  - (a.qty % b.uppp)) / b.uppp, int) as pqty"), 
                            DB::raw("convert(((a.qty % b.uppp) - ((a.qty % b.uppp) % b.muppp)) / b.muppp, int) as mqty"),
                            DB::raw("a.qty % b.uppp % b.muppp as bqty"),
                        )
                        ->join("iv_product as b", "a.product_id", "b.id")
                        ->join("iv_principal as c", "a.principal_id", "c.id")
                        ->where("a.company_id", $company_id)
                        ->where("a.location_id", $id)
                        ->where("a.confirmed_flag", "No");

        $records = DB::table("iv_stock_ledger as a")
                        ->select(
                            "c.principal_name",
                            "a.job_no", 
                            "a.product_code", 
                            "b.product_name", 
                            "a.lot_no", 
                            "a.mfg_date", 
                            "a.exp_date", 
                            "a.puom", 
                            "a.muom", 
                            "a.buom",
                            DB::raw("convert((a.qtys  - (a.qtys % b.uppp)) / b.uppp, int) as pqty"), 
                            DB::raw("convert(((a.qtys % b.uppp) - ((a.qtys % b.uppp) % b.muppp)) / b.muppp, int) as mqty"),
                            DB::raw("a.qtys % b.uppp % b.muppp as bqty"),
                        )
                        ->join("iv_product as b", "a.product_id", "b.id")
                        ->join("iv_principal as c", "a.principal_id", "c.id")
                        ->where("a.company_id", $company_id)
                        ->where("a.location_id", $id)
                        ->union($reserved_list)
                        ->get();

        $data_arr = array();

        foreach($records as $record) {  
            $data_arr[] = array(
                "principal_name" => $record->principal_name,
                "product_code" => $record->product_code,
                "product_name" => $record->product_name,
                "lot_no" => $record->lot_no,
                "mfg_date" => \Carbon\Carbon::parse($record->mfg_date)->format("m/d/Y"),
                "exp_date" => \Carbon\Carbon::parse($record->exp_date)->format("m/d/Y"),
                "pqty" => $record->pqty,
                "mqty" => $record->mqty,
                "bqty" => $record->bqty,
                "puom" => $record->puom,
                "muom" => $record->muom,
                "buom" => $record->buom,
            );
        }

        $response = array(
            "aaData" => $data_arr
        );
    
        echo json_encode($response);
        exit;
    }
}