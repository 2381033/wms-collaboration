<?php

namespace App\Http\Controllers\Transaction\CycleCount;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BatchController extends Controller
{
    public function index(Request $request) {
        $details = [];
        if ($request->ajax()) {
            if (!empty($request->cycle_id) && !empty($request->cycle_id)) {
                $details = DB::table("iv_cyclecount_batch as a")
                    ->select(
                        "a.lot_no", 
                        DB::raw("abs(convert((a.qtya  - (a.qtya % b.uppp)) / b.uppp, int)) as pqty"), 
                        DB::raw("abs(convert(((a.qtya % b.uppp) - ((a.qtya % b.uppp) % b.muppp)) / b.muppp, int)) as mqty"),
                        DB::raw("abs(a.qtya % b.uppp % b.muppp) as bqty"), "b.puom", "b.muom", "b.buom", 
                        "b.product_name", "c.site_name", "d.area_name", "a.location_code"
                    )
                    ->join("iv_product as b", "a.product_id", "b.id")
                    ->leftjoin("iv_site as c", "a.site_id", "c.id")
                    ->leftjoin("iv_site_area as d", "a.area_id", "d.id")
                    ->where("a.cyclecount_id", "=", $request->cycle_id)
                    ->where("a.confirmed_flag", "=", "No")
                    ->get();
            } 
        
            return datatables()->of($details)
            ->addIndexColumn()       
            ->make(true);
        }
    }
}