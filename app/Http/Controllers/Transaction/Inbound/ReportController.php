<?php

namespace App\Http\Controllers\Transaction\Inbound;

use App\Exports\InboundExport;
use App\Http\Controllers\Controller;
use App\Models\Master\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index($type, $id, $product_code = '', $picking_id = '')
    {
        switch ($type) {
            case "icr":
                $job = DB::table("iv_inbound_job as a")
                    ->select("a.*", "b.principal_name", "c.mode_name", "d.vehicle_no", "b.multi_level")
                    ->join("iv_principal as b", "a.principal_id", "b.id")
                    ->join("iv_mode as c", "a.mode_id", "c.id")
                    ->join("iv_inbound_vehicle as d", "a.id", "d.inbound_id")
                    ->where("a.id", "=", $id)
                    ->get();

                $detail = DB::table("iv_inbound_detail as a")
                    ->select(
                        "a.vehicle_no",
                        "a.product_code",
                        "b.product_name",
                        "a.po_number",
                        "a.lot_no",
                        "a.document_ref",
                        "a.mfg_date",
                        "a.exp_date",
                        "b.uppp",
                        "b.muppp",
                        DB::raw("convert((a.qty  - (a.qty % b.uppp)) / b.uppp, int) as pqty"),
                        DB::raw("convert(((a.qty % b.uppp) - ((a.qty % b.uppp) % b.muppp)) / b.muppp, int) as mqty"),
                        DB::raw("a.qty % b.uppp % b.muppp as bqty"),
                        "b.puom",
                        "b.muom",
                        "b.buom",
                        "b.volume",
                        "b.gross_weight"
                    )
                    ->join("iv_product as b", "a.product_id", "b.id")
                    ->where("a.inbound_id", "=", $id)
                    ->get();

                $data = [
                    "job_view" => $job,
                    "detail_list" => $detail
                ];

                return view("transaction.inbound.icr", $data);
                break;
            case "grn":
                $detail = DB::table("iv_inbound_detail as a")
                    ->select(
                        "a.product_code",
                        "b.product_name",
                        "a.po_number",
                        "a.lot_no",
                        "a.document_ref",
                        "a.mfg_date",
                        "a.exp_date",
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
                        "a.discrepancy_pqty",
                        "a.discrepancy_mqty",
                        "a.discrepancy_bqty",
                        "a.discrepancy_qty",
                        "b.puom",
                        "b.muom",
                        "b.buom",
                        "b.volume",
                        "b.gross_weight",
                        "a.product_status",
                        DB::raw("CASE WHEN a.qty = a.actual_qty THEN 'Full' WHEN a.qty > a.actual_qty THEN 'Short' ELSE 'Excess' END as received")
                    )
                    ->join("iv_product as b", "a.product_id", "b.id")
                    ->where("a.inbound_id", "=", $id)
                    ->where("a.received_flag", "=", "Yes")
                    ->get();

                $validasi = DB::table('iv_inbound_detail')
                    ->where('inbound_id', $id)
                    ->where("received_flag", "=", "Yes")
                    ->count();

                if ($validasi > 0) {
                    $detail = DB::table('iv_inbound_per_pallet as a')
                        ->where('a.inbound_id', $id)
                        ->get();

                    $detail->map(function ($value) {
                        $value->master_product = DB::table('iv_product')
                            ->where('product_code', $value->product_code)
                            ->first();

                        $value->master_detail = DB::table('iv_inbound_detail')
                            ->where('product_code', $value->product_code)
                            ->where('inbound_id', $value->inbound_id)
                            ->where('id', $value->picking_id)
                            ->first();
                    });
                } else {
                    $detail = $detail;
                }

                $job = DB::table("iv_inbound_job as a")
                    ->select("a.*", "b.principal_name", "c.mode_name", "b.multi_level")
                    ->join("iv_principal as b", "a.principal_id", "b.id")
                    ->join("iv_mode as c", "a.mode_id", "c.id")
                    ->where("a.id", "=", $id)
                    ->first();
                // dd($detail);

                $data = [
                    "job_view" => $job,
                    "detail_list" => $detail
                ];

                return view("transaction.inbound.grn", $data);
                break;
            case "grns":
                $stockList = DB::table("iv_inbound_detail as a")
                    ->select(
                        "a.product_code",
                        "b.product_name",
                        "a.po_number",
                        "a.lot_no",
                        "a.document_ref",
                        "a.mfg_date",
                        "a.exp_date",
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
                        "b.volume",
                        "b.gross_weight",
                        "a.product_status",
                        DB::raw("CASE WHEN a.qty = a.actual_qty THEN 'Full' WHEN a.qty > a.actual_qty THEN 'Short' ELSE 'Excess' END as received")
                    )
                    ->join("iv_product as b", "a.product_id", "b.id")
                    ->where("a.inbound_id", "=", $id)
                    ->where("a.received_flag", "=", "Yes")
                    ->get();

                $job = DB::table("iv_inbound_job as a")
                    ->select("a.*", "b.principal_name", "c.mode_name", "b.multi_level")
                    ->join("iv_principal as b", "a.principal_id", "b.id")
                    ->join("iv_mode as c", "a.mode_id", "c.id")
                    ->where("a.id", "=", $id)
                    ->first();

                $headerOne = "<tr><td>Principal Name</td><td>:</td><td>$job->principal_name</td></tr>";
                $headerOne .= "<tr><td>Job Number</td><td>:</td><td>$job->job_no</td></tr>";
                $headerOne .= "<tr><td>Job Date</td><td>:</td><td>" . date("d-m-Y", strtotime($job->job_date)) . "</td></tr>";

                if ($job->multi_level == "Yes") {
                    $headOne = collect([
                        ["name" => "Reference No", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "PO / DO No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "SKU No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "SKU Name", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Batch No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Mfg Date", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Exp Date", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Volume", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Gross Weight", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Expected Quantity", "rowspan" => "1", "colspan" => "6"],
                        ["name" => "Actual Quantity", "rowspan" => "1", "colspan" => "6"],
                        ["name" => "Received Status", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Product Status", "rowspan" => "2", "colspan" => "1"],
                    ]);

                    $headTwo = collect([
                        ["name" => "1st Qty"],
                        ["name" => "1st Unit"],
                        ["name" => "2nd Qty"],
                        ["name" => "2nd Unit"],
                        ["name" => "3rd Qty"],
                        ["name" => "3rd Unit"],
                        ["name" => "1st Qty"],
                        ["name" => "1st Unit"],
                        ["name" => "2nd Qty"],
                        ["name" => "2nd Unit"],
                        ["name" => "3rd Qty"],
                        ["name" => "3rd Unit"],
                    ]);

                    $bodyOne = collect([
                        ["name" => "Ref. No.", "field_name" => "document_ref", "class" => "left", "colspan" => "1"],
                        ["name" => "Ref. No.", "field_name" => "po_number", "class" => "left", "colspan" => "1"],
                        ["name" => "SKU No.", "field_name" => "product_code", "class" => "left", "colspan" => "1"],
                        ["name" => "SKU Name", "field_name" => "product_name", "class" => "left", "colspan" => "1"],
                        ["name" => "Batch No.", "field_name" => "lot_no", "class" => "left", "colspan" => "1"],
                        ["name" => "Mfg Date", "field_name" => "mfg_date", "class" => "left", "colspan" => "1"],
                        ["name" => "Exp Date", "field_name" => "exp_date", "class" => "left", "colspan" => "1"],
                        ["name" => "Volume", "field_name" => "volume", "class" => "right", "colspan" => "1"],
                        ["name" => "Gross Weight", "field_name" => "gross_weight", "class" => "right", "colspan" => "1"],
                        ["name" => "1st", "field_name" => "pqty", "class" => "right", "colspan" => "1"],
                        ["name" => "1st", "field_name" => "puom", "class" => "center", "colspan" => "1"],
                        ["name" => "2nd", "field_name" => "mqty", "class" => "right", "colspan" => "1"],
                        ["name" => "2nd", "field_name" => "muom", "class" => "center", "colspan" => "1"],
                        ["name" => "3rd", "field_name" => "bqty", "class" => "right", "colspan" => "1"],
                        ["name" => "3rd", "field_name" => "buom", "class" => "center", "colspan" => "1"],
                        ["name" => "1st", "field_name" => "actual_pqty", "class" => "right", "colspan" => "1"],
                        ["name" => "1st", "field_name" => "puom", "class" => "center", "colspan" => "1"],
                        ["name" => "2nd", "field_name" => "actual_mqty", "class" => "right", "colspan" => "1"],
                        ["name" => "2nd", "field_name" => "muom", "class" => "center", "colspan" => "1"],
                        ["name" => "3rd", "field_name" => "actual_bqty", "class" => "right", "colspan" => "1"],
                        ["name" => "3rd", "field_name" => "buom", "class" => "center", "colspan" => "1"],
                        ["name" => "Received", "field_name" => "received", "class" => "center", "colspan" => "1"],
                        ["name" => "Product", "field_name" => "product_status", "class" => "center", "colspan" => "1"]
                    ]);

                    $listData = [];
                    foreach ($stockList as $value) {
                        $listData[] = [
                            "document_ref" => $value->document_ref,
                            "po_number" => $value->po_number,
                            "product_code" => $value->product_code,
                            "product_name" => $value->product_name,
                            "lot_no" => $value->lot_no,
                            "mfg_date" => isset($value->mfg_date) ? \Carbon\Carbon::parse($value->mfg_date)->format("d-m-Y") : "",
                            "exp_date" => isset($value->exp_date) ? \Carbon\Carbon::parse($value->exp_date)->format("d-m-Y") : "",
                            "volume" => number_format($value->volume * $value->qty, 0, ",", "."),
                            "gross_weight" => number_format($value->gross_weight * $value->qty, 0, ",", "."),
                            "pqty" => number_format($value->pqty, 0, ",", "."),
                            "mqty" => number_format($value->mqty, 0, ",", "."),
                            "bqty" => number_format($value->bqty, 0, ",", "."),
                            "actual_pqty" => number_format($value->actual_pqty, 0, ",", "."),
                            "actual_mqty" => number_format($value->actual_mqty, 0, ",", "."),
                            "actual_bqty" => number_format($value->actual_bqty, 0, ",", "."),
                            "puom" => $value->puom,
                            "muom" => $value->muom,
                            "buom" => $value->buom,
                            "received" => $value->received,
                            "product_status" => $value->product_status
                        ];
                    }

                    $data = [
                        "title" => "Goods Receipt Report",
                        "css" => "landscape",
                        "headerOne" => $headerOne,
                        "headOne" => $headOne->toArray(),
                        "headTwo" => $headTwo->toArray(),
                        "bodyOne" => $bodyOne->toArray(),
                        "listData" => $listData,
                        "columnCount" => 23
                    ];
                } else {
                    $headOne = collect([
                        ["name" => "Reference No", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "PO / DO No", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "SKU No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "SKU Name", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Batch No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Mfg Date", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Exp Date", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Volume", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Gross Weight", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Expected Quantity", "rowspan" => "1", "colspan" => "2"],
                        ["name" => "Actual Quantity", "rowspan" => "1", "colspan" => "2"],
                        ["name" => "Received Status", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Product Status", "rowspan" => "2", "colspan" => "1"],
                    ]);

                    $headTwo = collect([
                        ["name" => "1st Qty"],
                        ["name" => "1st Unit"],
                        ["name" => "1st Qty"],
                        ["name" => "1st Unit"],
                    ]);

                    $bodyOne = collect([
                        ["name" => "Ref. No.", "field_name" => "document_ref", "class" => "left", "colspan" => "1"],
                        ["name" => "Ref. No.", "field_name" => "po_number", "class" => "left", "colspan" => "1"],
                        ["name" => "SKU No.", "field_name" => "product_code", "class" => "left", "colspan" => "1"],
                        ["name" => "SKU Name", "field_name" => "product_name", "class" => "left", "colspan" => "1"],
                        ["name" => "Batch No.", "field_name" => "lot_no", "class" => "left", "colspan" => "1"],
                        ["name" => "Mfg Date", "field_name" => "mfg_date", "class" => "left", "colspan" => "1"],
                        ["name" => "Exp Date", "field_name" => "exp_date", "class" => "left", "colspan" => "1"],
                        ["name" => "Volume", "field_name" => "volume", "class" => "right", "colspan" => "1"],
                        ["name" => "Gross Weight", "field_name" => "gross_weight", "class" => "right", "colspan" => "1"],
                        ["name" => "1st", "field_name" => "pqty", "class" => "right", "colspan" => "1"],
                        ["name" => "1st", "field_name" => "puom", "class" => "center", "colspan" => "1"],
                        ["name" => "1st", "field_name" => "actual_pqty", "class" => "right", "colspan" => "1"],
                        ["name" => "1st", "field_name" => "puom", "class" => "center", "colspan" => "1"],
                        ["name" => "Received", "field_name" => "received", "class" => "center", "colspan" => "1"],
                        ["name" => "Product", "field_name" => "product_status", "class" => "center", "colspan" => "1"]
                    ]);

                    $listData = [];
                    foreach ($stockList as $value) {
                        $listData[] = [
                            "document_ref" => $value->document_ref,
                            "po_number" => $value->po_number,
                            "product_code" => $value->product_code,
                            "product_name" => $value->product_name,
                            "lot_no" => $value->lot_no,
                            "mfg_date" => isset($value->mfg_date) ? \Carbon\Carbon::parse($value->mfg_date)->format("d-m-Y") : "",
                            "exp_date" => isset($value->exp_date) ? \Carbon\Carbon::parse($value->exp_date)->format("d-m-Y") : "",
                            "volume" => number_format($value->volume * $value->qty, 0, ",", "."),
                            "gross_weight" => number_format($value->gross_weight * $value->qty, 0, ",", "."),
                            "pqty" => number_format($value->pqty, 0, ",", "."),
                            "mqty" => number_format($value->mqty, 0, ",", "."),
                            "bqty" => number_format($value->bqty, 0, ",", "."),
                            "actual_pqty" => number_format($value->actual_pqty, 0, ",", "."),
                            "actual_mqty" => number_format($value->actual_mqty, 0, ",", "."),
                            "actual_bqty" => number_format($value->actual_bqty, 0, ",", "."),
                            "puom" => $value->puom,
                            "muom" => $value->muom,
                            "buom" => $value->buom,
                            "received" => $value->received,
                            "product_status" => $value->product_status
                        ];
                    }

                    $data = [
                        "title" => "Goods Receipt Report",
                        "css" => "landscape",
                        "headerOne" => $headerOne,
                        "headOne" => $headOne->toArray(),
                        "headTwo" => $headTwo->toArray(),
                        "bodyOne" => $bodyOne->toArray(),
                        "listData" => $listData,
                        "columnCount" => 15
                    ];
                }


                return view("report", $data);
                break;
            case "putaway_report":
                $stockList = DB::table("iv_inbound_batch as a")
                    ->select(
                        "a.product_code",
                        "b.product_name",
                        "a.po_number",
                        "a.lot_no",
                        "a.document_ref",
                        "a.mfg_date",
                        "a.exp_date",
                        "c.site_name",
                        "d.area_name",
                        "a.location_code",
                        "b.uppp",
                        "b.muppp",
                        "a.qty",
                        "a.mqty",
                        "a.bqty",
                        "a.qty",
                        "b.puom",
                        "b.muom",
                        "b.buom",
                        "b.volume",
                        "b.gross_weight",
                        DB::raw("CASE WHEN b.manufactur_code IS NULL THEN 'No' ELSE 'Yes' END as manufactur_code")
                    )
                    ->join("iv_product as b", "a.product_id", "b.id")
                    ->leftjoin("iv_site as c", "a.site_id", "c.id")
                    ->leftjoin("iv_site_area as d", "a.area_id", "d.id")
                    ->where("a.inbound_id", "=", $id)
                    ->get();

                $job = DB::table("iv_inbound_job as a")
                    ->select("a.*", "b.principal_name", "c.mode_name", "b.multi_level")
                    ->join("iv_principal as b", "a.principal_id", "b.id")
                    ->join("iv_mode as c", "a.mode_id", "c.id")
                    ->where("a.id", "=", $id)
                    ->first();

                $headerOne = "<tr><td>Principal Name</td><td>:</td><td>$job->principal_name</td></tr>";
                $headerOne .= "<tr><td>Job Number</td><td>:</td><td>$job->job_no</td></tr>";
                $headerOne .= "<tr><td>Job Date</td><td>:</td><td>" . date("d-m-Y", strtotime($job->job_date)) . "</td></tr>";

                if ($job->multi_level == "Yes") {
                    $headOne = collect([
                        ["name" => "Reference No", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "PO / DO No.", "rowspan" => "2", "colspan" => "1"],
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
                        ["name" => "Ref. No.", "field_name" => "document_ref", "class" => "left", "colspan" => "1"],
                        ["name" => "Ref. No.", "field_name" => "po_number", "class" => "left", "colspan" => "1"],
                        ["name" => "Product Name", "field_name" => "product_code", "class" => "left", "colspan" => "1"],
                        ["name" => "Product Name", "field_name" => "product_name", "class" => "left", "colspan" => "1"],
                        ["name" => "Batch No.", "field_name" => "lot_no", "class" => "left", "colspan" => "1"],
                        ["name" => "Mfg Date", "field_name" => "mfg_date", "class" => "left", "colspan" => "1"],
                        ["name" => "Exp Date", "field_name" => "exp_date", "class" => "left", "colspan" => "1"],
                        ["name" => "Site", "field_name" => "site_name", "class" => "left", "colspan" => "1"],
                        ["name" => "Area", "field_name" => "area_name", "class" => "left", "colspan" => "1"],
                        ["name" => "Location", "field_name" => "location_code", "class" => "left", "colspan" => "1"],
                        ["name" => "1st", "field_name" => "qty", "class" => "right", "colspan" => "1"],
                        ["name" => "1st", "field_name" => "puom", "class" => "center", "colspan" => "1"],
                        ['name' => '2nd', 'field_name' => 'mqty', 'class' => 'right', 'colspan' => "1"],
                        ['name' => '2nd', 'field_name' => 'muom', 'class' => 'center', 'colspan' => "1"],
                        ['name' => '3rd', 'field_name' => 'bqty', 'class' => 'right', 'colspan' => "1"],
                        ['name' => '3rd', 'field_name' => 'buom', 'class' => 'center', 'colspan' => "1"],
                    ]);

                    $columnCount = 16;
                } else {
                    $headOne = collect([
                        ["name" => "Reference No", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "PO / DO No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "SKU No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "SKU Name", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Batch No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Mfg Date", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Exp Date", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Site", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Area", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Location", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Scan", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Quantity", "rowspan" => "1", "colspan" => "2"],
                    ]);

                    $headTwo = collect([
                        ['name' => '1st Qty'],
                        ['name' => '1st Unit'],
                    ]);


                    $bodyOne = collect([
                        ["name" => "Product Name", "field_name" => "product_code", "class" => "left", "colspan" => "2"],
                        ["name" => "Product Name", "field_name" => "product_name", "class" => "left", "colspan" => "4"],
                        ["name" => "1st", "field_name" => "puom", "class" => "center", "colspan" => "1"],
                        ["name" => "2nd", "field_name" => "muom", "class" => "center", "colspan" => "1"],
                        ["name" => "3rd", "field_name" => "buom", "class" => "center", "colspan" => "1"]
                    ]);

                    $bodyOne = collect([
                        ["name" => "Ref. No.", "field_name" => "document_ref", "class" => "left", "colspan" => "1"],
                        ["name" => "Ref. No.", "field_name" => "po_number", "class" => "left", "colspan" => "1"],
                        ["name" => "Product Name", "field_name" => "product_code", "class" => "left", "colspan" => "1"],
                        ["name" => "Product Name", "field_name" => "product_name", "class" => "left", "colspan" => "1"],
                        ["name" => "Batch No.", "field_name" => "lot_no", "class" => "left", "colspan" => "1"],
                        ["name" => "Mfg Date", "field_name" => "mfg_date", "class" => "left", "colspan" => "1"],
                        ["name" => "Exp Date", "field_name" => "exp_date", "class" => "left", "colspan" => "1"],
                        ["name" => "Site", "field_name" => "site_name", "class" => "left", "colspan" => "1"],
                        ["name" => "Area", "field_name" => "area_name", "class" => "left", "colspan" => "1"],
                        ["name" => "Location", "field_name" => "location_code", "class" => "left", "colspan" => "1"],
                        ["name" => "Scan Ean", "field_name" => "manufactur_code", "class" => "left", "colspan" => "1"],
                        ["name" => "1st", "field_name" => "qty", "class" => "right", "colspan" => "1"],
                        ["name" => "1st", "field_name" => "puom", "class" => "center", "colspan" => "1"],
                    ]);

                    $columnCount = 12;
                }

                $listData = [];
                foreach ($stockList as $value) {
                    $listData[] = [
                        "po_number" => $value->po_number,
                        "product_code" => $value->product_code,
                        "product_name" => $value->product_name,
                        "document_ref" => $value->document_ref,
                        "lot_no" => $value->lot_no,
                        "mfg_date" => isset($value->mfg_date) ? \Carbon\Carbon::parse($value->mfg_date)->format("d-m-Y") : "",
                        "exp_date" => isset($value->exp_date) ? \Carbon\Carbon::parse($value->exp_date)->format("d-m-Y") : "",
                        "site_name" => $value->site_name,
                        "area_name" => $value->area_name,
                        "location_code" => $value->location_code,
                        "manufactur_code" => $value->manufactur_code,
                        "qty" => number_format($value->qty, 0, ",", "."),
                        "mqty" => number_format($value->mqty, 0, ",", "."),
                        "bqty" => number_format($value->bqty, 0, ",", "."),
                        "puom" => $value->puom,
                        "muom" => $value->muom,
                        "buom" => $value->buom
                    ];
                }

                $signature = "<tr><td>Prepare By:</td><td>Supervised By:</td><td>Checked By:</td><td>Racked By:</td></tr>";
                $signature .= "<tr><td>Date:</td><td>Date:</td><td>Date:</td><td>Date:</td></tr>";
                $signature .= "<tr><td>Signature:</td><td>Signature:</td><td>Signature:</td><td>Signature:</td></tr>";

                $data = [
                    "title" => "Put Away Report",
                    "css" => "landscape",
                    "headerOne" => $headerOne,
                    "headOne" => $headOne->toArray(),
                    "headTwo" => $headTwo->toArray(),
                    "bodyOne" => $bodyOne->toArray(),
                    "listData" => $listData,
                    "signature" => $signature,
                    "columnCount" => $columnCount,
                    'type' => $type
                ];

                return view("report", $data);
                break;
            case "putaway":
                $stockList = DB::table("iv_inbound_per_pallet")
                    ->where("inbound_id", "=", $id)
                    ->get();

                $stockList->map(function ($value) {
                    $value->mt_detail = DB::table('iv_inbound_detail')
                        ->select(
                            'po_number',
                            'document_ref',
                            'lot_no',
                            'puom',
                            'product_id',
                            'site_id',
                            'muom',
                            'buom',
                            'mfg_date',
                            'exp_date',
                        )
                        ->where('inbound_id', '=', $value->inbound_id)
                        ->where('product_code', '=', $value->product_code)
                        ->first();

                    $value->mt_product = DB::table('iv_product')
                        ->where('id', '=', $value->mt_detail->product_id)
                        ->first();

                    return $value;
                });

                $job = DB::table("iv_inbound_job as a")
                    ->select("a.*", "b.principal_name", "c.mode_name", "b.multi_level")
                    ->join("iv_principal as b", "a.principal_id", "b.id")
                    ->join("iv_mode as c", "a.mode_id", "c.id")
                    ->where("a.id", "=", $id)
                    ->first();

                $headerOne = "<tr><td>Principal Name</td><td>:</td><td>$job->principal_name</td></tr>";
                $headerOne .= "<tr><td>Job Number</td><td>:</td><td>$job->job_no</td></tr>";
                $headerOne .= "<tr><td>Job Date</td><td>:</td><td>" . date("d-m-Y", strtotime($job->job_date)) . "</td></tr>";

                $headOne = collect([
                    ["name" => "Reference No", "rowspan" => "2", "colspan" => "1"],
                    ["name" => "PO / DO No.", "rowspan" => "2", "colspan" => "1"],
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
                    ["name" => "Product Name", "field_name" => "product_code", "class" => "left", "colspan" => "2"],
                    ["name" => "Product Name", "field_name" => "product_name", "class" => "left", "colspan" => "4"],
                    ["name" => "1st", "field_name" => "puom", "class" => "center", "colspan" => "1"],
                    ["name" => "2nd", "field_name" => "muom", "class" => "center", "colspan" => "1"],
                    ["name" => "3rd", "field_name" => "buom", "class" => "center", "colspan" => "1"]
                ]);

                $bodyOne = collect([
                    ["name" => "Ref. No.", "field_name" => "document_ref", "class" => "left", "colspan" => "1"],
                    ["name" => "Ref. No.", "field_name" => "po_number", "class" => "left", "colspan" => "1"],
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

                $columnCount = 12;

                $listData = [];
                foreach ($stockList as $value) {
                    $listData[] = [
                        "po_number" => $value->mt_detail->po_number,
                        "product_code" => $value->product_code,
                        "product_name" => $value->mt_product->product_name,
                        "document_ref" => $value->mt_detail->document_ref,
                        "lot_no" => $value->mt_detail->lot_no,
                        "mfg_date" => isset($value->mt_detail->mfg_date) ? \Carbon\Carbon::parse($value->mt_detail->mfg_date)->format("d-m-Y") : "",
                        "exp_date" => isset($value->mt_detail->exp_date) ? \Carbon\Carbon::parse($value->mt_detail->exp_date)->format("d-m-Y") : "",
                        "site_name" => '',
                        "area_name" => '',
                        "location_code" => '',
                        "pqty" => number_format($value->qty_per_pallet, 0, ",", "."),
                        "mqty" => number_format($value->qty_per_pallet, 0, ",", "."),
                        "bqty" => number_format($value->qty_per_pallet, 0, ",", "."),
                        "puom" => $value->mt_detail->puom,
                        "muom" => $value->mt_detail->muom,
                        "buom" => $value->mt_detail->buom
                    ];
                }

                $signature = "<tr><td>Prepare By:</td><td>Supervised By:</td><td>Checked By:</td><td>Racked By:</td></tr>";
                $signature .= "<tr><td>Date:</td><td>Date:</td><td>Date:</td><td>Date:</td></tr>";
                $signature .= "<tr><td>Signature:</td><td>Signature:</td><td>Signature:</td><td>Signature:</td></tr>";

                $data = [
                    "title" => "Put Away List",
                    "css" => "landscape",
                    "headerOne" => $headerOne,
                    "headOne" => $headOne->toArray(),
                    "headTwo" => $headTwo->toArray(),
                    "bodyOne" => $bodyOne->toArray(),
                    "listData" => $listData,
                    "signature" => $signature,
                    "columnCount" => $columnCount,
                    'type' => $type
                ];

                return view("report", $data);
                break;
            case "draftPutaway":
                $stockList = DB::table("iv_inbound_per_pallet")
                    ->where("inbound_id", "=", $id)
                    ->get();

                $stockList->map(function ($value) {
                    $value->mt_detail = DB::table('iv_inbound_detail')
                        ->select(
                            'po_number',
                            'document_ref',
                            'lot_no',
                            'puom',
                            'product_id',
                            'site_id',
                            'muom',
                            'buom',
                            'mfg_date',
                            'exp_date',
                        )
                        ->where('inbound_id', '=', $value->inbound_id)
                        ->where('id', '=', $value->picking_id)
                        ->first();

                    $value->mt_product = DB::table('iv_product')
                        ->where('id', '=', $value->mt_detail->product_id)
                        ->first();

                    return $value;
                });

                $job = DB::table("iv_inbound_job as a")
                    ->select("a.*", "b.principal_name", "c.mode_name", "b.multi_level")
                    ->join("iv_principal as b", "a.principal_id", "b.id")
                    ->join("iv_mode as c", "a.mode_id", "c.id")
                    ->where("a.id", "=", $id)
                    ->first();

                $headerOne = "<tr><td>Principal Name</td><td>:</td><td>$job->principal_name</td></tr>";
                $headerOne .= "<tr><td>Job Number</td><td>:</td><td>$job->job_no</td></tr>";
                $headerOne .= "<tr><td>Job Date</td><td>:</td><td>" . date("d-m-Y", strtotime($job->job_date)) . "</td></tr>";

                $headOne = collect([
                    ["name" => "Reference No", "rowspan" => "2", "colspan" => "1"],
                    ["name" => "PO / DO No.", "rowspan" => "2", "colspan" => "1"],
                    ["name" => "SKU No.", "rowspan" => "2", "colspan" => "1"],
                    ["name" => "SKU Name", "rowspan" => "2", "colspan" => "1"],
                    ["name" => "Batch No.", "rowspan" => "2", "colspan" => "1"],
                    ["name" => "Mfg Date", "rowspan" => "2", "colspan" => "1"],
                    ["name" => "Exp Date", "rowspan" => "2", "colspan" => "1"],
                    // ["name" => "Site", "rowspan" => "2", "colspan" => "1"],
                    // ["name" => "Area", "rowspan" => "2", "colspan" => "1"],
                    ["name" => "Location", "rowspan" => "2", "colspan" => "1"],
                    ["name" => "Quantity", "rowspan" => "1", "colspan" => "2"],
                ]);

                $headTwo = collect([
                    ['name' => '1st Qty'],
                    ['name' => '1st Unit'],
                ]);


                $bodyOne = collect([
                    ["name" => "Product Name", "field_name" => "product_code", "class" => "left", "colspan" => "2"],
                    ["name" => "Product Name", "field_name" => "product_name", "class" => "left", "colspan" => "4"],
                    ["name" => "1st", "field_name" => "puom", "class" => "center", "colspan" => "1"],
                    ["name" => "2nd", "field_name" => "muom", "class" => "center", "colspan" => "1"],
                    ["name" => "3rd", "field_name" => "buom", "class" => "center", "colspan" => "1"]
                ]);

                $bodyOne = collect([
                    ["name" => "Ref. No.", "field_name" => "document_ref", "class" => "left", "colspan" => "1"],
                    ["name" => "Ref. No.", "field_name" => "po_number", "class" => "left", "colspan" => "1"],
                    ["name" => "Product Name", "field_name" => "product_code", "class" => "left", "colspan" => "1"],
                    ["name" => "Product Name", "field_name" => "product_name", "class" => "left", "colspan" => "1"],
                    ["name" => "Batch No.", "field_name" => "lot_no", "class" => "left", "colspan" => "1"],
                    ["name" => "Mfg Date", "field_name" => "mfg_date", "class" => "left", "colspan" => "1"],
                    ["name" => "Exp Date", "field_name" => "exp_date", "class" => "left", "colspan" => "1"],
                    // ["name" => "Site", "field_name" => "site_name", "class" => "left", "colspan" => "1"],
                    // ["name" => "Area", "field_name" => "area_name", "class" => "left", "colspan" => "1"],
                    ["name" => "Location", "field_name" => "location_code", "class" => "left", "colspan" => "1"],
                    ["name" => "1st", "field_name" => "pqty", "class" => "right", "colspan" => "1"],
                    ["name" => "1st", "field_name" => "puom", "class" => "center", "colspan" => "1"],
                ]);

                $columnCount = 12;

                $listData = [];
                foreach ($stockList as $value) {
                    $listData[] = [
                        "po_number" => $value->mt_detail->po_number,
                        "product_code" => $value->product_code,
                        "product_name" => $value->mt_product->product_name,
                        "document_ref" => $value->mt_detail->document_ref,
                        "lot_no" => $value->mt_detail->lot_no,
                        "mfg_date" => isset($value->mt_detail->mfg_date) ? \Carbon\Carbon::parse($value->mt_detail->mfg_date)->format("d-m-Y") : "",
                        "exp_date" => isset($value->mt_detail->exp_date) ? \Carbon\Carbon::parse($value->mt_detail->exp_date)->format("d-m-Y") : "",
                        // "site_name" => '',
                        // "area_name" => '',
                        "location_code" => $value->location_code,
                        "pqty" => number_format($value->qty_per_pallet, 0, ",", "."),
                        "mqty" => number_format($value->qty_per_pallet, 0, ",", "."),
                        "bqty" => number_format($value->qty_per_pallet, 0, ",", "."),
                        "puom" => $value->mt_detail->puom,
                        "muom" => $value->mt_detail->muom,
                        "buom" => $value->mt_detail->buom
                    ];
                }

                $signature = "<tr><td>Prepare By:</td><td>Supervised By:</td><td>Checked By:</td><td>Racked By:</td></tr>";
                $signature .= "<tr><td>Date:</td><td>Date:</td><td>Date:</td><td>Date:</td></tr>";
                $signature .= "<tr><td>Signature:</td><td>Signature:</td><td>Signature:</td><td>Signature:</td></tr>";

                $data = [
                    "title" => "Draft Put Away List",
                    "css" => "landscape",
                    "headerOne" => $headerOne,
                    "headOne" => $headOne->toArray(),
                    "headTwo" => $headTwo->toArray(),
                    "bodyOne" => $bodyOne->toArray(),
                    "listData" => $listData,
                    "signature" => $signature,
                    "columnCount" => $columnCount,
                    'type' => $type
                ];

                return view("report", $data);
                break;

            case "pallet":
                $view = DB::table("iv_inbound_job as a")
                    ->select(
                        "a.*",
                        "b.principal_name",
                        "b.multi_level"
                    )
                    ->join("iv_principal as b", "a.principal_id", "b.id")
                    ->where("a.id", $id)
                    ->first();
                $prod_hastag = substr_count($product_code, '-|');
                if ($prod_hastag > 0) {
                    $product_code =  str_replace('-|', '#', $product_code);
                } else {
                    $product_code =  $product_code;
                }
                $prod_slash = substr_count($product_code, '|');
                if ($prod_slash > 0) {
                    $product_code =  str_replace('|', '/', $product_code);
                } else {
                    $product_code =  $product_code;
                }

                $list = DB::table("iv_inbound_per_pallet")
                    ->where("picking_id", $picking_id)
                    ->where("product_code", $product_code)
                    ->get();

                $list->map(function ($value) {
                    $value->master_detail = DB::table("iv_inbound_detail")
                        ->where('id', $value->picking_id)
                        ->where("product_code", $value->product_code)
                        ->first();
                    return $value;
                });

                $list->map(function ($value) {
                    $value->master_product = DB::table("iv_product")
                        ->where('product_code', $value->master_detail->product_code)
                        ->first();
                    return $value;
                });

                $list->map(function ($value) {
                    $value->master_principal = DB::table("iv_principal")
                        ->where('id', $value->master_product->principal_id)
                        ->first();
                    return $value;
                });

                $list->map(function ($value) {
                    $value->master_job = DB::table("iv_inbound_job")
                        ->where('id', $value->master_detail->inbound_id)
                        ->first();
                    return $value;
                });

                $data = [
                    "view" => $view,
                    "list_data" => $list
                ];
                return view("transaction.inbound.pallet", $data);

                break;

            case "pallet_after":
                $view = DB::table("iv_inbound_job as a")
                    ->select(
                        "a.*",
                        "b.principal_name",
                        "b.multi_level"
                    )
                    ->join("iv_principal as b", "a.principal_id", "b.id")
                    ->where("a.id", $id)
                    ->first();


                $list = DB::table("iv_inbound_batch as a")
                    ->select("a.*", "b.product_name", "c.site_name", "d.area_name", "e.job_date")
                    ->join("iv_product as b", "a.product_id", "b.id")
                    ->leftjoin("iv_site as c", "a.site_id", "c.id")
                    ->leftjoin("iv_site_area as d", "a.area_id", "d.id")
                    ->join("iv_inbound_job as e", "a.inbound_id", "e.id")
                    ->where("a.inbound_id", $id)
                    ->get();

                $list->map(function ($value) {
                    $value->master_detail = DB::table("iv_inbound_detail")
                        ->where('inbound_id', $value->inbound_id)
                        ->where('product_code', $value->product_code)
                        ->where('lot_no', $value->lot_no)
                        ->first();
                    return $value;
                });

                $data = [
                    "view" => $view,
                    "list_data" => $list
                ];
                return view("transaction.inbound.pallet_after", $data);
                break;
            case "pallet_4":
                $view = DB::table("iv_inbound_job as a")
                    ->select(
                        "a.*",
                        "b.principal_name",
                        "b.multi_level"
                    )
                    ->join("iv_principal as b", "a.principal_id", "b.id")
                    ->where("a.id", $id)
                    ->first();

                $list = DB::table("iv_inbound_batch as a")
                    ->select("a.*", "b.product_name", "c.site_name", "d.area_name", "e.job_date")
                    ->join("iv_product as b", "a.product_id", "b.id")
                    ->leftjoin("iv_site as c", "a.site_id", "c.id")
                    ->leftjoin("iv_site_area as d", "a.area_id", "d.id")
                    ->join("iv_inbound_job as e", "a.inbound_id", "e.id")
                    ->where("a.inbound_id", $id)
                    ->get();

                $data = [
                    "view" => $view,
                    "list_data" => $list
                ];
                return view("transaction.inbound.pallet_4", $data);

                break;
            case "pallet_8":
                $view = DB::table("iv_inbound_job as a")
                    ->select(
                        "a.*",
                        "b.principal_name",
                        "b.multi_level"
                    )
                    ->join("iv_principal as b", "a.principal_id", "b.id")
                    ->where("a.id", $id)
                    ->first();

                $list = DB::table("iv_inbound_batch as a")
                    ->select("a.*", "b.product_name", "c.site_name", "d.area_name", "e.job_date")
                    ->join("iv_product as b", "a.product_id", "b.id")
                    ->leftjoin("iv_site as c", "a.site_id", "c.id")
                    ->leftjoin("iv_site_area as d", "a.area_id", "d.id")
                    ->join("iv_inbound_job as e", "a.inbound_id", "e.id")
                    ->where("a.inbound_id", $id)
                    ->get();

                $data = [
                    "view" => $view,
                    "list_data" => $list
                ];
                return view("transaction.inbound.pallet_8", $data);

                break;
            case "confirm":
                $stockList = DB::table("iv_inbound_batch as a")
                    ->select(
                        "a.inbound_id",
                        "a.product_code",
                        "b.product_name",
                        "a.po_number",
                        "a.lot_no",
                        "a.document_ref",
                        "a.mfg_date",
                        "a.exp_date",
                        "c.site_name",
                        "d.area_name",
                        "a.location_code",
                        "b.uppp",
                        "b.muppp",
                        "a.qty",
                        "a.mqty",
                        "a.bqty",
                        "b.puom",
                        "b.muom",
                        "b.buom",
                        "b.volume",
                        "b.gross_weight",
                        "b.length",
                        "b.width",
                        "b.height",
                        DB::raw('(b.length * b.width * b.height * a.qty) / 1000000 as total_volume')
                    )
                    ->join("iv_product as b", "a.product_id", "b.id")
                    ->leftjoin("iv_site as c", "a.site_id", "c.id")
                    ->leftjoin("iv_site_area as d", "a.area_id", "d.id")
                    ->where("a.inbound_id", "=", $id)
                    ->get();


                $job = DB::table("iv_inbound_job as a")
                    ->select("a.*", "b.principal_name", "c.mode_name", "b.multi_level")
                    ->join("iv_principal as b", "a.principal_id", "b.id")
                    ->join("iv_mode as c", "a.mode_id", "c.id")
                    ->where("a.id", "=", $id)
                    ->first();

                $headerOne = "<tr><td>Principal Name</td><td>:</td><td>$job->principal_name</td></tr>";
                $headerOne .= "<tr><td>Job Number</td><td>:</td><td>$job->job_no</td></tr>";
                $headerOne .= "<tr><td>Job Date</td><td>:</td><td>" . date("d-m-Y", strtotime($job->job_date)) . "</td></tr>";
                if ($job->multi_level == "Yes") {
                    $headOne = collect([
                        ["name" => "Reference No", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "PO / DO No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "SKU No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "SKU Name", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Batch No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Mfg Date", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Exp Date", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Site", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Area", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Location", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Quantity", "rowspan" => "1", "colspan" => "6"],
                        // ["name" => "Quantum", "rowspan" => "2", "colspan" => "1"],
                    ]);

                    $headTwo = collect([
                        ["name" => "1st Qty"],
                        ["name" => "1st Unit"],
                        ["name" => "2nd Qty"],
                        ["name" => "2nd Unit"],
                        ["name" => "3rd Qty"],
                        ["name" => "3rd Unit"],
                    ]);

                    $bodyOne = collect([
                        ["name" => "Ref. No", "field_name" => "document_ref", "class" => "left", "colspan" => "1"],
                        ["name" => "Ref. No", "field_name" => "po_number", "class" => "left", "colspan" => "1"],
                        ["name" => "Product Code", "field_name" => "product_code", "class" => "left", "colspan" => "1"],
                        ["name" => "Product Name", "field_name" => "product_name", "class" => "left", "colspan" => "1"],
                        ["name" => "Batch No.", "field_name" => "lot_no", "class" => "left", "colspan" => "1"],
                        ["name" => "Mfg Date", "field_name" => "mfg_date", "class" => "left", "colspan" => "1"],
                        ["name" => "Exp Date", "field_name" => "exp_date", "class" => "left", "colspan" => "1"],
                        ["name" => "Batch No.", "field_name" => "site_name", "class" => "left", "colspan" => "1"],
                        ["name" => "Mfg Date", "field_name" => "area_name", "class" => "left", "colspan" => "1"],
                        ["name" => "Exp Date", "field_name" => "location_code", "class" => "left", "colspan" => "1"],
                        ["name" => "1st", "field_name" => "qty", "class" => "right", "colspan" => "1"],
                        ["name" => "1st", "field_name" => "puom", "class" => "center", "colspan" => "1"],
                        ["name" => "2nd", "field_name" => "mqty", "class" => "right", "colspan" => "1"],
                        ["name" => "2nd", "field_name" => "muom", "class" => "center", "colspan" => "1"],
                        ["name" => "3rd", "field_name" => "bqty", "class" => "right", "colspan" => "1"],
                        ["name" => "3rd", "field_name" => "buom", "class" => "center", "colspan" => "1"],
                        // ["name" => "Quantum", "field_name" => "total", "class" => "left"],
                    ]);

                    $columnCount = 17;
                } else {
                    $headOne = collect([
                        ["name" => "Reference No", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "PO / DO No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "SKU No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "SKU Name", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Batch No.", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Mfg Date", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Exp Date", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Site", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Area", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Location", "rowspan" => "2", "colspan" => "1"],
                        ["name" => "Quantity", "rowspan" => "1", "colspan" => "2"],
                        ["name" => "Measurements", "rowspan" => "1", "colspan" => "4"],
                    ]);

                    $headTwo = collect([
                        ["name" => "1st Qty"],
                        ["name" => "1st Unit"],
                        ["name" => "Length"],
                        ["name" => "Width"],
                        ["name" => "Height"],
                        ["name" => "Total Volume"]
                    ]);

                    $bodyOne = collect([
                        ["name" => "Ref. No", "field_name" => "document_ref", "class" => "left", "colspan" => "1"],
                        ["name" => "Ref. No", "field_name" => "po_number", "class" => "left", "colspan" => "1"],
                        ["name" => "Product Code", "field_name" => "product_code", "class" => "left", "colspan" => "1"],
                        ["name" => "Product Name", "field_name" => "product_name", "class" => "left", "colspan" => "1"],
                        ["name" => "Batch No.", "field_name" => "lot_no", "class" => "left", "colspan" => "1"],
                        ["name" => "Mfg Date", "field_name" => "mfg_date", "class" => "left", "colspan" => "1"],
                        ["name" => "Exp Date", "field_name" => "exp_date", "class" => "left", "colspan" => "1"],
                        ["name" => "Batch No.", "field_name" => "site_name", "class" => "left", "colspan" => "1"],
                        ["name" => "Mfg Date", "field_name" => "area_name", "class" => "left", "colspan" => "1"],
                        ["name" => "Exp Date", "field_name" => "location_code", "class" => "left", "colspan" => "1"],
                        ["name" => "1st", "field_name" => "qty", "class" => "right", "colspan" => "1"],
                        ["name" => "1st", "field_name" => "puom", "class" => "center", "colspan" => "1"],
                        ["name" => "Length", "field_name" => "length", "class" => "right", "colspan" => "1"],
                        ["name" => "Width", "field_name" => "width", "class" => "right", "colspan" => "1"],
                        ["name" => "Height", "field_name" => "height", "class" => "right", "colspan" => "1"],
                        ["name" => "Total Volume", "field_name" => "total_volume", "class" => "right", "colspan" => "1"]
                    ]);

                    $columnCount = 16;
                }

                $listData = [];
                foreach ($stockList as $value) {
                    $listData[] = [
                        "po_number" => $value->po_number,
                        "product_code" => $value->product_code,
                        "product_name" => $value->product_name,
                        "document_ref" => $value->document_ref,
                        "lot_no" => $value->lot_no,
                        "mfg_date" => isset($value->mfg_date) ? \Carbon\Carbon::parse($value->mfg_date)->format("d-m-Y") : "",
                        "exp_date" => isset($value->exp_date) ? \Carbon\Carbon::parse($value->exp_date)->format("d-m-Y") : "",
                        "site_name" => $value->site_name,
                        "area_name" => $value->area_name,
                        "location_code" => $value->location_code,
                        "qty" => number_format($value->qty, 0, ",", "."),
                        "mqty" => number_format($value->mqty, 0, ",", "."),
                        "bqty" => number_format($value->bqty, 0, ",", "."),
                        "puom" => $value->puom,
                        "muom" => $value->muom,
                        "buom" => $value->buom,
                        "length" => number_format($value->length, 2, ",", "."),
                        "width" => number_format($value->width, 2, ",", "."),
                        "height" => number_format($value->height, 2, ",", "."),
                        "total_volume" => number_format($value->total_volume, 3, ",", "."),
                    ];
                }

                $data = [
                    "title" => "Inbound Confirmation Report",
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
            case "grn-summary":
                $detail = DB::table("iv_inbound_detail as a")
                    ->select(
                        "a.product_code",
                        "b.product_name",
                        "a.po_number",
                        "a.lot_no",
                        "a.document_ref",
                        "a.mfg_date",
                        "a.exp_date",
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
                        "a.discrepancy_pqty",
                        "a.discrepancy_mqty",
                        "a.discrepancy_bqty",
                        "a.discrepancy_qty",
                        "b.puom",
                        "b.muom",
                        "b.buom",
                        "b.volume",
                        "b.gross_weight",
                        "a.product_status",
                        DB::raw("CASE WHEN a.qty = a.actual_qty THEN 'Full' WHEN a.qty > a.actual_qty THEN 'Short' ELSE 'Excess' END as received")
                    )
                    ->join("iv_product as b", "a.product_id", "b.id")
                    ->where("a.inbound_id", "=", $id)
                    ->where("a.received_flag", "=", "Yes")
                    ->get();

                $job = DB::table("iv_inbound_job as a")
                    ->select("a.*", "b.principal_name", "c.mode_name", "b.multi_level")
                    ->join("iv_principal as b", "a.principal_id", "b.id")
                    ->join("iv_mode as c", "a.mode_id", "c.id")
                    ->where("a.id", "=", $id)
                    ->first();

                $data = [
                    "job_view" => $job,
                    "detail_list" => $detail
                ];

                return view("transaction.inbound.grn_summary", $data);
                break;
            case "confirm-quantum":
                $data = DB::table("iv_inbound_batch as a")
                    ->select(
                        "a.inbound_id",
                        "a.product_code",
                        "b.product_name",
                        "a.po_number",
                        "a.lot_no",
                        "a.document_ref",
                        "a.mfg_date",
                        "a.exp_date",
                        "c.site_name",
                        "d.area_name",
                        "a.location_code",
                        "b.uppp",
                        "b.muppp",
                        "a.qty",
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
                    ->where("a.inbound_id", "=", $id)
                    ->get();
                $quantum = 0;
                foreach ($data as $value) {
                    $quantum += $value->qty * $value->muppp + $value->mqty;
                }

                $lot_no = $data->groupBy('lot_no');

                $job = DB::table("iv_inbound_job as a")
                    ->select("a.*", "b.principal_name", "c.mode_name", "b.multi_level")
                    ->join("iv_principal as b", "a.principal_id", "b.id")
                    ->join("iv_mode as c", "a.mode_id", "c.id")
                    ->where("a.id", "=", $id)
                    ->first();

                $headerOne = "<tr><td>Principal Name</td><td>:</td><td>$job->principal_name</td></tr>";
                $headerOne .= "<tr><td>Job Number</td><td>:</td><td>$job->job_no</td></tr>";
                $headerOne .= "<tr><td>Job Date</td><td>:</td><td>" . date("d-M-Y", strtotime($job->job_date)) . "</td></tr>";

                $signature = "<tr><td>Prepare By:</td><td>Supervised By:</td><td>Checked By:</td><td>Racked By:</td></tr>";
                $signature .= "<tr><td>Date:</td><td>Date:</td><td>Date:</td><td>Date:</td></tr>";
                $signature .= "<tr><td>Signature:</td><td>Signature:</td><td>Signature:</td><td>Signature:</td></tr>";

                $title = 'Inbound Confirmation Report';

                return view("transaction.inbound.report_quantum", compact('data', 'signature', 'title', 'headerOne', 'lot_no', 'quantum'));

                break;
            default:
                break;
        }
    }

    public function allPallet(Request $request)
    {
        $view = DB::table("iv_inbound_job as a")
            ->select(
                "a.*",
                "b.principal_name",
                "b.multi_level"
            )
            ->join("iv_principal as b", "a.principal_id", "b.id")
            ->where("a.id", $request->job_id)
            ->first();
        $list = DB::table("iv_inbound_per_pallet")
            ->where("inbound_id", $view->id)
            ->whereIn("product_code", $request->list_sku)
            ->get();

        $list->map(function ($value) {
            $value->master_detail = DB::table("iv_inbound_detail")
                ->where('id', $value->picking_id)
                ->where("product_code", $value->product_code)
                ->first();
            return $value;
        });

        $list->map(function ($value) {
            $value->master_product = DB::table("iv_product")
                ->where('product_code', $value->master_detail->product_code)
                ->first();
            return $value;
        });

        $list->map(function ($value) {
            $value->master_principal = DB::table("iv_principal")
                ->where('id', $value->master_product->principal_id)
                ->first();
            return $value;
        });

        $list->map(function ($value) {
            $value->master_job = DB::table("iv_inbound_job")
                ->where('id', $value->master_detail->inbound_id)
                ->first();
            return $value;
        });

        $data = [
            "view" => $view,
            "list_data" => $list
        ];
        return view("transaction.inbound.pallet", $data);
    }

    public function export(Request $request)
    {
        $time = \Carbon\Carbon::now()->format("dmy.His");

        return Excel::download(new InboundExport($request->inbound_id), "inbound-$time.xlsx");
    }
}
