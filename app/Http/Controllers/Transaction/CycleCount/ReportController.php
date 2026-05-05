<?php

namespace App\Http\Controllers\Transaction\CycleCount;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index($type, $id) {
        switch ($type) {
            case "blank":
                $stockList = DB::table("iv_cyclecount_detail as a")
                                ->select(
                                    "a.product_code", 
                                    "b.product_name", 
                                    "a.lot_no", 
                                    "a.mfg_date",
                                    "a.exp_date", 
                                    "c.site_name", 
                                    "d.area_name", 
                                    "a.location_code", 
                                    "b.uppp",
                                    "b.muppp",
                                    "a.pqty",
                                    "a.mqty",
                                    "a.bqty",
                                    "a.qty",
                                    "b.puom",
                                    "b.muom",
                                    "b.buom"
                                    )
                                ->join("iv_product as b", "a.product_id", "b.id")
                                ->leftjoin("iv_site as c", "a.site_id", "c.id")
                                ->leftjoin("iv_site_area as d", "a.area_id", "d.id")
                                ->where("a.CycleCount_id", "=", $id)
                                ->get();
                
                $job = DB::table("iv_cyclecount_job as a")
                            ->select("a.*", "b.principal_name", "b.multi_level")
                            ->join("iv_principal as b", "a.principal_id", "b.id")
                            ->where("a.id", "=", $id)
                            ->first();
                
                $headerOne = "<tr><td>Principal Name</td><td>:</td><td>$job->principal_name</td></tr>";
                $headerOne .= "<tr><td>Job Number</td><td>:</td><td>$job->cyclecount_no</td></tr>";
                $headerOne .= "<tr><td>Job Date</td><td>:</td><td>".date("d-m-Y", strtotime($job->cyclecount_date))."</td></tr>";
                
                if ($job->multi_level == "Yes") {
                    $headOne = collect([
                        [ "name"=>"SKU No.", "rowspan"=>"2", "colspan"=>"1" ], 
                        [ "name"=>"SKU Name", "rowspan"=>"2", "colspan"=>"1" ], 
                        [ "name"=>"Location", "rowspan"=>"1", "colspan"=>"3" ],
                        [ "name"=>"Actual Quantity", "rowspan"=>"1", "colspan"=>"6" ]
                    ]);

                    $headTwo = collect([                    
                        [ "name"=>"Site" ], 
                        [ "name"=>"Area" ], 
                        [ "name"=>"Location" ],
                        [ "name"=>"1st Qty" ], 
                        [ "name"=>"1st Unit" ], 
                        [ "name"=>"2nd Qty" ], 
                        [ "name"=>"2nd Unit" ], 
                        [ "name"=>"3rd Qty" ],
                        [ "name"=>"3rd Unit" ], 
                    ]);

                    $bodyOne = collect([
                        [ "name"=>"SKU No.", "field_name"=>"product_code", "class"=>"left", "colspan"=>"1" ],
                        [ "name"=>"SKU Name", "field_name"=>"product_name", "class"=>"left", "colspan"=>"1" ],
                        [ "name"=>"Site", "field_name"=>"site_name", "class"=>"left", "colspan"=>"1" ], 
                        [ "name"=>"Area", "field_name"=>"area_name", "class"=>"left", "colspan"=>"1" ], 
                        [ "name"=>"Location", "field_name"=>"location_code", "class"=>"center", "colspan"=>"1" ],
                        [ "name"=>"1st", "field_name"=>"actual_pqty", "class"=>"blank-cell", "colspan"=>"1" ], 
                        [ "name"=>"1st", "field_name"=>"puom", "class"=>"center", "colspan"=>"1" ], 
                        [ "name"=>"2nd", "field_name"=>"actual_mqty", "class"=>"blank-cell", "colspan"=>"1" ],
                        [ "name"=>"2nd", "field_name"=>"muom", "class"=>"center", "colspan"=>"1" ], 
                        [ "name"=>"3rd", "field_name"=>"actual_bqty", "class"=>"blank-cell", "colspan"=>"1" ], 
                        [ "name"=>"3rd", "field_name"=>"buom", "class"=>"center", "colspan"=>"1" ],
                    ]);

                    $columnCount = 11;
                } else {
                    $headOne = collect([
                        [ "name"=>"SKU No.", "rowspan"=>"2", "colspan"=>"1" ], 
                        [ "name"=>"SKU Name", "rowspan"=>"2", "colspan"=>"1" ], 
                        [ "name"=>"Location", "rowspan"=>"1", "colspan"=>"3" ],
                        [ "name"=>"Actual Quantity", "rowspan"=>"1", "colspan"=>"2" ]
                    ]);

                    $headTwo = collect([                    
                        [ "name"=>"Site" ], 
                        [ "name"=>"Area" ], 
                        [ "name"=>"Location" ],
                        [ "name"=>"1st Qty" ], 
                        [ "name"=>"1st Unit" ], 
                    ]);

                    $bodyOne = collect([
                        [ "name"=>"SKU No.", "field_name"=>"product_code", "class"=>"left", "colspan"=>"1" ],
                        [ "name"=>"SKU Name", "field_name"=>"product_name", "class"=>"left", "colspan"=>"1" ],
                        [ "name"=>"Site", "field_name"=>"site_name", "class"=>"left", "colspan"=>"1" ], 
                        [ "name"=>"Area", "field_name"=>"area_name", "class"=>"left", "colspan"=>"1" ], 
                        [ "name"=>"Location", "field_name"=>"location_code", "class"=>"center", "colspan"=>"1" ],
                        [ "name"=>"1st", "field_name"=>"actual_pqty", "class"=>"blank-cell", "colspan"=>"1" ], 
                        [ "name"=>"1st", "field_name"=>"puom", "class"=>"center", "colspan"=>"1" ], 
                    ]);

                    $columnCount = 7;
                }

                $listData = [];
                foreach ($stockList as $value) {
                    $listData[] = [
                        "product_code"=>$value->product_code,
                        "product_name"=>$value->product_name,
                        "lot_no"=>$value->lot_no,
                        "site_name"=>$value->site_name,
                        "area_name"=>$value->area_name,
                        "location_code"=>$value->location_code,
                        "pqty"=>number_format($value->pqty, 0, ",", "."),
                        "mqty"=>number_format($value->mqty, 0, ",", "."),
                        "bqty"=>number_format($value->bqty, 0, ",", "."),
                        "actual_pqty"=>"",
                        "actual_mqty"=>"",
                        "actual_bqty"=>"",
                        "puom"=>$value->puom,
                        "muom"=>$value->muom,
                        "buom"=>$value->buom
                    ];
                }

                $signature = "<tr><td>Prepare By:</td><td>Checked By:</td><td>Supervised By:</td></tr>";
                $signature .= "<tr><td>Date:</td><td>Date:</td><td>Date:</td></tr>";
                $signature .= "<tr><td>Signature:</td><td>Signature:</td><td>Signature:</td></tr>";
                
                $data = [
                    "title"=>"Blank Form",
                    "css"=>"landscape",
                    "headerOne"=>$headerOne,
                    "headOne"=>$headOne->toArray(),
                    "headTwo"=>$headTwo->toArray(),
                    "bodyOne"=>$bodyOne->toArray(),
                    "listData"=>$listData,
                    "signature"=>$signature,
                    "columnCount"=>$columnCount
                ];

                return view("report", $data);
                break;
            case "book":
                $stockList = DB::table("iv_cyclecount_detail as a")
                                ->select(
                                    "a.product_code", 
                                    "b.product_name", 
                                    "a.lot_no", 
                                    "a.mfg_date",
                                    "a.exp_date", 
                                    "c.site_name", 
                                    "d.area_name", 
                                    "a.location_code", 
                                    "b.uppp",
                                    "b.muppp",
                                    "a.pqty",
                                    "a.mqty",
                                    "a.bqty",
                                    "a.qty",
                                    "b.puom",
                                    "b.muom",
                                    "b.buom"
                                    )
                                ->join("iv_product as b", "a.product_id", "b.id")
                                ->leftjoin("iv_site as c", "a.site_id", "c.id")
                                ->leftjoin("iv_site_area as d", "a.area_id", "d.id")
                                ->where("a.CycleCount_id", "=", $id)
                                ->get();
                
                $job = DB::table("iv_cyclecount_job as a")
                            ->select("a.*", "b.principal_name", "b.multi_level")
                            ->join("iv_principal as b", "a.principal_id", "b.id")
                            ->where("a.id", "=", $id)
                            ->first();
                
                $headerOne = "<tr><td>Principal Name</td><td>:</td><td>$job->principal_name</td></tr>";
                $headerOne .= "<tr><td>Job Number</td><td>:</td><td>$job->cyclecount_no</td></tr>";
                $headerOne .= "<tr><td>Job Date</td><td>:</td><td>".date("d-m-Y", strtotime($job->cyclecount_date))."</td></tr>";
                
                if ($job->multi_level == "Yes") {
                    $headOne = collect([
                        [ "name"=>"SKU No.", "rowspan"=>"2", "colspan"=>"1" ], 
                        [ "name"=>"SKU Name", "rowspan"=>"2", "colspan"=>"1" ], 
                        [ "name"=>"Location", "rowspan"=>"1", "colspan"=>"3" ],
                        [ "name"=>"Book Quantity", "rowspan"=>"1", "colspan"=>"6" ],
                        [ "name"=>"Actual Quantity", "rowspan"=>"1", "colspan"=>"6" ]
                    ]);

                    $headTwo = collect([                    
                        [ "name"=>"Site" ], 
                        [ "name"=>"Area" ], 
                        [ "name"=>"Location" ],
                        [ "name"=>"1st Qty" ], 
                        [ "name"=>"1st Unit" ], 
                        [ "name"=>"2nd Qty" ], 
                        [ "name"=>"2nd Unit" ], 
                        [ "name"=>"3rd Qty" ],
                        [ "name"=>"3rd Unit" ], 
                        [ "name"=>"1st Qty" ], 
                        [ "name"=>"1st Unit" ], 
                        [ "name"=>"2nd Qty" ], 
                        [ "name"=>"2nd Unit" ], 
                        [ "name"=>"3rd Qty" ],
                        [ "name"=>"3rd Unit" ], 
                    ]);

                    $bodyOne = collect([
                        [ "name"=>"SKU No.", "field_name"=>"product_code", "class"=>"left", "colspan"=>"1" ],
                        [ "name"=>"SKU Name", "field_name"=>"product_name", "class"=>"left", "colspan"=>"1" ],
                        [ "name"=>"Site", "field_name"=>"site_name", "class"=>"left", "colspan"=>"1" ], 
                        [ "name"=>"Area", "field_name"=>"area_name", "class"=>"left", "colspan"=>"1" ], 
                        [ "name"=>"Location", "field_name"=>"location_code", "class"=>"center", "colspan"=>"1" ],
                        [ "name"=>"1st", "field_name"=>"pqty", "class"=>"blank-cell right", "colspan"=>"1" ], 
                        [ "name"=>"1st", "field_name"=>"puom", "class"=>"center", "colspan"=>"1" ], 
                        [ "name"=>"2nd", "field_name"=>"mqty", "class"=>"blank-cell right", "colspan"=>"1" ],
                        [ "name"=>"2nd", "field_name"=>"muom", "class"=>"center", "colspan"=>"1" ], 
                        [ "name"=>"3rd", "field_name"=>"bqty", "class"=>"blank-cell right", "colspan"=>"1" ], 
                        [ "name"=>"3rd", "field_name"=>"buom", "class"=>"center", "colspan"=>"1" ],
                        [ "name"=>"1st", "field_name"=>"actual_pqty", "class"=>"blank-cell", "colspan"=>"1" ], 
                        [ "name"=>"1st", "field_name"=>"puom", "class"=>"center", "colspan"=>"1" ], 
                        [ "name"=>"2nd", "field_name"=>"actual_mqty", "class"=>"blank-cell", "colspan"=>"1" ],
                        [ "name"=>"2nd", "field_name"=>"muom", "class"=>"center", "colspan"=>"1" ], 
                        [ "name"=>"3rd", "field_name"=>"actual_bqty", "class"=>"blank-cell", "colspan"=>"1" ], 
                        [ "name"=>"3rd", "field_name"=>"buom", "class"=>"center", "colspan"=>"1" ],
                    ]);

                    $columnCount = 11;
                } else {
                    $headOne = collect([
                        [ "name"=>"SKU No.", "rowspan"=>"2", "colspan"=>"1" ], 
                        [ "name"=>"SKU Name", "rowspan"=>"2", "colspan"=>"1" ], 
                        [ "name"=>"Location", "rowspan"=>"1", "colspan"=>"3" ],
                        [ "name"=>"Book Quantity", "rowspan"=>"1", "colspan"=>"2" ],
                        [ "name"=>"Actual Quantity", "rowspan"=>"1", "colspan"=>"2" ]
                    ]);

                    $headTwo = collect([                    
                        [ "name"=>"Site" ], 
                        [ "name"=>"Area" ], 
                        [ "name"=>"Location" ],
                        [ "name"=>"1st Qty" ], 
                        [ "name"=>"1st Unit" ], 
                        [ "name"=>"1st Qty" ], 
                        [ "name"=>"1st Unit" ], 
                    ]);

                    $bodyOne = collect([
                        [ "name"=>"SKU No.", "field_name"=>"product_code", "class"=>"left", "colspan"=>"1" ],
                        [ "name"=>"SKU Name", "field_name"=>"product_name", "class"=>"left", "colspan"=>"1" ],
                        [ "name"=>"Site", "field_name"=>"site_name", "class"=>"left", "colspan"=>"1" ], 
                        [ "name"=>"Area", "field_name"=>"area_name", "class"=>"left", "colspan"=>"1" ], 
                        [ "name"=>"Location", "field_name"=>"location_code", "class"=>"center", "colspan"=>"1" ],
                        [ "name"=>"1st", "field_name"=>"pqty", "class"=>"blank-cell right", "colspan"=>"1" ], 
                        [ "name"=>"1st", "field_name"=>"puom", "class"=>"center", "colspan"=>"1" ], 
                        [ "name"=>"1st", "field_name"=>"actual_pqty", "class"=>"blank-cell right", "colspan"=>"1" ], 
                        [ "name"=>"1st", "field_name"=>"puom", "class"=>"center", "colspan"=>"1" ], 
                    ]);

                    $columnCount = 9;
                }
            
                $listData = [];
                foreach ($stockList as $value) {
                    $listData[] = [
                        "product_code"=>$value->product_code,
                        "product_name"=>$value->product_name,
                        "lot_no"=>$value->lot_no,
                        "site_name"=>$value->site_name,
                        "area_name"=>$value->area_name,
                        "location_code"=>$value->location_code,
                        "mfg_date"=> isset($value->mfg_date) ? \Carbon\Carbon::parse($value->mfg_date)->format("d-m-Y") : "",
                        "exp_date"=> isset($value->exp_date) ? \Carbon\Carbon::parse($value->exp_date)->format("d-m-Y") : "",
                        "pqty"=>number_format($value->pqty, 0, ",", "."),
                        "mqty"=>number_format($value->mqty, 0, ",", "."),
                        "bqty"=>number_format($value->bqty, 0, ",", "."),
                        "actual_pqty"=>"",
                        "actual_mqty"=>"",
                        "actual_bqty"=>"",
                        "puom"=>$value->puom,
                        "muom"=>$value->muom,
                        "buom"=>$value->buom
                    ];
                }

                $signature = "<tr><td>Prepare By:</td><td>Checked By:</td><td>Supervised By:</td></tr>";
                $signature .= "<tr><td>Date:</td><td>Date:</td><td>Date:</td></tr>";
                $signature .= "<tr><td>Signature:</td><td>Signature:</td><td>Signature:</td></tr>";
                
                $data = [
                    "title"=>"Book Form",
                    "css"=>"landscape",
                    "headerOne"=>$headerOne,
                    "headOne"=>$headOne->toArray(),
                    "headTwo"=>$headTwo->toArray(),
                    "bodyOne"=>$bodyOne->toArray(),
                    "listData"=>$listData,
                    "signature"=>$signature,
                    "columnCount"=>$columnCount
                ];

                return view("report", $data);
                break;
            case "release":
                $stockList = DB::table("iv_cyclecount_detail as a")
                                ->select(
                                    "a.product_code", 
                                    "b.product_name", 
                                    "a.lot_no", 
                                    "a.mfg_date", 
                                    "a.exp_date", 
                                    "c.site_name", 
                                    "d.area_name", 
                                    "a.location_code", 
                                    "b.uppp",
                                    "b.muppp",
                                    "a.pqty",
                                    "a.mqty",
                                    "a.bqty",
                                    "a.qty",
                                    "a.actual_pqty",
                                    "a.actual_mqty",
                                    "a.actual_bqty",
                                    "a.actual_qty",
                                    "b.puom",
                                    "b.muom",
                                    "b.buom",                                    
                                    "a.actual_lot_no", 
                                    "a.actual_mfg_date", 
                                    "a.actual_exp_date", 
                                    )
                                ->join("iv_product as b", "a.product_id", "b.id")
                                ->leftjoin("iv_site as c", "a.site_id", "c.id")
                                ->leftjoin("iv_site_area as d", "a.area_id", "d.id")
                                ->where("a.CycleCount_id", "=", $id)
                                ->where("a.confirmed_flag", "=", "Yes")
                                ->where(DB::raw("CASE WHEN a.pqty = a.actual_pqty AND a.mqty = a.actual_mqty AND a.bqty = a.actual_bqty THEN 1 ELSE 0 END"), "=", 1)
                                ->get();
                
                $job = DB::table("iv_cyclecount_job as a")
                            ->select("a.*", "b.principal_name", "b.multi_level")
                            ->join("iv_principal as b", "a.principal_id", "b.id")
                            ->where("a.id", "=", $id)
                            ->first();
                
                $headerOne = "<tr><td>Principal Name</td><td>:</td><td>$job->principal_name</td></tr>";
                $headerOne .= "<tr><td>Job Number</td><td>:</td><td>$job->cyclecount_no</td></tr>";
                $headerOne .= "<tr><td>Job Date</td><td>:</td><td>".date("d-m-Y", strtotime($job->cyclecount_date))."</td></tr>";
                
                if ($job->multi_level == "Yes") {
                    $headOne = collect([
                        [ "name"=>"SKU No.", "rowspan"=>"2", "colspan"=>"1" ], 
                        [ "name"=>"SKU Name", "rowspan"=>"2", "colspan"=>"1" ], 
                        [ "name"=>"Batch", "rowspan"=>"2", "colspan"=>"1" ], 
                        [ "name"=>"Mfg Date", "rowspan"=>"2", "colspan"=>"1" ], 
                        [ "name"=>"Exp Date", "rowspan"=>"2", "colspan"=>"1" ], 
                        [ "name"=>"Location", "rowspan"=>"1", "colspan"=>"3" ],
                        [ "name"=>"Book Quantity", "rowspan"=>"1", "colspan"=>"3" ],
                        [ "name"=>"Actual Quantity", "rowspan"=>"1", "colspan"=>"3" ],
                        [ "name"=>"Unit", "rowspan"=>"1", "colspan"=>"3" ]
                    ]);

                    $headTwo = collect([                    
                        [ "name"=>"Site" ], 
                        [ "name"=>"Area" ], 
                        [ "name"=>"Location" ],
                        [ "name"=>"1st" ], 
                        [ "name"=>"2nd" ], 
                        [ "name"=>"3rd" ],
                        [ "name"=>"1st" ], 
                        [ "name"=>"2nd" ], 
                        [ "name"=>"3rd" ],
                        [ "name"=>"1st" ], 
                        [ "name"=>"2nd" ], 
                        [ "name"=>"3rd" ],
                    ]);

                    $bodyOne = collect([
                        [ "name"=>"SKU No.", "field_name"=>"product_code", "class"=>"left", "colspan"=>"1" ],
                        [ "name"=>"SKU Name", "field_name"=>"product_name", "class"=>"left", "colspan"=>"1" ],
                        [ "name"=>"Batch", "field_name"=>"lot_no", "class"=>"left", "colspan"=>"1" ], 
                        [ "name"=>"Mfg", "field_name"=>"mfg_date", "class"=>"center", "colspan"=>"1" ], 
                        [ "name"=>"Expired", "field_name"=>"exp_date", "class"=>"center", "colspan"=>"1" ],
                        [ "name"=>"Site", "field_name"=>"site_name", "class"=>"left", "colspan"=>"1" ], 
                        [ "name"=>"Area", "field_name"=>"area_name", "class"=>"left", "colspan"=>"1" ], 
                        [ "name"=>"Location", "field_name"=>"location_code", "class"=>"center", "colspan"=>"1" ],
                        [ "name"=>"1st", "field_name"=>"pqty", "class"=>"right", "colspan"=>"1" ], 
                        [ "name"=>"2nd", "field_name"=>"mqty", "class"=>"right", "colspan"=>"1" ],
                        [ "name"=>"3rd", "field_name"=>"bqty", "class"=>"right", "colspan"=>"1" ], 
                        [ "name"=>"1st", "field_name"=>"actual_pqty", "class"=>"right", "colspan"=>"1" ], 
                        [ "name"=>"2nd", "field_name"=>"actual_mqty", "class"=>"right", "colspan"=>"1" ],
                        [ "name"=>"3rd", "field_name"=>"actual_bqty", "class"=>"right", "colspan"=>"1" ], 
                        [ "name"=>"1st", "field_name"=>"puom", "class"=>"center", "colspan"=>"1" ], 
                        [ "name"=>"2nd", "field_name"=>"muom", "class"=>"center", "colspan"=>"1" ], 
                        [ "name"=>"3rd", "field_name"=>"buom", "class"=>"center", "colspan"=>"1" ],
                    ]);

                    $columnCount = 17;
                } else {
                    $headOne = collect([
                        [ "name"=>"SKU No.", "rowspan"=>"2", "colspan"=>"1" ], 
                        [ "name"=>"SKU Name", "rowspan"=>"2", "colspan"=>"1" ], 
                        [ "name"=>"Batch", "rowspan"=>"2", "colspan"=>"1" ], 
                        [ "name"=>"Mfg Date", "rowspan"=>"2", "colspan"=>"1" ], 
                        [ "name"=>"Exp Date", "rowspan"=>"2", "colspan"=>"1" ], 
                        [ "name"=>"Location", "rowspan"=>"1", "colspan"=>"3" ],
                        [ "name"=>"Book", "rowspan"=>"1", "colspan"=>"1" ],
                        [ "name"=>"Actual", "rowspan"=>"1", "colspan"=>"1" ],
                        [ "name"=>"Unit", "rowspan"=>"2", "colspan"=>"1" ]
                    ]);

                    $headTwo = collect([                    
                        [ "name"=>"Site" ], 
                        [ "name"=>"Area" ], 
                        [ "name"=>"Location" ],
                        [ "name"=>"Qty" ], 
                        [ "name"=>"Qty" ], 
                    ]);

                    $bodyOne = collect([
                        [ "name"=>"SKU No.", "field_name"=>"product_code", "class"=>"left", "colspan"=>"1" ],
                        [ "name"=>"SKU Name", "field_name"=>"product_name", "class"=>"left", "colspan"=>"1" ],
                        [ "name"=>"Batch", "field_name"=>"lot_no", "class"=>"left", "colspan"=>"1" ], 
                        [ "name"=>"Mfg", "field_name"=>"mfg_date", "class"=>"center", "colspan"=>"1" ], 
                        [ "name"=>"Expired", "field_name"=>"exp_date", "class"=>"center", "colspan"=>"1" ],
                        [ "name"=>"Site", "field_name"=>"site_name", "class"=>"left", "colspan"=>"1" ], 
                        [ "name"=>"Area", "field_name"=>"area_name", "class"=>"left", "colspan"=>"1" ], 
                        [ "name"=>"Location", "field_name"=>"location_code", "class"=>"center", "colspan"=>"1" ],
                        [ "name"=>"1st", "field_name"=>"pqty", "class"=>"right", "colspan"=>"1" ], 
                        [ "name"=>"1st", "field_name"=>"actual_pqty", "class"=>"right", "colspan"=>"1" ], 
                        [ "name"=>"1st", "field_name"=>"puom", "class"=>"center", "colspan"=>"1" ], 
                    ]);

                    $columnCount = 12;
                }
            
                $listData = [];
                foreach ($stockList as $value) {
                    $listData[] = [
                        "product_code"=>$value->product_code,
                        "product_name"=>$value->product_name,
                        "lot_no"=>$value->lot_no,
                        "site_name"=>$value->site_name,
                        "area_name"=>$value->area_name,
                        "location_code"=>$value->location_code,
                        "mfg_date"=> isset($value->mfg_date) ? \Carbon\Carbon::parse($value->mfg_date)->format("d-m-Y") : "",
                        "exp_date"=> isset($value->exp_date) ? \Carbon\Carbon::parse($value->exp_date)->format("d-m-Y") : "",
                        "actual_mfg_date"=> isset($value->mfg_date) ? \Carbon\Carbon::parse($value->actual_mfg_date)->format("d-m-Y") : "",
                        "actual_exp_date"=> isset($value->exp_date) ? \Carbon\Carbon::parse($value->actual_exp_date)->format("d-m-Y") : "",
                        "pqty"=>number_format($value->pqty, 0, ",", "."),
                        "mqty"=>number_format($value->mqty, 0, ",", "."),
                        "bqty"=>number_format($value->bqty, 0, ",", "."),
                        "actual_pqty"=>number_format($value->actual_pqty, 0, ",", "."),
                        "actual_mqty"=>number_format($value->actual_mqty, 0, ",", "."),
                        "actual_bqty"=>number_format($value->actual_bqty, 0, ",", "."),
                        "actual_lot_no"=>$value->actual_lot_no,
                        "puom"=>$value->puom,
                        "muom"=>$value->muom,
                        "buom"=>$value->buom
                    ];
                }

                $data = [
                    "title"=>"Stock Opname - Release Report",
                    "css"=>"landscape",
                    "headerOne"=>$headerOne,
                    "headOne"=>$headOne->toArray(),
                    "headTwo"=>$headTwo->toArray(),
                    "bodyOne"=>$bodyOne->toArray(),
                    "listData"=>$listData,
                    "columnCount"=>$columnCount
                ];

                return view("report", $data);
                break;
            case "invest":
                $stockList = DB::table("iv_cyclecount_detail as a")
                                ->select(
                                    "a.product_code", 
                                    "b.product_name", 
                                    "a.lot_no", 
                                    "a.mfg_date", 
                                    "a.exp_date", 
                                    "c.site_name", 
                                    "d.area_name", 
                                    "a.location_code", 
                                    "b.uppp",
                                    "b.muppp",
                                    "a.pqty",
                                    "a.mqty",
                                    "a.bqty",
                                    "a.qty",
                                    "a.actual_pqty",
                                    "a.actual_mqty",
                                    "a.actual_bqty",
                                    "a.actual_qty",
                                    "b.puom",
                                    "b.muom",
                                    "b.buom",                                    
                                    "a.actual_lot_no", 
                                    "a.actual_mfg_date", 
                                    "a.actual_exp_date", 
                                    )
                                ->join("iv_product as b", "a.product_id", "b.id")
                                ->leftjoin("iv_site as c", "a.site_id", "c.id")
                                ->leftjoin("iv_site_area as d", "a.area_id", "d.id")
                                ->where("a.CycleCount_id", "=", $id)
                                ->where("a.confirmed_flag", "=", "Yes")
                                ->where(DB::raw("CASE WHEN a.pqty <> a.actual_pqty OR a.mqty <> a.actual_mqty OR a.bqty <> a.actual_bqty OR a.lot_no <> a.actual_lot_no OR a.mfg_date <> a.actual_mfg_date OR a.exp_date <> a.actual_exp_date THEN 1 ELSE 0 END"), "=", 1)
                                ->get();
                
                $job = DB::table("iv_cyclecount_job as a")
                            ->select("a.*", "b.principal_name", "b.multi_level")
                            ->join("iv_principal as b", "a.principal_id", "b.id")
                            ->where("a.id", "=", $id)
                            ->first();
                
                $headerOne = "<tr><td>Principal Name</td><td>:</td><td>$job->principal_name</td></tr>";
                $headerOne .= "<tr><td>Job Number</td><td>:</td><td>$job->cyclecount_no</td></tr>";
                $headerOne .= "<tr><td>Job Date</td><td>:</td><td>".date("d-m-Y", strtotime($job->cyclecount_date))."</td></tr>";
                
                if ($job->multi_level == "Yes") {
                    $headOne = collect([
                        [ "name"=>"SKU No.", "rowspan"=>"2", "colspan"=>"1" ], 
                        [ "name"=>"SKU Name", "rowspan"=>"2", "colspan"=>"1" ], 
                        [ "name"=>"Batch", "rowspan"=>"2", "colspan"=>"1" ], 
                        [ "name"=>"Mfg Date", "rowspan"=>"2", "colspan"=>"1" ], 
                        [ "name"=>"Exp Date", "rowspan"=>"2", "colspan"=>"1" ], 
                        [ "name"=>"Location", "rowspan"=>"1", "colspan"=>"3" ],
                        [ "name"=>"Book Quantity", "rowspan"=>"1", "colspan"=>"3" ],
                        [ "name"=>"Actual Quantity", "rowspan"=>"1", "colspan"=>"3" ],
                        [ "name"=>"Unit", "rowspan"=>"1", "colspan"=>"3" ]
                    ]);

                    $headTwo = collect([                    
                        [ "name"=>"Site" ], 
                        [ "name"=>"Area" ], 
                        [ "name"=>"Location" ],
                        [ "name"=>"1st" ], 
                        [ "name"=>"2nd" ], 
                        [ "name"=>"3rd" ],
                        [ "name"=>"1st" ], 
                        [ "name"=>"2nd" ], 
                        [ "name"=>"3rd" ],
                        [ "name"=>"1st" ], 
                        [ "name"=>"2nd" ], 
                        [ "name"=>"3rd" ],
                    ]);

                    $bodyOne = collect([
                        [ "name"=>"SKU No.", "field_name"=>"product_code", "class"=>"left", "colspan"=>"1" ],
                        [ "name"=>"SKU Name", "field_name"=>"product_name", "class"=>"left", "colspan"=>"1" ],
                        [ "name"=>"Batch", "field_name"=>"lot_no", "class"=>"left", "colspan"=>"1" ], 
                        [ "name"=>"Mfg", "field_name"=>"mfg_date", "class"=>"center", "colspan"=>"1" ], 
                        [ "name"=>"Expired", "field_name"=>"exp_date", "class"=>"center", "colspan"=>"1" ],
                        [ "name"=>"Site", "field_name"=>"site_name", "class"=>"left", "colspan"=>"1" ], 
                        [ "name"=>"Area", "field_name"=>"area_name", "class"=>"left", "colspan"=>"1" ], 
                        [ "name"=>"Location", "field_name"=>"location_code", "class"=>"center", "colspan"=>"1" ],
                        [ "name"=>"1st", "field_name"=>"pqty", "class"=>"right", "colspan"=>"1" ], 
                        [ "name"=>"2nd", "field_name"=>"mqty", "class"=>"right", "colspan"=>"1" ],
                        [ "name"=>"3rd", "field_name"=>"bqty", "class"=>"right", "colspan"=>"1" ], 
                        [ "name"=>"1st", "field_name"=>"actual_pqty", "class"=>"right", "colspan"=>"1" ], 
                        [ "name"=>"2nd", "field_name"=>"actual_mqty", "class"=>"right", "colspan"=>"1" ],
                        [ "name"=>"3rd", "field_name"=>"actual_bqty", "class"=>"right", "colspan"=>"1" ], 
                        [ "name"=>"1st", "field_name"=>"puom", "class"=>"center", "colspan"=>"1" ], 
                        [ "name"=>"2nd", "field_name"=>"muom", "class"=>"center", "colspan"=>"1" ], 
                        [ "name"=>"3rd", "field_name"=>"buom", "class"=>"center", "colspan"=>"1" ],
                    ]);

                    $columnCount = 17;
                } else {
                    $headOne = collect([
                        [ "name"=>"SKU No.", "rowspan"=>"2", "colspan"=>"1" ], 
                        [ "name"=>"SKU Name", "rowspan"=>"2", "colspan"=>"1" ], 
                        [ "name"=>"Batch", "rowspan"=>"2", "colspan"=>"1" ], 
                        [ "name"=>"Mfg Date", "rowspan"=>"2", "colspan"=>"1" ], 
                        [ "name"=>"Exp Date", "rowspan"=>"2", "colspan"=>"1" ], 
                        [ "name"=>"Location", "rowspan"=>"1", "colspan"=>"3" ],
                        [ "name"=>"Book", "rowspan"=>"1", "colspan"=>"1" ],
                        [ "name"=>"Actual", "rowspan"=>"1", "colspan"=>"1" ],
                        [ "name"=>"Unit", "rowspan"=>"2", "colspan"=>"1" ]
                    ]);

                    $headTwo = collect([                    
                        [ "name"=>"Site" ], 
                        [ "name"=>"Area" ], 
                        [ "name"=>"Location" ],
                        [ "name"=>"Qty" ], 
                        [ "name"=>"Qty" ], 
                    ]);

                    $bodyOne = collect([
                        [ "name"=>"SKU No.", "field_name"=>"product_code", "class"=>"left", "colspan"=>"1" ],
                        [ "name"=>"SKU Name", "field_name"=>"product_name", "class"=>"left", "colspan"=>"1" ],
                        [ "name"=>"Batch", "field_name"=>"lot_no", "class"=>"left", "colspan"=>"1" ], 
                        [ "name"=>"Mfg", "field_name"=>"mfg_date", "class"=>"center", "colspan"=>"1" ], 
                        [ "name"=>"Expired", "field_name"=>"exp_date", "class"=>"center", "colspan"=>"1" ],
                        [ "name"=>"Site", "field_name"=>"site_name", "class"=>"left", "colspan"=>"1" ], 
                        [ "name"=>"Area", "field_name"=>"area_name", "class"=>"left", "colspan"=>"1" ], 
                        [ "name"=>"Location", "field_name"=>"location_code", "class"=>"center", "colspan"=>"1" ],
                        [ "name"=>"1st", "field_name"=>"pqty", "class"=>"right", "colspan"=>"1" ], 
                        [ "name"=>"1st", "field_name"=>"actual_pqty", "class"=>"right", "colspan"=>"1" ], 
                        [ "name"=>"1st", "field_name"=>"puom", "class"=>"center", "colspan"=>"1" ], 
                    ]);

                    $columnCount = 12;
                }
                
                $listData = [];
                foreach ($stockList as $value) {
                    $listData[] = [
                        "product_code"=>$value->product_code,
                        "product_name"=>$value->product_name,
                        "lot_no"=>$value->lot_no,
                        "site_name"=>$value->site_name,
                        "area_name"=>$value->area_name,
                        "location_code"=>$value->location_code,
                        "mfg_date"=> isset($value->mfg_date) ? \Carbon\Carbon::parse($value->mfg_date)->format("d-m-Y") : "",
                        "exp_date"=> isset($value->exp_date) ? \Carbon\Carbon::parse($value->exp_date)->format("d-m-Y") : "",
                        "actual_mfg_date"=> isset($value->mfg_date) ? \Carbon\Carbon::parse($value->actual_mfg_date)->format("d-m-Y") : "",
                        "actual_exp_date"=> isset($value->exp_date) ? \Carbon\Carbon::parse($value->actual_exp_date)->format("d-m-Y") : "",
                        "pqty"=>number_format($value->pqty, 0, ",", "."),
                        "mqty"=>number_format($value->mqty, 0, ",", "."),
                        "bqty"=>number_format($value->bqty, 0, ",", "."),
                        "actual_pqty"=>number_format($value->actual_pqty, 0, ",", "."),
                        "actual_mqty"=>number_format($value->actual_mqty, 0, ",", "."),
                        "actual_bqty"=>number_format($value->actual_bqty, 0, ",", "."),
                        "actual_lot_no"=>$value->actual_lot_no,
                        "puom"=>$value->puom,
                        "muom"=>$value->muom,
                        "buom"=>$value->buom
                    ];
                }

                $data = [
                    "title"=>"Stock Opname - Adjustment Report",
                    "css"=>"landscape",
                    "headerOne"=>$headerOne,
                    "headOne"=>$headOne->toArray(),
                    "headTwo"=>$headTwo->toArray(),
                    "bodyOne"=>$bodyOne->toArray(),
                    "listData"=>$listData,
                    "columnCount"=>$columnCount
                ];

                return view("report", $data);
                break;
            default:
            
                break;
        }
    }
}