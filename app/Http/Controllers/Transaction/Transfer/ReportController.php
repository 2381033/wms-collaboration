<?php

namespace App\Http\Controllers\Transaction\Transfer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index($type, $id)
    {
        switch ($type) {
            case 'entry':
                $stockList = DB::table('iv_transfer_detail as a')
                    ->select(
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
                    ->where('a.transfer_id', '=', $id)
                    ->get();

                $job = DB::table('iv_transfer_job as a')
                    ->select('a.*', 'b.principal_name', 'b.multi_level')
                    ->join('iv_principal as b', 'a.principal_id', 'b.id')
                    ->where('a.id', '=', $id)
                    ->first();

                $headerOne = "<tr><td>Principal Name</td><td>:</td><td>$job->principal_name</td></tr>";
                $headerOne .= "<tr><td>Job Number</td><td>:</td><td>$job->job_no</td></tr>";
                $headerOne .= "<tr><td>Job Date</td><td>:</td><td>" . date("d-m-Y", strtotime($job->job_date)) . "</td></tr>";

                if ($job->multi_level == "Yes") {
                    $headOne = collect([
                        ["name" => "SKU No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "SKU Name", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Batch No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Mfg Date", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Exp Date", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Site", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Area", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Location", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Quantity", "rowspan" => "1", "colspan" => "6"],
                    ]);

                    $headTwo = collect([
                        ['name' => '1st Qty'],
                        ['name' => '1st Unit'],
                        ['name' => '2nd Qty'],
                        ['name' => '2nd Unit'],
                        ['name' => '3rd Qty'],
                        ['name' => '3rd Unit'],
                    ]);

                    $bodyOne = collect([
                        ["name" => "Product Name", "field_name" => "product_code", "class" => "left", "colspan" => "1"],
                        ["name" => "Product Name", "field_name" => "product_name", "class" => "left", "colspan" => "1"],
                        ["name" => "Batch No.", "field_name" => "lot_no", "class" => "left", "colspan" => "1"],
                        ["name" => "Mfg Date", "field_name" => "mfg_date", "class" => "center", "colspan" => "1"],
                        ["name" => "Exp Date", "field_name" => "exp_date", "class" => "center", "colspan" => "1"],
                        ["name" => "Site", "field_name" => "site_name", "class" => "left", "colspan" => "1"],
                        ["name" => "Area", "field_name" => "area_name", "class" => "left", "colspan" => "1"],
                        ["name" => "Location", "field_name" => "location_code", "class" => "left", "colspan" => "1"],
                        ["name" => "1st", "field_name" => "pqty", "class" => "right", "colspan" => "1"],
                        ["name" => "1st", "field_name" => "puom", "class" => "center", "colspan" => "1"],
                        ['name' => '2nd', 'field_name' => 'mqty', 'class' => 'right', 'colspan' => "1"],
                        ['name' => '2nd', 'field_name' => 'muom', 'class' => 'center', 'colspan' => "1"],
                        ['name' => '3rd', 'field_name' => 'bqty', 'class' => 'right', 'colspan' => "1"],
                        ['name' => '3rd', 'field_name' => 'buom', 'class' => 'center', 'colspan' => "1"],
                    ]);

                    $columnCount = 14;
                } else {
                    $headOne = collect([
                        ["name" => "SKU No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "SKU Name", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Batch No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Mfg Date", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Exp Date", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Site", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Area", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Location", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Quantity", "rowspan" => "1", "colspan" => "2"],
                    ]);

                    $headTwo = collect([
                        ['name' => '1st Qty'],
                        ['name' => '1st Unit'],
                    ]);

                    $bodyOne = collect([
                        ["name" => "Product Name", "field_name" => "product_code", "class" => "left", "colspan" => "1"],
                        ["name" => "Product Name", "field_name" => "product_name", "class" => "left", "colspan" => "1"],
                        ["name" => "Batch No.", "field_name" => "lot_no", "class" => "left", "colspan" => "1"],
                        ["name" => "Mfg Date", "field_name" => "mfg_date", "class" => "left", "colspan" => "1"],
                        ["name" => "Exp Date", "field_name" => "exp_date", "class" => "left", "colspan" => "1"],
                        ["name" => "Site", "field_name" => "site_name", "class" => "left", "colspan" => "1"],
                        ["name" => "Area", "field_name" => "area_name", "class" => "left", "colspan" => "1"],
                        ["name" => "Location", "field_name" => "location_code", "class" => "left", "colspan" => "1"],
                        ["name" => "1st", "field_name" => "pqty", "class" => "right", "colspan" => "1"],
                        ["name" => "1st", "field_name" => "puom", "class" => "center", "colspan" => "1"],
                    ]);

                    $columnCount = 10;
                }

                $listData = [];
                foreach ($stockList as $value) {
                    $listData[] = [
                        'product_code' => $value->product_code,
                        'product_name' => $value->product_name,
                        'lot_no' => $value->lot_no,
                        'site_name' => $value->site_name,
                        'area_name' => $value->area_name,
                        'location_code' => $value->location_code,
                        "mfg_date" => isset($value->mfg_date) ? \Carbon\Carbon::parse($value->mfg_date)->format("d-m-Y") : "",
                        "exp_date" => isset($value->exp_date) ? \Carbon\Carbon::parse($value->exp_date)->format("d-m-Y") : "",
                        'pqty' => number_format($value->pqty, 0, ',', '.'),
                        'mqty' => number_format($value->mqty, 0, ',', '.'),
                        'bqty' => number_format($value->bqty, 0, ',', '.'),
                        'actual_pqty' => number_format($value->actual_pqty, 0, ',', '.'),
                        'actual_mqty' => number_format($value->actual_mqty, 0, ',', '.'),
                        'actual_bqty' => number_format($value->actual_bqty, 0, ',', '.'),
                        'puom' => $value->puom,
                        'muom' => $value->muom,
                        'buom' => $value->buom
                    ];
                }

                $signature = "<tr><td>Prepare By:</td><td>Checked By:</td><td>Supervised By:</td></tr>";
                $signature .= "<tr><td>Date:</td><td>Date:</td><td>Date:</td></tr>";
                $signature .= "<tr><td>Signature:</td><td>Signature:</td><td>Signature:</td></tr>";

                $data = [
                    "title" => "Stock Transfer Report",
                    "css" => "landscape",
                    "headerOne" => $headerOne,
                    "headOne" => $headOne->toArray(),
                    "headTwo" => $headTwo->toArray(),
                    "bodyOne" => $bodyOne->toArray(),
                    "signature" => $signature,
                    "listData" => $listData,
                    "columnCount" => $columnCount
                ];

                return view('report', $data);
                break;
            case 'pick':
                $dataList = DB::table('iv_transfer_batch as a')
                    ->select(
                        'e.principal_name',
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
                    ->join('iv_principal as e', 'a.principal_id', 'e.id')
                    ->where('a.transfer_id', '=', $id)
                    ->where('a.job_type', 'TFRO')
                    ->orderby('b.product_name', 'ASC')
                    ->orderby('a.lot_no', 'ASC')
                    ->orderby('a.job_type', 'ASC')
                    ->get();

                $job = DB::table('iv_transfer_job as a')
                    ->select('a.*', 'b.principal_name', 'b.multi_level')
                    ->join('iv_principal as b', 'a.principal_id', 'b.id')
                    ->where('a.id', '=', $id)
                    ->first();

                $headerOne = "<tr><td>Principal Name</td><td>:</td><td>$job->principal_name</td></tr>";
                $headerOne .= "<tr><td>Job Number</td><td>:</td><td>$job->job_no</td></tr>";
                $headerOne .= "<tr><td>Job Date</td><td>:</td><td>" . date("d-m-Y", strtotime($job->job_date)) . "</td></tr>";

                if ($job->multi_level == "Yes") {
                    $headOne = collect([
                        ["name" => "SKU No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "SKU Name", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Batch No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Mfg Date", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Exp Date", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Status", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Site", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Area", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Location", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Quantity", "rowspan" => "1", "colspan" => "6"],
                    ]);

                    $headTwo = collect([
                        ['name' => '1st Qty'],
                        ['name' => '1st Unit'],
                        ['name' => '2nd Qty'],
                        ['name' => '2nd Unit'],
                        ['name' => '3rd Qty'],
                        ['name' => '3rd Unit'],
                    ]);

                    $bodyOne = collect([
                        ["name" => "Product Name", "field_name" => "product_code", "class" => "left", "colspan" => "1"],
                        ["name" => "Product Name", "field_name" => "product_name", "class" => "left", "colspan" => "1"],
                        ["name" => "Batch No.", "field_name" => "lot_no", "class" => "left", "colspan" => "1"],
                        ["name" => "Mfg Date", "field_name" => "mfg_date", "class" => "center", "colspan" => "1"],
                        ["name" => "Exp Date", "field_name" => "exp_date", "class" => "center", "colspan" => "1"],
                        ["name" => "Status", "field_name" => "job_type", "class" => "center", "colspan" => "1"],
                        ["name" => "Site", "field_name" => "site_name", "class" => "left", "colspan" => "1"],
                        ["name" => "Area", "field_name" => "area_name", "class" => "left", "colspan" => "1"],
                        ["name" => "Location", "field_name" => "location_code", "class" => "left", "colspan" => "1"],
                        ["name" => "1st", "field_name" => "pqty", "class" => "right", "colspan" => "1"],
                        ["name" => "1st", "field_name" => "puom", "class" => "center", "colspan" => "1"],
                        ['name' => '2nd', 'field_name' => 'mqty', 'class' => 'right', 'colspan' => "1"],
                        ['name' => '2nd', 'field_name' => 'muom', 'class' => 'center', 'colspan' => "1"],
                        ['name' => '3rd', 'field_name' => 'bqty', 'class' => 'right', 'colspan' => "1"],
                        ['name' => '3rd', 'field_name' => 'buom', 'class' => 'center', 'colspan' => "1"],
                    ]);

                    $columnCount = 15;
                } else {
                    $headOne = collect([
                        ["name" => "SKU No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "SKU Name", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Batch No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Mfg Date", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Exp Date", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Status", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Site", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Area", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Location", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Quantity", "rowspan" => "1", "colspan" => "2"],
                    ]);

                    $headTwo = collect([
                        ['name' => '1st Qty'],
                        ['name' => '1st Unit'],
                    ]);

                    $bodyOne = collect([
                        ["name" => "Product Name", "field_name" => "product_code", "class" => "left", "colspan" => "1"],
                        ["name" => "Product Name", "field_name" => "product_name", "class" => "left", "colspan" => "1"],
                        ["name" => "Batch No.", "field_name" => "lot_no", "class" => "left", "colspan" => "1"],
                        ["name" => "Mfg Date", "field_name" => "mfg_date", "class" => "left", "colspan" => "1"],
                        ["name" => "Exp Date", "field_name" => "exp_date", "class" => "left", "colspan" => "1"],
                        ["name" => "Status", "field_name" => "job_type", "class" => "center", "colspan" => "1"],
                        ["name" => "Site", "field_name" => "site_name", "class" => "left", "colspan" => "1"],
                        ["name" => "Area", "field_name" => "area_name", "class" => "left", "colspan" => "1"],
                        ["name" => "Location", "field_name" => "location_code", "class" => "left", "colspan" => "1"],
                        ["name" => "1st", "field_name" => "pqty", "class" => "right", "colspan" => "1"],
                        ["name" => "1st", "field_name" => "puom", "class" => "center", "colspan" => "1"],
                    ]);

                    $columnCount = 11;
                }

                $listData = [];
                foreach ($dataList as $value) {
                    $listData[] = [
                        'product_code' => $value->product_code,
                        'product_name' => $value->product_name,
                        'lot_no' => $value->lot_no,
                        'site_name' => $value->site_name,
                        'area_name' => $value->area_name,
                        'location_code' => $value->location_code,
                        "mfg_date" => isset($value->mfg_date) ? \Carbon\Carbon::parse($value->mfg_date)->format("d-m-Y") : "",
                        "exp_date" => isset($value->exp_date) ? \Carbon\Carbon::parse($value->exp_date)->format("d-m-Y") : "",
                        'pqty' => number_format($value->pqty, 0, ',', '.'),
                        'mqty' => number_format($value->mqty, 0, ',', '.'),
                        'bqty' => number_format($value->bqty, 0, ',', '.'),
                        'puom' => $value->puom,
                        'muom' => $value->muom,
                        'buom' => $value->buom,
                        'job_type' => $value->job_type == 'TFRO' ? 'Stock Move From' : 'Stock Move To'
                    ];
                }

                $signature = "<tr><td>Prepare By:</td><td>Checked By:</td><td>Supervised By:</td></tr>";
                $signature .= "<tr><td>Date:</td><td>Date:</td><td>Date:</td></tr>";
                $signature .= "<tr><td>Signature:</td><td>Signature:</td><td>Signature:</td></tr>";

                $data = [
                    "title" => "Stock Transfer Report",
                    "css" => "landscape",
                    "headerOne" => $headerOne,
                    "headOne" => $headOne->toArray(),
                    "headTwo" => $headTwo->toArray(),
                    "bodyOne" => $bodyOne->toArray(),
                    "signature" => $signature,
                    "listData" => $listData,
                    "columnCount" => $columnCount
                ];

                return view('report', $data);
                break;
            case 'put':
                $dataList = DB::table('iv_transfer_batch as a')
                    ->select(
                        'e.principal_name',
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
                    ->join('iv_principal as e', 'a.principal_id', 'e.id')
                    ->where('a.transfer_id', '=', $id)
                    ->where('a.job_type', 'TFRI')
                    ->orderby('b.product_name', 'ASC')
                    ->orderby('a.lot_no', 'ASC')
                    ->orderby('a.job_type', 'ASC')
                    ->get();

                $job = DB::table('iv_transfer_job as a')
                    ->select('a.*', 'b.principal_name', 'b.multi_level')
                    ->join('iv_principal as b', 'a.principal_id', 'b.id')
                    ->where('a.id', '=', $id)
                    ->first();

                $headerOne = "<tr><td>Principal Name</td><td>:</td><td>$job->principal_name</td></tr>";
                $headerOne .= "<tr><td>Job Number</td><td>:</td><td>$job->job_no</td></tr>";
                $headerOne .= "<tr><td>Job Date</td><td>:</td><td>" . date("d-m-Y", strtotime($job->job_date)) . "</td></tr>";

                if ($job->multi_level == "Yes") {
                    $headOne = collect([
                        ["name" => "SKU No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "SKU Name", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Batch No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Mfg Date", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Exp Date", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Status", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Site", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Area", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Location", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Quantity", "rowspan" => "1", "colspan" => "6"],
                    ]);

                    $headTwo = collect([
                        ['name' => '1st Qty'],
                        ['name' => '1st Unit'],
                        ['name' => '2nd Qty'],
                        ['name' => '2nd Unit'],
                        ['name' => '3rd Qty'],
                        ['name' => '3rd Unit'],
                    ]);

                    $bodyOne = collect([
                        ["name" => "Product Name", "field_name" => "product_code", "class" => "left", "colspan" => "1"],
                        ["name" => "Product Name", "field_name" => "product_name", "class" => "left", "colspan" => "1"],
                        ["name" => "Batch No.", "field_name" => "lot_no", "class" => "left", "colspan" => "1"],
                        ["name" => "Mfg Date", "field_name" => "mfg_date", "class" => "center", "colspan" => "1"],
                        ["name" => "Exp Date", "field_name" => "exp_date", "class" => "center", "colspan" => "1"],
                        ["name" => "Status", "field_name" => "job_type", "class" => "center", "colspan" => "1"],
                        ["name" => "Site", "field_name" => "site_name", "class" => "left", "colspan" => "1"],
                        ["name" => "Area", "field_name" => "area_name", "class" => "left", "colspan" => "1"],
                        ["name" => "Location", "field_name" => "location_code", "class" => "left", "colspan" => "1"],
                        ["name" => "1st", "field_name" => "pqty", "class" => "right", "colspan" => "1"],
                        ["name" => "1st", "field_name" => "puom", "class" => "center", "colspan" => "1"],
                        ['name' => '2nd', 'field_name' => 'mqty', 'class' => 'right', 'colspan' => "1"],
                        ['name' => '2nd', 'field_name' => 'muom', 'class' => 'center', 'colspan' => "1"],
                        ['name' => '3rd', 'field_name' => 'bqty', 'class' => 'right', 'colspan' => "1"],
                        ['name' => '3rd', 'field_name' => 'buom', 'class' => 'center', 'colspan' => "1"],
                    ]);

                    $columnCount = 15;
                } else {
                    $headOne = collect([
                        ["name" => "SKU No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "SKU Name", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Batch No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Mfg Date", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Exp Date", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Status", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Site", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Area", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Location", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Quantity", "rowspan" => "1", "colspan" => "2"],
                    ]);

                    $headTwo = collect([
                        ['name' => '1st Qty'],
                        ['name' => '1st Unit'],
                    ]);

                    $bodyOne = collect([
                        ["name" => "Product Name", "field_name" => "product_code", "class" => "left", "colspan" => "1"],
                        ["name" => "Product Name", "field_name" => "product_name", "class" => "left", "colspan" => "1"],
                        ["name" => "Batch No.", "field_name" => "lot_no", "class" => "left", "colspan" => "1"],
                        ["name" => "Mfg Date", "field_name" => "mfg_date", "class" => "left", "colspan" => "1"],
                        ["name" => "Exp Date", "field_name" => "exp_date", "class" => "left", "colspan" => "1"],
                        ["name" => "Status", "field_name" => "job_type", "class" => "center", "colspan" => "1"],
                        ["name" => "Site", "field_name" => "site_name", "class" => "left", "colspan" => "1"],
                        ["name" => "Area", "field_name" => "area_name", "class" => "left", "colspan" => "1"],
                        ["name" => "Location", "field_name" => "location_code", "class" => "left", "colspan" => "1"],
                        ["name" => "1st", "field_name" => "pqty", "class" => "right", "colspan" => "1"],
                        ["name" => "1st", "field_name" => "puom", "class" => "center", "colspan" => "1"],
                    ]);

                    $columnCount = 11;
                }

                $listData = [];
                foreach ($dataList as $value) {
                    $listData[] = [
                        'product_code' => $value->product_code,
                        'product_name' => $value->product_name,
                        'lot_no' => $value->lot_no,
                        'site_name' => $value->site_name,
                        'area_name' => $value->area_name,
                        'location_code' => $value->location_code,
                        "mfg_date" => isset($value->mfg_date) ? \Carbon\Carbon::parse($value->mfg_date)->format("d-m-Y") : "",
                        "exp_date" => isset($value->exp_date) ? \Carbon\Carbon::parse($value->exp_date)->format("d-m-Y") : "",
                        'pqty' => number_format($value->pqty, 0, ',', '.'),
                        'mqty' => number_format($value->mqty, 0, ',', '.'),
                        'bqty' => number_format($value->bqty, 0, ',', '.'),
                        'puom' => $value->puom,
                        'muom' => $value->muom,
                        'buom' => $value->buom,
                        'job_type' => $value->job_type == 'TFRO' ? 'Stock Move From' : 'Stock Move To'
                    ];
                }

                $signature = "<tr><td>Prepare By:</td><td>Checked By:</td><td>Supervised By:</td></tr>";
                $signature .= "<tr><td>Date:</td><td>Date:</td><td>Date:</td></tr>";
                $signature .= "<tr><td>Signature:</td><td>Signature:</td><td>Signature:</td></tr>";

                $data = [
                    "title" => "Stock Transfer Report",
                    "css" => "landscape",
                    "headerOne" => $headerOne,
                    "headOne" => $headOne->toArray(),
                    "headTwo" => $headTwo->toArray(),
                    "bodyOne" => $bodyOne->toArray(),
                    "signature" => $signature,
                    "listData" => $listData,
                    "columnCount" => $columnCount
                ];

                return view('report', $data);
                break;
            case 'pickputs':
                $dataList = DB::table('iv_transfer_batch as a')
                    ->select(
                        'e.principal_name',
                        'a.job_type',
                        'a.id',
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
                        'a.line_id',
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
                    ->join('iv_principal as e', 'a.principal_id', 'e.id')
                    ->where('a.transfer_id', '=', $id)
                    ->orderby('b.product_name', 'ASC')
                    ->orderby('a.lot_no', 'ASC')
                    ->orderby('a.job_type', 'DESC')
                    ->get();

                $job = DB::table('iv_transfer_job as a')
                    ->select('a.*', 'b.principal_name', 'b.multi_level')
                    ->join('iv_principal as b', 'a.principal_id', 'b.id')
                    ->where('a.id', '=', $id)
                    ->first();

                $headerOne = "<tr><td>Principal Name</td><td>:</td><td>$job->principal_name</td></tr>";
                $headerOne .= "<tr><td>Job Number</td><td>:</td><td>$job->job_no</td></tr>";
                $headerOne .= "<tr><td>Job Date</td><td>:</td><td>" . date("d-m-Y", strtotime($job->job_date)) . "</td></tr>";

                $signature = "<tr><td>Prepare By:</td><td>Checked By:</td><td>Supervised By:</td></tr>";
                $signature .= "<tr><td>Date:</td><td>Date:</td><td>Date:</td></tr>";
                $signature .= "<tr><td>Signature:</td><td>Signature:</td><td>Signature:</td></tr>";

                $data = [
                    "title" => "Stock Transfer ( Combined Pick and Put-Away ) Report",
                    "headerOne" => $headerOne,
                    "signature" => $signature,
                    "dataList" => $dataList,
                    'job' => $job
                ];
                // dd($dataList->where('product_code', 'BMTLA')->where('lot_no', '23Y0901AK'));

                return view('transaction.transfer.report', $data);
                break;
            case 'pickput':
                $dataList = DB::table('iv_transfer_batch as a')
                    ->select(
                        'e.principal_name',
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
                    ->join('iv_principal as e', 'a.principal_id', 'e.id')
                    ->where('a.transfer_id', '=', $id)
                    ->orderby('b.product_name', 'ASC')
                    ->orderby('a.lot_no', 'ASC')
                    ->orderby('a.job_type', 'DESC')
                    ->get();

                $job = DB::table('iv_transfer_job as a')
                    ->select('a.*', 'b.principal_name', 'b.multi_level')
                    ->join('iv_principal as b', 'a.principal_id', 'b.id')
                    ->where('a.id', '=', $id)
                    ->first();

                $headerOne = "<tr><td>Principal Name</td><td>:</td><td>$job->principal_name</td></tr>";
                $headerOne .= "<tr><td>Job Number</td><td>:</td><td>$job->job_no</td></tr>";
                $headerOne .= "<tr><td>Job Date</td><td>:</td><td>" . date("d-m-Y", strtotime($job->job_date)) . "</td></tr>";

                if ($job->multi_level == "Yes") {
                    $headOne = collect([
                        ["name" => "SKU No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "SKU Name", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Batch No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Mfg Date", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Exp Date", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Status", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Site", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Area", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Location", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Quantity", "rowspan" => "1", "colspan" => "6"],
                    ]);

                    $headTwo = collect([
                        ['name' => '1st Qty'],
                        ['name' => '1st Unit'],
                        ['name' => '2nd Qty'],
                        ['name' => '2nd Unit'],
                        ['name' => '3rd Qty'],
                        ['name' => '3rd Unit'],
                    ]);

                    $bodyOne = collect([
                        ["name" => "Product Name", "field_name" => "product_code", "class" => "left", "colspan" => "1"],
                        ["name" => "Product Name", "field_name" => "product_name", "class" => "left", "colspan" => "1"],
                        ["name" => "Batch No.", "field_name" => "lot_no", "class" => "left", "colspan" => "1"],
                        ["name" => "Mfg Date", "field_name" => "mfg_date", "class" => "center", "colspan" => "1"],
                        ["name" => "Exp Date", "field_name" => "exp_date", "class" => "center", "colspan" => "1"],
                        ["name" => "Status", "field_name" => "job_type", "class" => "center", "colspan" => "1"],
                        ["name" => "Site", "field_name" => "site_name", "class" => "left", "colspan" => "1"],
                        ["name" => "Area", "field_name" => "area_name", "class" => "left", "colspan" => "1"],
                        ["name" => "Location", "field_name" => "location_code", "class" => "left", "colspan" => "1"],
                        ["name" => "1st", "field_name" => "pqty", "class" => "right", "colspan" => "1"],
                        ["name" => "1st", "field_name" => "puom", "class" => "center", "colspan" => "1"],
                        ['name' => '2nd', 'field_name' => 'mqty', 'class' => 'right', 'colspan' => "1"],
                        ['name' => '2nd', 'field_name' => 'muom', 'class' => 'center', 'colspan' => "1"],
                        ['name' => '3rd', 'field_name' => 'bqty', 'class' => 'right', 'colspan' => "1"],
                        ['name' => '3rd', 'field_name' => 'buom', 'class' => 'center', 'colspan' => "1"],
                    ]);

                    $columnCount = 15;
                } else {
                    $headOne = collect([
                        ["name" => "SKU No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "SKU Name", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Batch No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Mfg Date", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Exp Date", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Status", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Site", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Area", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Location", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Quantity", "rowspan" => "1", "colspan" => "2"],
                    ]);

                    $headTwo = collect([
                        ['name' => '1st Qty'],
                        ['name' => '1st Unit'],
                    ]);

                    $bodyOne = collect([
                        ["name" => "Product Name", "field_name" => "product_code", "class" => "left", "colspan" => "1"],
                        ["name" => "Product Name", "field_name" => "product_name", "class" => "left", "colspan" => "1"],
                        ["name" => "Batch No.", "field_name" => "lot_no", "class" => "left", "colspan" => "1"],
                        ["name" => "Mfg Date", "field_name" => "mfg_date", "class" => "left", "colspan" => "1"],
                        ["name" => "Exp Date", "field_name" => "exp_date", "class" => "left", "colspan" => "1"],
                        ["name" => "Status", "field_name" => "job_type", "class" => "center", "colspan" => "1"],
                        ["name" => "Site", "field_name" => "site_name", "class" => "left", "colspan" => "1"],
                        ["name" => "Area", "field_name" => "area_name", "class" => "left", "colspan" => "1"],
                        ["name" => "Location", "field_name" => "location_code", "class" => "left", "colspan" => "1"],
                        ["name" => "1st", "field_name" => "pqty", "class" => "right", "colspan" => "1"],
                        ["name" => "1st", "field_name" => "puom", "class" => "center", "colspan" => "1"],
                    ]);

                    $columnCount = 11;
                }

                $listData = [];
                foreach ($dataList as $value) {
                    $listData[] = [
                        'product_code' => $value->product_code,
                        'product_name' => $value->product_name,
                        'lot_no' => $value->lot_no,
                        'site_name' => $value->site_name,
                        'area_name' => $value->area_name,
                        'location_code' => $value->location_code,
                        "mfg_date" => isset($value->mfg_date) ? \Carbon\Carbon::parse($value->mfg_date)->format("d-m-Y") : "",
                        "exp_date" => isset($value->exp_date) ? \Carbon\Carbon::parse($value->exp_date)->format("d-m-Y") : "",
                        'pqty' => number_format($value->pqty, 0, ',', '.'),
                        'mqty' => number_format($value->mqty, 0, ',', '.'),
                        'bqty' => number_format($value->bqty, 0, ',', '.'),
                        'puom' => $value->puom,
                        'muom' => $value->muom,
                        'buom' => $value->buom,
                        'job_type' => $value->job_type == 'TFRO' ? 'Stock Move From' : 'Stock Move To'
                    ];
                }

                $signature = "<tr><td>Prepare By:</td><td>Checked By:</td><td>Supervised By:</td></tr>";
                $signature .= "<tr><td>Date:</td><td>Date:</td><td>Date:</td></tr>";
                $signature .= "<tr><td>Signature:</td><td>Signature:</td><td>Signature:</td></tr>";

                $data = [
                    "title" => "Stock Transfer Report",
                    "css" => "landscape",
                    "headerOne" => $headerOne,
                    "headOne" => $headOne->toArray(),
                    "headTwo" => $headTwo->toArray(),
                    "bodyOne" => $bodyOne->toArray(),
                    "signature" => $signature,
                    "listData" => $listData,
                    "columnCount" => $columnCount
                ];

                return view('report', $data);
                break;
            default:
                break;
        }
    }
}
