<?php

namespace App\Http\Controllers\Transaction\Outbound;

use App\Exports\OutboundExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;
use App\Exports\ReportPickingExcel as PickingReportExcelNew;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function index($type, $id)
    {
        switch ($type) {
            case "picking":
                $stockList = DB::table("iv_outbound_batch as a")
                    ->select(
                        "e.customer_name",
                        "a.order_no",
                        "a.product_code",
                        "b.product_name",
                        "a.lot_no",
                        "a.document_ref",
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
                        "b.buom",
                        DB::raw("a.qty * b.gross_weight as weight"),
                        DB::raw("a.qty * b.volume as volume")
                    )
                    ->join("iv_product as b", "a.product_id", "b.id")
                    ->leftjoin("iv_site as c", "a.site_id", "c.id")
                    ->leftjoin("iv_site_area as d", "a.area_id", "d.id")
                    ->leftjoin("iv_customer as e", "a.customer_id", "e.id")
                    ->where("a.outbound_id", $id)
                    ->orderBy("c.site_name", 'ASC')
                    ->orderBy("d.area_name", 'ASC')
                    ->orderBy("a.location_code", 'ASC')
                    ->get();

                $job = DB::table("iv_outbound_job as a")
                    ->select("a.*", "b.principal_name", "c.mode_name", "b.multi_level")
                    ->join("iv_principal as b", "a.principal_id", "b.id")
                    ->join("iv_mode as c", "a.mode_id", "c.id")
                    ->where("a.id", "=", $id)
                    ->first();

                $headerOne = "<tr><td>Principal Name</td><td>:</td><td>$job->principal_name</td></tr>";
                $headerOne .= "<tr><td>Job Number</td><td>:</td><td>$job->job_no</td></tr>";
                $headerOne .= "<tr><td>Job Date</td><td>:</td><td>" . date("d-m-Y", strtotime($job->job_date)) . "</td></tr>";
                $headerOne .= "<tr><td>Reference No.</td><td>:</td><td>$job->reference_no</td></tr>";

                if ($job->multi_level == "Yes") {
                    $headOne = collect([
                        ["name" => "No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Customer Name.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Order No", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "SKU No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "SKU Name", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Batch No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Mfg Date", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Exp Date", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Site", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Area", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Location", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Quantity", "rowspan" => "1", "colspan" => "6"],
                        ["name" => "Gross Weight", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Volume", "rowspan" => "2", "colspan" => "1"],
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
                        ["name" => "Product Name", "field_name" => "product_code", "class" => "left", "colspan" => "2"],
                        ["name" => "Product Name", "field_name" => "product_name", "class" => "left", "colspan" => "4"],
                        ["name" => "1st", "field_name" => "puom", "class" => "center", "colspan" => "1"],
                        ["name" => "2nd", "field_name" => "muom", "class" => "center", "colspan" => "1"],
                        ["name" => "3rd", "field_name" => "buom", "class" => "center", "colspan" => "1"]
                    ]);

                    $bodyOne = collect([
                        ["name" => "Product Name", "field_name" => "line_no", "class" => "center", "colspan" => "1"],
                        ["name" => "Line No", "field_name" => "customer_name", "class" => "center", "colspan" => "1"],
                        ["name" => "Line No", "field_name" => "order_no", "class" => "center", "colspan" => "1"],
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
                        ['name' => '2nd', 'field_name' => 'mqty', 'class' => 'right', 'colspan' => "1"],
                        ['name' => '2nd', 'field_name' => 'muom', 'class' => 'center', 'colspan' => "1"],
                        ['name' => '3rd', 'field_name' => 'bqty', 'class' => 'right', 'colspan' => "1"],
                        ['name' => '3rd', 'field_name' => 'buom', 'class' => 'center', 'colspan' => "1"],
                        ['name' => 'Gross Weight', 'field_name' => 'weight', 'class' => 'right', 'colspan' => "1"],
                        ['name' => 'Volume', 'field_name' => 'volume', 'class' => 'right', 'colspan' => "1"],
                    ]);

                    $columnCount = 19;
                } else {
                    $headOne = collect([
                        ["name" => "No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Customer Name.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Order No", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "SKU No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "SKU Name", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Batch No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Mfg Date", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Exp Date", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Site", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Area", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Location", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Quantity", "rowspan" => "1", "colspan" => "2"],
                        ["name" => "Gross Weight", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Volume", "rowspan" => "2", "colspan" => "1"],
                    ]);

                    $headTwo = collect([
                        ['name' => '1st Qty'],
                        ['name' => '1st Unit'],
                    ]);

                    $bodyOne = collect([
                        ["name" => "Line No", "field_name" => "line_no", "class" => "center", "colspan" => "1"],
                        ["name" => "Line No", "field_name" => "customer_name", "class" => "center", "colspan" => "1"],
                        ["name" => "Line No", "field_name" => "order_no", "class" => "center", "colspan" => "1"],
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
                        ['name' => 'Gross Weight', 'field_name' => 'weight', 'class' => 'right', 'colspan' => "1"],
                        ['name' => 'Volume', 'field_name' => 'volume', 'class' => 'right', 'colspan' => "1"],
                    ]);

                    $columnCount = 15;
                }

                $listData = [];
                $id = 1;
                foreach ($stockList as $value) {
                    $listData[] = [
                        "line_no" => $id,
                        "customer_name" => $value->customer_name,
                        "order_no" => $value->order_no,
                        "product_code" => $value->product_code,
                        "product_name" => $value->product_name,
                        "lot_no" => $value->lot_no,
                        "mfg_date" => isset($value->mfg_date) ? \Carbon\Carbon::parse($value->mfg_date)->format("d-m-Y") : "",
                        "exp_date" => isset($value->exp_date) ? \Carbon\Carbon::parse($value->exp_date)->format("d-m-Y") : "",
                        "site_name" => '',
                        "area_name" => '',
                        "location_code" => '',
                        "pqty" => number_format($value->pqty, 0, ",", "."),
                        "mqty" => number_format($value->mqty, 0, ",", "."),
                        "bqty" => number_format($value->bqty, 0, ",", "."),
                        "puom" => $value->puom,
                        "muom" => $value->muom,
                        "buom" => $value->buom,
                        "weight" => number_format($value->weight, 3, ",", "."),
                        "volume" => number_format($value->volume, 3, ",", "."),
                    ];

                    $id++;
                }

                $signature = "<tr><td>Prepare By:</td><td>Picked By:</td><td>Checked By:</td><td>Supervised By:</td></tr>";
                $signature .= "<tr><td>Date:</td><td>Date:</td><td>Date:</td><td>Date:</td></tr>";
                $signature .= "<tr><td>Signature:</td><td>Signature:</td><td>Signature:</td><td>Signature:</td></tr>";

                $data = [
                    "title" => "Outbound Picking List",
                    "css" => "landscape",
                    "headerOne" => $headerOne,
                    "headOne" => $headOne->toArray(),
                    "headTwo" => $headTwo->toArray(),
                    "bodyOne" => $bodyOne->toArray(),
                    "listData" => $listData,
                    "signature" => $signature,
                    "columnCount" => $columnCount
                ];

                return view("report", $data);
                break;
            case "picking_report":
                $stockList = DB::table("iv_outbound_batch as a")
                    ->select(
                        "e.customer_name",
                        "a.order_no",
                        "a.product_code",
                        "b.product_name",
                        "a.lot_no",
                        "a.document_ref",
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
                        "b.buom",
                        DB::raw("CASE WHEN b.manufactur_code IS NULL THEN 'No' ELSE 'Yes' END as manufactur_code"),
                        DB::raw("a.qty * b.gross_weight as weight"),
                        DB::raw("a.qty * b.volume as volume")
                    )
                    ->join("iv_product as b", "a.product_id", "b.id")
                    ->leftjoin("iv_site as c", "a.site_id", "c.id")
                    ->leftjoin("iv_site_area as d", "a.area_id", "d.id")
                    ->leftjoin("iv_customer as e", "a.customer_id", "e.id")
                    ->where("a.outbound_id", $id)
                    ->orderBy("c.site_name", 'ASC')
                    ->orderBy("d.area_name", 'ASC')
                    ->orderBy("a.location_code", 'ASC')
                    ->get();

                $job = DB::table("iv_outbound_job as a")
                    ->select("a.*", "b.principal_name", "c.mode_name", "b.multi_level")
                    ->join("iv_principal as b", "a.principal_id", "b.id")
                    ->join("iv_mode as c", "a.mode_id", "c.id")
                    ->where("a.id", "=", $id)
                    ->first();

                $headerOne = "<tr><td>Principal Name</td><td>:</td><td>$job->principal_name</td></tr>";
                $headerOne .= "<tr><td>Job Number</td><td>:</td><td>$job->job_no</td></tr>";
                $headerOne .= "<tr><td>Job Date</td><td>:</td><td>" . date("d-m-Y", strtotime($job->job_date)) . "</td></tr>";
                $headerOne .= "<tr><td>Reference No.</td><td>:</td><td>$job->reference_no</td></tr>";

                if ($job->multi_level == "Yes") {
                    $headOne = collect([
                        ["name" => "No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Customer Name.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Order No", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "SKU No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "SKU Name", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Batch No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Mfg Date", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Exp Date", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Site", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Area", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Location", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Quantity", "rowspan" => "1", "colspan" => "6"],
                        ["name" => "Gross Weight", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Volume", "rowspan" => "2", "colspan" => "1"],
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
                        ["name" => "Product Name", "field_name" => "product_code", "class" => "left", "colspan" => "2"],
                        ["name" => "Product Name", "field_name" => "product_name", "class" => "left", "colspan" => "4"],
                        ["name" => "1st", "field_name" => "puom", "class" => "center", "colspan" => "1"],
                        ["name" => "2nd", "field_name" => "muom", "class" => "center", "colspan" => "1"],
                        ["name" => "3rd", "field_name" => "buom", "class" => "center", "colspan" => "1"]
                    ]);

                    $bodyOne = collect([
                        ["name" => "Product Name", "field_name" => "line_no", "class" => "center", "colspan" => "1"],
                        ["name" => "Line No", "field_name" => "customer_name", "class" => "center", "colspan" => "1"],
                        ["name" => "Line No", "field_name" => "order_no", "class" => "center", "colspan" => "1"],
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
                        ['name' => '2nd', 'field_name' => 'mqty', 'class' => 'right', 'colspan' => "1"],
                        ['name' => '2nd', 'field_name' => 'muom', 'class' => 'center', 'colspan' => "1"],
                        ['name' => '3rd', 'field_name' => 'bqty', 'class' => 'right', 'colspan' => "1"],
                        ['name' => '3rd', 'field_name' => 'buom', 'class' => 'center', 'colspan' => "1"],
                        ['name' => 'Gross Weight', 'field_name' => 'weight', 'class' => 'right', 'colspan' => "1"],
                        ['name' => 'Volume', 'field_name' => 'volume', 'class' => 'right', 'colspan' => "1"],
                    ]);

                    $columnCount = 19;
                } else {
                    $headOne = collect([
                        ["name" => "No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Customer Name.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Order No", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "SKU No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "SKU Name", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Batch No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Mfg Date", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Exp Date", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Site", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Area", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Location", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Quantity", "rowspan" => "1", "colspan" => "2"],
                        ["name" => "Gross Weight", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Volume", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Scan", "rowspan" => "2", "colspan" => "1"],
                    ]);

                    $headTwo = collect([
                        ['name' => '1st Qty'],
                        ['name' => '1st Unit'],
                    ]);

                    $bodyOne = collect([
                        ["name" => "Line No", "field_name" => "line_no", "class" => "center", "colspan" => "1"],
                        ["name" => "Line No", "field_name" => "customer_name", "class" => "center", "colspan" => "1"],
                        ["name" => "Line No", "field_name" => "order_no", "class" => "center", "colspan" => "1"],
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
                        ['name' => 'Gross Weight', 'field_name' => 'weight', 'class' => 'right', 'colspan' => "1"],
                        ['name' => 'Volume', 'field_name' => 'volume', 'class' => 'right', 'colspan' => "1"],
                        ['name' => 'Scan', 'field_name' => 'manufactur_code', 'class' => 'right', 'colspan' => "1"],
                    ]);

                    $columnCount = 15;
                }

                $listData = [];
                $id = 1;
                foreach ($stockList as $value) {
                    $listData[] = [
                        "line_no" => $id,
                        "customer_name" => $value->customer_name,
                        "order_no" => $value->order_no,
                        "product_code" => $value->product_code,
                        "product_name" => $value->product_name,
                        "lot_no" => $value->lot_no,
                        "mfg_date" => isset($value->mfg_date) ? \Carbon\Carbon::parse($value->mfg_date)->format("d-m-Y") : "",
                        "exp_date" => isset($value->exp_date) ? \Carbon\Carbon::parse($value->exp_date)->format("d-m-Y") : "",
                        "site_name" => $value->site_name,
                        "area_name" => $value->area_name,
                        "location_code" => $value->location_code,
                        "pqty" => number_format($value->pqty, 0, ",", "."),
                        "mqty" => number_format($value->mqty, 0, ",", "."),
                        "bqty" => number_format($value->bqty, 0, ",", "."),
                        "puom" => $value->puom,
                        "muom" => $value->muom,
                        "buom" => $value->buom,
                        "weight" => number_format($value->weight, 3, ",", "."),
                        "volume" => number_format($value->volume, 3, ",", "."),
                        "manufactur_code" => $value->manufactur_code
                    ];

                    $id++;
                }

                $signature = "<tr><td>Prepare By:</td><td>Picked By:</td><td>Checked By:</td><td>Supervised By:</td></tr>";
                $signature .= "<tr><td>Date:</td><td>Date:</td><td>Date:</td><td>Date:</td></tr>";
                $signature .= "<tr><td>Signature:</td><td>Signature:</td><td>Signature:</td><td>Signature:</td></tr>";

                $data = [
                    "title" => "Outbound Picking Report",
                    "css" => "landscape",
                    "headerOne" => $headerOne,
                    "headOne" => $headOne->toArray(),
                    "headTwo" => $headTwo->toArray(),
                    "bodyOne" => $bodyOne->toArray(),
                    "listData" => $listData,
                    "signature" => $signature,
                    "columnCount" => $columnCount
                ];
                return view("report", $data);
                break;
            case "pallet":
                $list = DB::table("iv_inbound_batch as a")
                    ->select("a.*", "b.product_name", "c.site_name", "d.area_name")
                    ->join("iv_product as b", "a.product_id", "b.id")
                    ->leftjoin("iv_site as c", "a.site_id", "c.id")
                    ->leftjoin("iv_site_area as d", "a.area_id", "d.id")
                    ->where("a.inbound_id", "=", $id)
                    ->get();

                $data = [
                    "listData" => $list
                ];
                return view("transaction.inbound.pallet", $data);

                break;
            case "confirm":
                $stockList = DB::table("iv_outbound_batch as a")
                    ->select(
                        "a.product_code",
                        "b.product_name",
                        "a.lot_no",
                        "a.document_ref",
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
                        "b.buom",
                        "b.volume",
                        "b.gross_weight"
                    )
                    ->join("iv_product as b", "a.product_id", "b.id")
                    ->leftjoin("iv_site as c", "a.site_id", "c.id")
                    ->leftjoin("iv_site_area as d", "a.area_id", "d.id")
                    ->where("a.outbound_id", "=", $id)
                    ->get();

                $job = DB::table("iv_outbound_job as a")
                    ->select("a.*", "b.principal_name", "c.mode_name", "b.multi_level")
                    ->join("iv_principal as b", "a.principal_id", "b.id")
                    ->join("iv_mode as c", "a.mode_id", "c.id")
                    ->where("a.id", "=", $id)
                    ->first();

                $headerOne = "<tr><td>Principal Name</td><td>:</td><td>$job->principal_name</td></tr>";
                $headerOne .= "<tr><td>Job Number</td><td>:</td><td>$job->job_no</td></tr>";
                $headerOne .= "<tr><td>Job Date</td><td>:</td><td>" . date("d-m-Y", strtotime($job->job_date)) . "</td></tr>";
                $headerOne .= "<tr><td>Reference No.</td><td>:</td><td>$job->reference_no</td></tr>";

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
                        ["name" => "Product Name", "field_name" => "product_code", "class" => "left", "colspan" => "2"],
                        ["name" => "Product Name", "field_name" => "product_name", "class" => "left", "colspan" => "4"],
                        ["name" => "1st", "field_name" => "puom", "class" => "center", "colspan" => "1"],
                        ["name" => "2nd", "field_name" => "muom", "class" => "center", "colspan" => "1"],
                        ["name" => "3rd", "field_name" => "buom", "class" => "center", "colspan" => "1"]
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
                        "product_code" => $value->product_code,
                        "product_name" => $value->product_name,
                        "lot_no" => $value->lot_no,
                        "mfg_date" => isset($value->mfg_date) ? \Carbon\Carbon::parse($value->mfg_date)->format("d-m-Y") : "",
                        "exp_date" => isset($value->exp_date) ? \Carbon\Carbon::parse($value->exp_date)->format("d-m-Y") : "",
                        "site_name" => $value->site_name,
                        "area_name" => $value->area_name,
                        "location_code" => $value->location_code,
                        "pqty" => number_format($value->pqty, 0, ",", "."),
                        "mqty" => number_format($value->mqty, 0, ",", "."),
                        "bqty" => number_format($value->bqty, 0, ",", "."),
                        "puom" => $value->puom,
                        "muom" => $value->muom,
                        "buom" => $value->buom
                    ];
                }

                $data = [
                    "title" => "Outbound Confirmation Report",
                    "css" => "landscape",
                    "headerOne" => $headerOne,
                    "headOne" => $headOne->toArray(),
                    "headTwo" => $headTwo->toArray(),
                    "bodyOne" => $bodyOne->toArray(),
                    "listData" => $listData,
                    "columnCount" => $columnCount
                ];

                return view("report", $data);
                break;
            case "despatch":
                $despatch = DB::table("iv_outbound_despatch as a")
                    ->select(
                        "a.*",
                        "b.principal_name",
                        "b.address1 as prin_address1",
                        "b.address2 as prin_address2",
                        "b.address3 as prin_address3",
                        "b.address4 as prin_address4",
                        "c.customer_name",
                        "d.store_name",
                        "d.address1 as store_address1",
                        "d.address2 as store_address2",
                        "d.address3 as store_address3",
                        "d.address4 as store_address4",
                        "b.multi_level",
                        "b.volume_flag"
                    )
                    ->join("iv_principal as b", "a.principal_id", "b.id")
                    ->join("iv_customer as c", "a.customer_id", "c.id")
                    ->leftjoin("tm_store as d", "a.store_id", "d.id")
                    ->where("a.id", $id)
                    ->first();

                $order = [];
                $detail = [];
                if (isset($despatch)) {
                    $order = DB::table("iv_outbound_order as a")
                        ->select("a.*")
                        ->where("a.outbound_id", $despatch->outbound_id)
                        ->where("a.customer_id", $despatch->customer_id)
                        ->first();

                    $detail = DB::table("iv_outbound_batch as a")
                        ->select(
                            "a.id",
                            "a.product_code",
                            "b.product_name",
                            "a.lot_no",
                            "a.pqty",
                            "a.mqty",
                            "a.remarks",
                            "b.puom",
                            "b.muom",
                            "b.buom",
                            "b.uppp",
                            "b.muppp",
                            "b.volume",
                            "b.gross_weight",
                            DB::raw("sum(a.qty) as qty")
                        )
                        ->join("iv_product as b", "a.product_id", "b.id")
                        ->where("a.outbound_id", $despatch->outbound_id)
                        ->where("a.customer_id", $despatch->customer_id)
                        ->groupBy(
                            "a.product_code",
                            "b.product_name",
                            "a.lot_no",
                            "b.puom",
                            "b.muom",
                            "b.buom",
                            "b.uppp",
                            "b.muppp",
                            "b.volume",
                            "b.gross_weight"
                        )
                        ->get();
                }
                $data = [
                    "view_data" => $despatch,
                    "order_data" => $order,
                    "detail_list" => $detail
                ];

                return view("transaction.outbound.despatch", $data);
                break;
            case "loading_list":
                $despatch = DB::table("iv_outbound_despatch as a")
                    ->select(
                        "a.*",
                        "b.principal_name",
                        "b.address1 as prin_address1",
                        "b.address2 as prin_address2",
                        "b.address3 as prin_address3",
                        "b.address4 as prin_address4",
                        "c.customer_name",
                        "d.store_name",
                        "d.address1 as store_address1",
                        "d.address2 as store_address2",
                        "d.address3 as store_address3",
                        "d.address4 as store_address4",
                        "b.multi_level",
                        "b.volume_flag"
                    )
                    ->join("iv_principal as b", "a.principal_id", "b.id")
                    ->join("iv_customer as c", "a.customer_id", "c.id")
                    ->leftjoin("tm_store as d", "a.store_id", "d.id")
                    ->where("a.outbound_id", $id)
                    ->first();

                $order = [];
                $detail = [];
                if (isset($despatch)) {
                    $order = DB::table("iv_outbound_order as a")
                        ->select("a.*")
                        ->where("a.outbound_id", $despatch->outbound_id)
                        ->where("a.customer_id", $despatch->customer_id)
                        ->first();

                    $detail = DB::table("iv_outbound_batch as a")
                        ->select(
                            "a.id",
                            "a.product_code",
                            "b.product_name",
                            "a.lot_no",
                            "a.pqty",
                            "a.mqty",
                            "a.remarks",
                            "b.puom",
                            "b.muom",
                            "b.buom",
                            "b.uppp",
                            "b.muppp",
                            "b.volume",
                            "b.gross_weight",
                            DB::raw("sum(a.qty) as qty")
                        )
                        ->join("iv_product as b", "a.product_id", "b.id")
                        ->where("a.outbound_id", $id)
                        // ->where("a.customer_id", $despatch->customer_id)
                        ->groupBy(
                            "a.product_code",
                            "b.product_name",
                            "a.lot_no",
                            "b.puom",
                            "b.muom",
                            "b.buom",
                            "b.uppp",
                            "b.muppp",
                            "b.volume",
                            "b.gross_weight"
                        )
                        ->get();
                }
                $data = [
                    "view_data" => $despatch,
                    "order_data" => $order,
                    "detail_list" => $detail
                ];

                return view("transaction.outbound.loading_list", $data);
                break;
            default:

                break;
        }
    }

    public function palletPickingReport($id)
    {
        $header = DB::table('iv_outbound_job')->where('id', $id)->first();
        // dd($id);
        $getName =  DB::table('iv_principal')->where('id', $header->principal_id)->value('principal_name');

        $pickingan = DB::table("iv_outbound_batch")
            ->select("serial_id", "product_code", "location_code",  "location_id", "qty", "job_no", 'lot_no', "product_id")
            ->where("outbound_id", $id)
            ->orderBy("location_code", 'ASC')
            ->get();

        // dd($pickingan);
        $productID = $pickingan->pluck('product_id')->toArray();
        $locationID = $pickingan->pluck('location_id')->toArray();
        $lotNo = $pickingan->pluck('lot_no')->toArray();
        $transaction = DB::table('iv_stock_transaction')
            ->select('product_code', 'location_code', 'lot_no', 'job_type', 'qty', 'reference_no', 'created_at', 'job_date')
            ->orderBy('location_code', 'ASC')
            ->whereYear('job_date', date('Y'))
            ->whereIn('product_id', $productID)
            ->whereIn('location_id', $locationID)
            ->whereIn('lot_no', $lotNo)
            ->get();

        // dd($transaction);

        $grouped = $transaction->groupBy(function ($item) {
            // dd($item);
            return $item->product_code . '|' . $item->location_code . '|' . $item->lot_no;
        });

        $results = $grouped->map(function ($items) use ($header) {
            // dd(['ini items' => $items, 'header' => $header]);
            // dd($items);
            $stockAwal = $this->getStockAwal($items, $header);
            // dd($stockAwal);
            // Calculate 'pickingan' based on 'reference_no' and 'job_type' 'EXP'
            $pickingan = $items->where('reference_no', $header->job_no)
                ->where('job_type', 'EXP')
                ->sum('qty');

            return [
                'stockAwal' => $stockAwal,
                'pickingan' => $pickingan,
                'pcsan' => $stockAwal - $pickingan,
            ];
        });

        // dd($results);

        $filtered = [];
        foreach ($transaction->where('reference_no', $header->job_no) as $key => $value) {
            $filtered[$value->product_code . '|' . $value->location_code . '|' . $value->lot_no] = $results[$value->product_code . '|' . $value->location_code . '|' . $value->lot_no];
        }


        // Print results
        $data = [];
        foreach ($filtered as $keys => $values) {
            // dd($values);
            $productCode = explode('|', $keys)[0];
            $locationCode = explode('|', $keys)[1];
            $lotNo = explode('|', $keys)[2];
            $yangDiAmbil = $values['stockAwal'] - $values['pcsan'];
            $data[] = [
                'product_code' => $productCode,
                'location_code' => $locationCode,
                'lot_no' => $lotNo,
                'stockAwal' => $values['stockAwal'],
                'yangDiAmbil' => $yangDiAmbil,
                'stockAkhir' => $values['pcsan'],
            ];
            // dd($data);
        }
        $despatch = DB::table('iv_outbound_despatch')
            ->select('outbound_id', 'vehicle_no', 'store_id', 'size_id', 'container_no')
            ->where('outbound_id', $header->id)
            ->get();
        $despatch->map(function ($value) {
            $value->size = DB::table('iv_container_size')->where('id', $value->size_id)->value('size_name');
            $value->tujuan = DB::table('tm_store')->where('id', $value->store_id)->value('store_name');
            return $value;
        });
        $despatch = $despatch->first();
        return view("transaction.outbound.pallet_picking_report", compact('header', 'data', 'getName', 'despatch'));
    }

    private function getStockAwal($items, $header)
    {
        $confirmDate = Carbon::parse($header->confirmed_date);
        // dd($header->confirmed_date);
        $impQty = $items->whereIn('job_type', ['IMP', 'TFRI', 'ADJ+'])->sum('qty');
        // dd($impQty);
        $expQty = $items->filter(function ($value) use ($confirmDate, $header) {
            $created_at = Carbon::parse($value->created_at);
            // dd($confirmDate);

            return  $created_at < $confirmDate &&
                $value->reference_no != $header->job_no &&
                $value->job_type == 'EXP';
        })->sum('qty');
        // dd($expQty);

        return $impQty - $expQty;
    }

    public function export($outbound_id)
    {
        $job_no = DB::table('iv_outbound_job')
            ->where('id', $outbound_id)->value('job_no');

        return Excel::download(new PickingReportExcelNew($outbound_id), "outbound-picking-$job_no.xlsx");
    }
    public function addRemarks(Request $request)
    {
        DB::transaction(function () use ($request) {
            try {
                for ($i = 0; $i < count($request->id); $i++) {
                    DB::table('iv_outbound_batch')
                        ->where('id', $request->id[$i])
                        ->update([
                            'remarks' => $request->remarks[$i]
                        ]);
                }
                DB::commit();
                Session::flash('success', 'Update Successfully..');
            } catch (\Exception $e) {
                DB::rollBack();
                Session::flash('error', $e->getMessage());
            }
        });
        return back();
    }
}
