<?php

namespace App\Http\Controllers\Transaction\Adjustment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index($type, $id) {
        switch ($type) {
            case 'entry':
                $stockList = DB::table('iv_adjustment_detail as a')
                                ->select(
                                    'a.status_flag', 
                                    'a.adjust_type', 
                                    'a.product_code', 
                                    'b.product_name', 
                                    'a.lot_no', 
                                    'a.document_ref', 
                                    'a.mfg_date', 
                                    'a.exp_date', 
                                    'c.site_name', 
                                    'd.area_name', 
                                    'a.location_code', 
                                    'b.uppp',
                                    'b.muppp',
                                    'a.pqty',
                                    'a.mqty',
                                    'a.bqty',
                                    'a.qty',
                                    'a.actual_pqty',
                                    'a.actual_mqty',
                                    'a.actual_bqty',
                                    'a.actual_qty',
                                    'b.puom',
                                    'b.muom',
                                    'b.buom'
                                    )
                                ->join('iv_product as b', 'a.product_id', 'b.id')
                                ->join('iv_site as c', 'a.site_id', 'c.id')
                                ->join('iv_site_area as d', 'a.area_id', 'd.id')
                                ->where('a.adjust_id', '=', $id)
                                ->get();
                
                $job = DB::table('iv_adjustment_job as a')
                            ->select('a.*', 'b.type_name')
                            ->join('iv_adjustment_type as b', 'a.type_id', 'b.id')
                            ->where('a.id', '=', $id)
                            ->first();
                
                $headerOne = "<tr><td>Adjustment Number</td><td>:</td><td>$job->adjust_no</td></tr>";
                $headerOne .= "<tr><td>Adjustment Date</td><td>:</td><td>".date("d-m-Y", strtotime($job->adjust_date))."</td></tr>";
                $headerOne .= "<tr><td>Reason Name</td><td>:</td><td>$job->type_name</td></tr>";
                
                $headOne = collect([
                    [ "name"=>"SKU No.", "rowspan"=>"2", "colspan"=>"1" ], 
                    [ "name"=>"SKU Name", "rowspan"=>"2", "colspan"=>"1" ], 
                    [ "name"=>"Batch", "rowspan"=>"2", "colspan"=>"1" ], 
                    [ "name"=>"Mfg Date", "rowspan"=>"2", "colspan"=>"1" ], 
                    [ "name"=>"Exp Date", "rowspan"=>"2", "colspan"=>"1" ], 
                    [ "name"=>"Stock Status", "rowspan"=>"2", "colspan"=>"1" ], 
                    [ "name"=>"Adjust Type", "rowspan"=>"2", "colspan"=>"1" ],
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
                    [ 'name'=>'Stock Status', 'field_name'=>'status_flag', 'class'=>'center', 'colspan'=>"1" ], 
                    [ 'name'=>'Location', 'field_name'=>'adjust_type', 'class'=>'center', 'colspan'=>"1" ],
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
            
                $listData = [];
                foreach ($stockList as $value) {
                    $listData[] = [
                        'product_code'=>$value->product_code,
                        'product_name'=>$value->product_name,
                        'lot_no'=>$value->lot_no,
                        'site_name'=>$value->site_name,
                        'area_name'=>$value->area_name,
                        'location_code'=>$value->location_code,
                        "mfg_date"=> isset($value->mfg_date) ? \Carbon\Carbon::parse($value->mfg_date)->format("d-m-Y") : "",
                        "exp_date"=> isset($value->exp_date) ? \Carbon\Carbon::parse($value->exp_date)->format("d-m-Y") : "",
                        'pqty'=>number_format($value->pqty, 0, ',', '.'),
                        'mqty'=>number_format($value->mqty, 0, ',', '.'),
                        'bqty'=>number_format($value->bqty, 0, ',', '.'),
                        'actual_pqty'=>number_format($value->actual_pqty, 0, ',', '.'),
                        'actual_mqty'=>number_format($value->actual_mqty, 0, ',', '.'),
                        'actual_bqty'=>number_format($value->actual_bqty, 0, ',', '.'),
                        'puom'=>$value->puom,
                        'muom'=>$value->muom,
                        'buom'=>$value->buom,
                        'status_flag'=>$value->status_flag,
                        'adjust_type'=>$value->adjust_type
                    ];
                }

                $data = [
                    "title"=>"Adjustment Check List Report ( Entry List )",
                    "css"=>"landscape",
                    "headerOne"=>$headerOne,
                    "headOne"=>$headOne->toArray(),
                    "headTwo"=>$headTwo->toArray(),
                    "bodyOne"=>$bodyOne->toArray(),
                    "listData"=>$listData,
                    "columnCount"=>19
                ];

                return view('report', $data);
                break;
            case 'process':
                $stockList = DB::table('iv_adjustment_batch as a')
                                ->select(
                                    'a.job_type', 
                                    'a.product_code', 
                                    'b.product_name', 
                                    'a.lot_no', 
                                    'a.document_ref', 
                                    'a.mfg_date', 
                                    'a.exp_date', 
                                    'c.site_name', 
                                    'd.area_name', 
                                    'a.location_code', 
                                    'b.uppp',
                                    'b.muppp',
                                    'a.pqty',
                                    'a.mqty',
                                    'a.bqty',
                                    'a.qty',
                                    'b.puom',
                                    'b.muom',
                                    'b.buom'
                                    )
                                ->join('iv_product as b', 'a.product_id', 'b.id')
                                ->join('iv_site as c', 'a.site_id', 'c.id')
                                ->join('iv_site_area as d', 'a.area_id', 'd.id')
                                ->where('a.adjust_id', '=', $id)
                                ->get();
                
                $job = DB::table('iv_adjustment_job as a')
                            ->select('a.*', 'b.type_name')
                            ->join('iv_adjustment_type as b', 'a.type_id', 'b.id')
                            ->where('a.id', '=', $id)
                            ->first();
                
                $headerOne = "<tr><td>Adjustment Number</td><td>:</td><td>$job->adjust_no</td></tr>";
                $headerOne .= "<tr><td>Adjustment Date</td><td>:</td><td>".date("d-m-Y", strtotime($job->adjust_date))."</td></tr>";
                $headerOne .= "<tr><td>Reason Name</td><td>:</td><td>$job->type_name</td></tr>";
                
                $headOne = collect([
                    [ "name"=>"SKU No.", "rowspan"=>"2", "colspan"=>"1" ], 
                    [ "name"=>"SKU Name", "rowspan"=>"2", "colspan"=>"1" ], 
                    [ "name"=>"Batch", "rowspan"=>"2", "colspan"=>"1" ], 
                    [ "name"=>"Mfg Date", "rowspan"=>"2", "colspan"=>"1" ], 
                    [ "name"=>"Exp Date", "rowspan"=>"2", "colspan"=>"1" ], 
                    [ "name"=>"Adjust Type", "rowspan"=>"2", "colspan"=>"1" ],
                    [ "name"=>"Location", "rowspan"=>"1", "colspan"=>"3" ],
                    [ "name"=>"Quantity", "rowspan"=>"1", "colspan"=>"3" ],
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
                ]);

                $bodyOne = collect([
                    [ "name"=>"SKU No.", "field_name"=>"product_code", "class"=>"left", "colspan"=>"1" ],
                    [ "name"=>"SKU Name", "field_name"=>"product_name", "class"=>"left", "colspan"=>"1" ],
                    [ "name"=>"Batch", "field_name"=>"lot_no", "class"=>"left", "colspan"=>"1" ], 
                    [ "name"=>"Mfg", "field_name"=>"mfg_date", "class"=>"center", "colspan"=>"1" ], 
                    [ "name"=>"Expired", "field_name"=>"exp_date", "class"=>"center", "colspan"=>"1" ],
                    [ "name"=>"Site", "field_name"=>"site_name", "class"=>"left", "colspan"=>"1" ], 
                    [ "name"=>"Area", "field_name"=>"area_name", "class"=>"left", "colspan"=>"1" ], 
                    [ 'name'=>'Location', 'field_name'=>'adjust_type', 'class'=>'center', 'colspan'=>"1" ],
                    [ "name"=>"Location", "field_name"=>"location_code", "class"=>"center", "colspan"=>"1" ],
                    [ "name"=>"1st", "field_name"=>"pqty", "class"=>"right", "colspan"=>"1" ], 
                    [ "name"=>"2nd", "field_name"=>"mqty", "class"=>"right", "colspan"=>"1" ],
                    [ "name"=>"3rd", "field_name"=>"bqty", "class"=>"right", "colspan"=>"1" ], 
                    [ "name"=>"1st", "field_name"=>"puom", "class"=>"center", "colspan"=>"1" ], 
                    [ "name"=>"2nd", "field_name"=>"muom", "class"=>"center", "colspan"=>"1" ], 
                    [ "name"=>"3rd", "field_name"=>"buom", "class"=>"center", "colspan"=>"1" ],                    
                ]);
            
                $listData = [];
                foreach ($stockList as $value) {
                    $listData[] = [
                        'product_code'=>$value->product_code,
                        'product_name'=>$value->product_name,
                        'lot_no'=>$value->lot_no,
                        'site_name'=>$value->site_name,
                        'area_name'=>$value->area_name,
                        'location_code'=>$value->location_code,
                        "mfg_date"=> isset($value->mfg_date) ? \Carbon\Carbon::parse($value->mfg_date)->format("d-m-Y") : "",
                        "exp_date"=> isset($value->exp_date) ? \Carbon\Carbon::parse($value->exp_date)->format("d-m-Y") : "",
                        'pqty'=>number_format($value->pqty, 0, ',', '.'),
                        'mqty'=>number_format($value->mqty, 0, ',', '.'),
                        'bqty'=>number_format($value->bqty, 0, ',', '.'),
                        'puom'=>$value->puom,
                        'muom'=>$value->muom,
                        'buom'=>$value->buom,
                        'adjust_type'=>$value->job_type == 'ADJ-' ? 'Minus' : 'Plus'
                    ];
                }

                $data = [
                    "title"=>"Adjustment Check List Report ( Process List )",
                    "css"=>"landscape",
                    "headerOne"=>$headerOne,
                    "headOne"=>$headOne->toArray(),
                    "headTwo"=>$headTwo->toArray(),
                    "bodyOne"=>$bodyOne->toArray(),
                    "listData"=>$listData,
                    "columnCount"=>15
                ];

                return view('report', $data);
                break;
            default:
            
                break;
        }
    }
}