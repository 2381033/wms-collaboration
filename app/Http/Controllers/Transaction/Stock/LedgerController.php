<?php

namespace App\Http\Controllers\Transaction\Stock;

use App\Exports\StockLedgerReportExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LedgerController extends Controller
{
    public function index()
    {
        return view("report.stock-report.index");
    }

    public function report(Request $request)
    {
        $user = Auth::user();

        $company_id = Auth::user()->company_id;
        $principal_id = $request->principal_id;
        $branch_id = $request->branch_id;
        $reportType = $request->reportType;
        $GroupOn = $request->GroupOn;
        $sortOrder = $request->sortOrder;

        $principal = \App\Models\Master\Principal::find($principal_id);

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

        $area_id = "%";

        $site_list = [];
        if (!empty($request->site_id) && isset($request->site_id)) {
            $site_list[] = $request->site_id;
        } else {
            foreach ($user->site->all() as $value) {
                $site_list[] = $value->id;
            }
        }

        if (!empty($request->area_id) && isset($request->area_id)) {
            $area_id = $request->area_id;
        }

        if (!empty($request->location_code_from) && !empty($request->location_code_to)) {
            $location_from = $request->location_code_from;
            $location_to = $request->location_code_to;
        } else {
            if (!empty($request->location_code_from) && empty($request->location_code_to)) {
                $location_from = $request->location_code_from;
                $location_to = "zzzzzzzzzzzzzzz";
            } else if (empty($request->brand_code_from) && !empty($request->location_code_to)) {
                $location_from = "";
                $location_to = $request->location_code_to;
            } else {
                $location_from = "";
                $location_to = "zzzzzzzzzzzzzzz";
            }
        }

        $exp_date_from = "1990-01-01";
        $exp_date_to = "2999-12-31";
        if (!empty($request->exp_date_from) && !empty($request->exp_date_to)) {
            $exp_date_from = $request->exp_date_from;
            $exp_date_to = $request->exp_date_to;
        } else if (!empty($request->exp_date_from) && empty($request->exp_date_to)) {
            $exp_date_from = $request->exp_date_from;
            $exp_date_to = "2999-12-31";
        }

        $exp_date_from = \Carbon\Carbon::parse($exp_date_from)->format("Y-m-d");
        $exp_date_to = \Carbon\Carbon::parse($exp_date_to)->format("Y-m-d");
        if (is_numeric($product_from)) {
            $product_from = (int)$product_from;
        } else {
            $product_from = $product_from;
        }
        if (is_numeric($product_to)) {
            $product_to = (int)$product_to;
        } else {
            $product_to = $product_to;
        }

        if ($reportType == "summary") {
            $stok = DB::table('iv_stock_transaction as a')
                ->select(
                    'product_id',
                    DB::raw("sum(CASE WHEN a.job_type IN ('IMP', 'TFRI', 'ADJ+') THEN a.qty ELSE 0 END ) as qty_received"),
                    DB::raw("sum(CASE WHEN a.job_type IN ('EXP', 'TFRO', 'ADJ-') THEN a.qty ELSE 0 END ) as qty_issue")
                )
                ->where('company_id', $company_id)
                ->where('principal_id', $principal_id)
                ->where('branch_id', $branch_id)
                ->whereBetween('product_code', [$product_from, $product_to])
                ->whereIn(DB::raw("COALESCE(a.site_id, 0)"), $site_list)
                ->where(DB::raw("COALESCE(a.area_id, 0)"), "LIKE", $area_id)
                ->whereBetween(DB::raw("COALESCE(a.location_code, '')"), [$location_from, $location_to])
                ->whereBetween('a.job_date', [date('1990-01-01'), date('2999-12-31')])
                ->groupBy('product_code')
                ->orderBy("product_code", $sortOrder)
                ->orderBy("site_id", $sortOrder)
                ->orderBy("area_id", $sortOrder)
                ->orderBy("location_code", $sortOrder)
                ->get();

            switch ($GroupOn) {
                case "product":
                    $stockList = DB::table("iv_stock_ledger as a")
                        ->select(
                            "a.id",
                            "a.product_code",
                            "a.product_id",
                            "b.product_name",
                            "b.uppp",
                            "b.muppp",
                            "b.volume",
                            "b.gross_weight",
                            "b.puom",
                            "b.muom",
                            "b.buom",
                            DB::raw("sum(a.qtys) as qtys"),
                            DB::raw("sum(a.qtya) as qtya"),
                            DB::raw("sum(a.qtyp) as qtyp"),
                            DB::raw("CASE WHEN a.status = 'B' THEN 'BAD' ELSE 'GOODS' END as status_code")
                        )
                        ->join("iv_product as b", "a.product_id", "b.id")
                        ->join("iv_product_group as e", "b.group_id", "e.id")
                        ->join("iv_product_brand as f", "b.brand_id", "f.id")
                        ->leftjoin("iv_location as g", "a.location_id", "g.id")
                        ->where("a.company_id", $company_id)
                        ->where("a.principal_id", $principal_id)
                        ->where("a.branch_id", $branch_id)
                        ->where("a.qtys", ">", 0)
                        ->whereBetween("e.group_code", [$group_from, $group_to])
                        ->whereBetween("f.brand_code", [$brand_from, $brand_to])
                        ->whereBetween("b.product_code", [$product_from, $product_to])
                        ->whereIn(DB::raw("COALESCE(a.site_id, 0)"), $site_list)
                        ->where(DB::raw("COALESCE(a.area_id, 0)"), "LIKE", $area_id)
                        ->whereBetween(DB::raw("COALESCE(a.location_code, '')"), [$location_from, $location_to])
                        ->whereBetween(DB::raw("COALESCE(a.exp_date, now())"), [$exp_date_from, $exp_date_to])
                        ->groupBy("a.product_id")
                        ->orderBy("b.product_code", $sortOrder)
                        ->orderBy("a.site_id", $sortOrder)
                        ->orderBy("a.area_id", $sortOrder)
                        ->orderBy("a.location_code", $sortOrder)
                        ->get();
                    $arr_product = $stockList->pluck('product_id')->toArray();

                    $stok = $stok->whereIn('product_id', $arr_product);

                    $total = [];
                    foreach ($stok as $value) {
                        $total[] = $value->qty_received - $value->qty_issue;
                    }

                    if ($principal->multi_level == "Yes") {
                        $headOne = collect([
                            ["name" => "SKU No.", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "SKU Name", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "SOH", "rowspan" => "1", "colspan" => "3"],
                            ["name" => "SOB", "rowspan" => "1", "colspan" => "3"],
                            ["name" => "SOA", "rowspan" => "1", "colspan" => "3"],
                            ["name" => "Unit", "rowspan" => "1", "colspan" => "3"],
                            ["name" => "Status", "rowspan" => "2", "colspan" => "1"],
                        ]);

                        $headTwo = collect([
                            ["name" => "1st"],
                            ["name" => "2nd"],
                            ["name" => "3rd"],
                            ["name" => "1st"],
                            ["name" => "2nd"],
                            ["name" => "3rd"],
                            ["name" => "1st"],
                            ["name" => "2nd"],
                            ["name" => "3rd"],
                            ["name" => "1st"],
                            ["name" => "2nd"],
                            ["name" => "3rd"]
                        ]);

                        $bodyOne = collect([
                            ["name" => "SKU No.", "field_name" => "product_code", "class" => "left"],
                            ["name" => "SKU Name", "field_name" => "product_name", "class" => "left"],
                            ["name" => "1st", "field_name" => "pqtys", "class" => "right"],
                            ["name" => "2nd", "field_name" => "mqtys", "class" => "right"],
                            ["name" => "3rd", "field_name" => "bqtys", "class" => "right"],
                            ["name" => "1st", "field_name" => "pqtyp", "class" => "right"],
                            ["name" => "2nd", "field_name" => "mqtyp", "class" => "right"],
                            ["name" => "3rd", "field_name" => "bqtyp", "class" => "right"],
                            ["name" => "1st", "field_name" => "pqtya", "class" => "right"],
                            ["name" => "2nd", "field_name" => "mqtya", "class" => "right"],
                            ["name" => "3rd", "field_name" => "bqtya", "class" => "right"],
                            ["name" => "1st", "field_name" => "puom", "class" => "center"],
                            ["name" => "2nd", "field_name" => "muom", "class" => "center"],
                            ["name" => "3rd", "field_name" => "buom", "class" => "center"],
                            ["name" => "status", "field_name" => "status_code", "class" => "center"]
                        ]);

                        $columnCount = 15;
                    } else {
                        $headOne = collect([
                            ["name" => "SKU No.", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "SKU Name", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "SOH", "rowspan" => "1", "colspan" => "1"],
                            ["name" => "SOB", "rowspan" => "1", "colspan" => "1"],
                            ["name" => "SOA", "rowspan" => "1", "colspan" => "1"],
                            ["name" => "Unit", "rowspan" => "1", "colspan" => "1"],
                            ["name" => "Status", "rowspan" => "2", "colspan" => "1"]
                        ]);

                        $headTwo = collect([
                            ["name" => "1st"],
                            ["name" => "1st"],
                            ["name" => "1st"],
                            ["name" => "1st"],
                        ]);

                        $bodyOne = collect([
                            ["name" => "SKU No.", "field_name" => "product_code", "class" => "left"],
                            ["name" => "SKU Name", "field_name" => "product_name", "class" => "left"],
                            ["name" => "1st", "field_name" => "pqtys", "class" => "right"],
                            ["name" => "1st", "field_name" => "pqtyp", "class" => "right"],
                            ["name" => "1st", "field_name" => "pqtya", "class" => "right"],
                            ["name" => "1st", "field_name" => "puom", "class" => "center"],
                            ["name" => "status", "field_name" => "status_code", "class" => "center"]
                        ]);

                        $columnCount = 7;
                    }

                    $listData = [];
                    foreach ($stockList as $key => $value) {
                        // dd( $value->qtys != $total[$key] ? $value->qtys . '-'. $total[$key]  : 'sama');
                        $listData[] = [
                            "product_code" => $value->product_code,
                            "product_name" => $value->product_name,
                            "puom" => $value->puom,
                            "muom" => $value->muom,
                            "buom" => $value->buom,
                            "pqtys" => $value->qtys != $total[$key] ? $value->qtys : $total[$key],
                            "mqtys" => (($value->qtys % $value->uppp) - (($value->qtys % $value->uppp) % $value->muppp)) / $value->muppp,
                            "bqtys" => $value->qtys % $value->uppp % $value->muppp,
                            "pqtyp" => ($value->qtyp  - ($value->qtyp % $value->uppp)) / $value->uppp,
                            "mqtyp" => (($value->qtyp % $value->uppp) - (($value->qtyp % $value->uppp) % $value->muppp)) / $value->muppp,
                            "bqtyp" => $value->qtyp % $value->uppp % $value->muppp,
                            "pqtya" => $value->qtys != $total[$key] ? abs($value->qtys - ($value->qtyp  - ($value->qtyp % $value->uppp)) / $value->uppp) :  abs($total[$key] - ($value->qtyp  - ($value->qtyp % $value->uppp)) / $value->uppp),
                            "mqtya" => (($value->qtya % $value->uppp) - (($value->qtya % $value->uppp) % $value->muppp)) / $value->muppp,
                            "bqtya" => $value->qtya % $value->uppp % $value->muppp,
                            "status_code" => 'GOODS',
                        ];
                    }

                    $data = [
                        "title" => "Product Wise - Stock Report ( Summary )",
                        "css" => "portrait",
                        "headOne" => $headOne->toArray(),
                        "headTwo" => $headTwo->toArray(),
                        "bodyOne" => $bodyOne->toArray(),
                        "listData" => $listData,
                        "columnCount" => $columnCount
                    ];

                    return view("report", $data);
                    break;
                case "product-lot":
                    $stockList = DB::table("iv_stock_ledger as a")
                        ->select(
                            "a.product_code",
                            "b.product_name",
                            "a.lot_no",
                            "b.uppp",
                            "b.muppp",
                            "b.volume",
                            "b.gross_weight",
                            "b.puom",
                            "b.muom",
                            "b.buom",
                            DB::raw("sum(a.qtys) as qtys"),
                            DB::raw("sum(a.qtya) as qtya"),
                            DB::raw("sum(a.qtyp) as qtyp")
                        )
                        ->join("iv_product as b", "a.product_id", "b.id")
                        ->join("iv_product_group as e", "b.group_id", "e.id")
                        ->join("iv_product_brand as f", "b.brand_id", "f.id")
                        ->where("a.company_id", $company_id)
                        ->where("a.principal_id", $principal_id)
                        ->where("a.branch_id", $branch_id)
                        ->where("a.qtys", ">", 0)
                        ->whereBetween("e.group_code", [$group_from, $group_to])
                        ->whereBetween("f.brand_code", [$brand_from, $brand_to])
                        ->whereBetween("b.product_code", [$product_from, $product_to])
                        ->whereIn(DB::raw("COALESCE(a.site_id, 0)"), $site_list)
                        ->where(DB::raw("COALESCE(a.area_id, 0)"), "LIKE", $area_id)
                        ->whereBetween(DB::raw("COALESCE(a.location_code, '')"), [$location_from, $location_to])
                        ->whereBetween(DB::raw("COALESCE(a.exp_date, now())"), [$exp_date_from, $exp_date_to])
                        ->groupBy("a.product_code", "b.product_name", "a.lot_no", "b.uppp", "b.muppp", "b.volume", "b.gross_weight", "b.puom", "b.muom", "b.buom")
                        ->orderBy("a.lot_no", $sortOrder)
                        ->orderBy("b.product_name", $sortOrder)
                        ->orderBy("a.location_code", $sortOrder)
                        ->get();

                    if ($principal->multi_level == "Yes") {
                        $headOne = collect([
                            ["name" => "SKU No.", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "SKU Name", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Batch No", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "SOA", "rowspan" => "1", "colspan" => "3"],
                            ["name" => "SOB", "rowspan" => "1", "colspan" => "3"],
                            ["name" => "SOA", "rowspan" => "1", "colspan" => "3"],
                            ["name" => "Unit", "rowspan" => "1", "colspan" => "3"]
                        ]);

                        $headTwo = collect([
                            ["name" => "1st"],
                            ["name" => "2nd"],
                            ["name" => "3rd"],
                            ["name" => "1st"],
                            ["name" => "2nd"],
                            ["name" => "3rd"],
                            ["name" => "1st"],
                            ["name" => "2nd"],
                            ["name" => "3rd"],
                            ["name" => "1st"],
                            ["name" => "2nd"],
                            ["name" => "3rd"]
                        ]);

                        $bodyOne = collect([
                            ["name" => "SKU No.", "field_name" => "product_code", "class" => "left"],
                            ["name" => "SKU Name", "field_name" => "product_name", "class" => "left"],
                            ["name" => "Batch", "field_name" => "lot_no", "class" => "center"],
                            ["name" => "1st", "field_name" => "pqtys", "class" => "right"],
                            ["name" => "2nd", "field_name" => "mqtys", "class" => "right"],
                            ["name" => "3rd", "field_name" => "bqtys", "class" => "right"],
                            ["name" => "1st", "field_name" => "pqtyp", "class" => "right"],
                            ["name" => "2nd", "field_name" => "mqtyp", "class" => "right"],
                            ["name" => "3rd", "field_name" => "bqtyp", "class" => "right"],
                            ["name" => "1st", "field_name" => "pqtya", "class" => "right"],
                            ["name" => "2nd", "field_name" => "mqtya", "class" => "right"],
                            ["name" => "3rd", "field_name" => "bqtya", "class" => "right"],
                            ["name" => "1st", "field_name" => "puom", "class" => "center"],
                            ["name" => "2nd", "field_name" => "muom", "class" => "center"],
                            ["name" => "3rd", "field_name" => "buom", "class" => "center"]
                        ]);

                        $columnCount = 15;
                    } else {
                        $headOne = collect([
                            ["name" => "SKU No.", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "SKU Name", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Batch No", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "SOH", "rowspan" => "1", "colspan" => "1"],
                            ["name" => "SOB", "rowspan" => "1", "colspan" => "1"],
                            ["name" => "SOA", "rowspan" => "1", "colspan" => "1"],
                            ["name" => "Unit", "rowspan" => "1", "colspan" => "1"]
                        ]);

                        $headTwo = collect([
                            ["name" => "1st"],
                            ["name" => "1st"],
                            ["name" => "1st"],
                            ["name" => "1st"],
                        ]);

                        $bodyOne = collect([
                            ["name" => "SKU No.", "field_name" => "product_code", "class" => "left"],
                            ["name" => "SKU Name", "field_name" => "product_name", "class" => "left"],
                            ["name" => "Batch", "field_name" => "lot_no", "class" => "center"],
                            ["name" => "1st", "field_name" => "pqtys", "class" => "right"],
                            ["name" => "1st", "field_name" => "pqtyp", "class" => "right"],
                            ["name" => "1st", "field_name" => "pqtya", "class" => "right"],
                            ["name" => "1st", "field_name" => "puom", "class" => "center"],
                        ]);

                        $columnCount = 7;
                    }

                    $listData = [];
                    foreach ($stockList as $value) {
                        $listData[] = [
                            "product_code" => $value->product_code,
                            "product_name" => $value->product_name,
                            "lot_no" => $value->lot_no,
                            "puom" => $value->puom,
                            "muom" => $value->muom,
                            "buom" => $value->buom,
                            "pqtys" => ($value->qtys  - ($value->qtys % $value->uppp)) / $value->uppp,
                            "mqtys" => (($value->qtys % $value->uppp) - (($value->qtys % $value->uppp) % $value->muppp)) / $value->muppp,
                            "bqtys" => $value->qtys % $value->uppp % $value->muppp,
                            "pqtyp" => ($value->qtyp  - ($value->qtyp % $value->uppp)) / $value->uppp,
                            "mqtyp" => (($value->qtyp % $value->uppp) - (($value->qtyp % $value->uppp) % $value->muppp)) / $value->muppp,
                            "bqtyp" => $value->qtyp % $value->uppp % $value->muppp,
                            "pqtya" => ($value->qtya  - ($value->qtya % $value->uppp)) / $value->uppp,
                            "mqtya" => (($value->qtya % $value->uppp) - (($value->qtya % $value->uppp) % $value->muppp)) / $value->muppp,
                            "bqtya" => $value->qtya % $value->uppp % $value->muppp
                        ];
                    }

                    $data = [
                        "title" => "Batch Number Wise - Stock Report ( Summary )",
                        "css" => "portrait",
                        "headOne" => $headOne->toArray(),
                        "headTwo" => $headTwo->toArray(),
                        "bodyOne" => $bodyOne->toArray(),
                        "listData" => $listData,
                        "columnCount" => $columnCount
                    ];

                    return view("report", $data);
                    break;
                case "product-doc":
                    $stockList = DB::table("iv_stock_ledger as a")
                        ->select(
                            "a.product_code",
                            "b.product_name",
                            "a.document_ref",
                            "b.uppp",
                            "b.muppp",
                            "b.volume",
                            "b.gross_weight",
                            "b.puom",
                            "b.muom",
                            "b.buom",
                            DB::raw("sum(a.qtys) as qtys"),
                            DB::raw("sum(a.qtya) as qtya"),
                            DB::raw("sum(a.qtyp) as qtyp")
                        )
                        ->join("iv_product as b", "a.product_id", "b.id")
                        ->join("iv_product_group as e", "b.group_id", "e.id")
                        ->join("iv_product_brand as f", "b.brand_id", "f.id")
                        ->where("a.company_id", $company_id)
                        ->where("a.principal_id", $principal_id)
                        ->where("a.branch_id", $branch_id)
                        ->where("a.qtys", ">", 0)
                        ->whereBetween("e.group_code", [$group_from, $group_to])
                        ->whereBetween("f.brand_code", [$brand_from, $brand_to])
                        ->whereBetween("b.product_code", [$product_from, $product_to])
                        ->whereIn(DB::raw("COALESCE(a.site_id, 0)"), $site_list)
                        ->where(DB::raw("COALESCE(a.area_id, 0)"), "LIKE", $area_id)
                        ->whereBetween(DB::raw("COALESCE(a.location_code, '')"), [$location_from, $location_to])
                        ->whereBetween(DB::raw("COALESCE(a.exp_date, now())"), [$exp_date_from, $exp_date_to])
                        ->groupBy("a.product_code", "b.product_name", "a.document_ref", "b.uppp", "b.muppp", "b.volume", "b.gross_weight", "b.puom", "b.muom", "b.buom")
                        ->orderBy("a.document_ref", $sortOrder)
                        ->orderBy("b.product_name", $sortOrder)
                        ->orderBy("a.location_code", $sortOrder)
                        ->get();

                    if ($principal->multi_level == "Yes") {
                        $headOne = collect([
                            ["name" => "SKU No.", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "SKU Name", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Document Ref", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "SOH", "rowspan" => "1", "colspan" => "3"],
                            ["name" => "SOB", "rowspan" => "1", "colspan" => "3"],
                            ["name" => "SOA", "rowspan" => "1", "colspan" => "3"],
                            ["name" => "Unit", "rowspan" => "1", "colspan" => "3"]
                        ]);

                        $headTwo = collect([
                            ["name" => "1st"],
                            ["name" => "2nd"],
                            ["name" => "3rd"],
                            ["name" => "1st"],
                            ["name" => "2nd"],
                            ["name" => "3rd"],
                            ["name" => "1st"],
                            ["name" => "2nd"],
                            ["name" => "3rd"],
                            ["name" => "1st"],
                            ["name" => "2nd"],
                            ["name" => "3rd"]
                        ]);

                        $bodyOne = collect([
                            ["name" => "SKU No.", "field_name" => "product_code", "class" => "left"],
                            ["name" => "SKU Name", "field_name" => "product_name", "class" => "left"],
                            ["name" => "Batch", "field_name" => "document_ref", "class" => "center"],
                            ["name" => "1st", "field_name" => "pqtys", "class" => "right"],
                            ["name" => "2nd", "field_name" => "mqtys", "class" => "right"],
                            ["name" => "3rd", "field_name" => "bqtys", "class" => "right"],
                            ["name" => "1st", "field_name" => "pqtyp", "class" => "right"],
                            ["name" => "2nd", "field_name" => "mqtyp", "class" => "right"],
                            ["name" => "3rd", "field_name" => "bqtyp", "class" => "right"],
                            ["name" => "1st", "field_name" => "pqtya", "class" => "right"],
                            ["name" => "2nd", "field_name" => "mqtya", "class" => "right"],
                            ["name" => "3rd", "field_name" => "bqtya", "class" => "right"],
                            ["name" => "1st", "field_name" => "puom", "class" => "center"],
                            ["name" => "2nd", "field_name" => "muom", "class" => "center"],
                            ["name" => "3rd", "field_name" => "buom", "class" => "center"]
                        ]);

                        $columnCount = 15;
                    } else {
                        $headOne = collect([
                            ["name" => "SKU No.", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "SKU Name", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Document Ref", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "SOH", "rowspan" => "1", "colspan" => "1"],
                            ["name" => "SOB", "rowspan" => "1", "colspan" => "1"],
                            ["name" => "SOA", "rowspan" => "1", "colspan" => "1"],
                            ["name" => "Unit", "rowspan" => "1", "colspan" => "1"]
                        ]);

                        $headTwo = collect([
                            ["name" => "1st"],
                            ["name" => "1st"],
                            ["name" => "1st"],
                            ["name" => "1st"],
                        ]);

                        $bodyOne = collect([
                            ["name" => "SKU No.", "field_name" => "product_code", "class" => "left"],
                            ["name" => "SKU Name", "field_name" => "product_name", "class" => "left"],
                            ["name" => "Batch", "field_name" => "document_ref", "class" => "center"],
                            ["name" => "1st", "field_name" => "pqtys", "class" => "right"],
                            ["name" => "1st", "field_name" => "pqtyp", "class" => "right"],
                            ["name" => "1st", "field_name" => "pqtya", "class" => "right"],
                            ["name" => "1st", "field_name" => "puom", "class" => "center"],
                        ]);

                        $columnCount = 7;
                    }

                    $listData = [];
                    foreach ($stockList as $value) {
                        $listData[] = [
                            "product_code" => $value->product_code,
                            "product_name" => $value->product_name,
                            "document_ref" => $value->document_ref,
                            "puom" => $value->puom,
                            "muom" => $value->muom,
                            "buom" => $value->buom,
                            "pqtys" => ($value->qtys  - ($value->qtys % $value->uppp)) / $value->uppp,
                            "mqtys" => (($value->qtys % $value->uppp) - (($value->qtys % $value->uppp) % $value->muppp)) / $value->muppp,
                            "bqtys" => $value->qtys % $value->uppp % $value->muppp,
                            "pqtyp" => ($value->qtyp  - ($value->qtyp % $value->uppp)) / $value->uppp,
                            "mqtyp" => (($value->qtyp % $value->uppp) - (($value->qtyp % $value->uppp) % $value->muppp)) / $value->muppp,
                            "bqtyp" => $value->qtyp % $value->uppp % $value->muppp,
                            "pqtya" => ($value->qtya  - ($value->qtya % $value->uppp)) / $value->uppp,
                            "mqtya" => (($value->qtya % $value->uppp) - (($value->qtya % $value->uppp) % $value->muppp)) / $value->muppp,
                            "bqtya" => $value->qtya % $value->uppp % $value->muppp
                        ];
                    }

                    $data = [
                        "title" => "Document Reference Wise - Stock Report ( Summary )",
                        "css" => "portrait",
                        "headOne" => $headOne->toArray(),
                        "headTwo" => $headTwo->toArray(),
                        "bodyOne" => $bodyOne->toArray(),
                        "listData" => $listData,
                        "columnCount" => $columnCount
                    ];

                    return view("report", $data);
                    break;
                case "product-exp":
                    $stockList = DB::table("iv_stock_ledger as a")
                        ->select(
                            "a.product_code",
                            "b.product_name",
                            "a.mfg_date",
                            "a.exp_date",
                            "b.uppp",
                            "b.muppp",
                            "b.volume",
                            "b.gross_weight",
                            "b.puom",
                            "b.muom",
                            "b.buom",
                            DB::raw("sum(a.qtys) as qtys"),
                            DB::raw("sum(a.qtya) as qtya"),
                            DB::raw("sum(a.qtyp) as qtyp")
                        )
                        ->join("iv_product as b", "a.product_id", "b.id")
                        ->join("iv_product_group as e", "b.group_id", "e.id")
                        ->join("iv_product_brand as f", "b.brand_id", "f.id")
                        ->where("a.company_id", $company_id)
                        ->where("a.principal_id", $principal_id)
                        ->where("a.branch_id", $branch_id)
                        ->where("a.qtys", ">", 0)
                        ->whereBetween("e.group_code", [$group_from, $group_to])
                        ->whereBetween("f.brand_code", [$brand_from, $brand_to])
                        ->whereBetween("b.product_code", [$product_from, $product_to])
                        ->whereIn(DB::raw("COALESCE(a.site_id, 0)"), $site_list)
                        ->where(DB::raw("COALESCE(a.area_id, 0)"), "LIKE", $area_id)
                        ->whereBetween(DB::raw("COALESCE(a.location_code, '')"), [$location_from, $location_to])
                        ->whereBetween(DB::raw("COALESCE(a.exp_date, now())"), [$exp_date_from, $exp_date_to])
                        ->groupBy("a.product_code", "b.product_name", "a.mfg_date", "a.exp_date", "b.uppp", "b.muppp", "b.volume", "b.gross_weight", "b.puom", "b.muom", "b.buom")
                        ->orderBy("a.exp_date", $sortOrder)
                        ->orderBy("b.product_name", $sortOrder)
                        ->orderBy("a.location_code", $sortOrder)
                        ->get();

                    if ($principal->multi_level == "Yes") {
                        $headOne = collect([
                            ["name" => "SKU No.", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "SKU Name", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Date", "rowspan" => "1", "colspan" => "2"],
                            ["name" => "SOH", "rowspan" => "1", "colspan" => "3"],
                            ["name" => "SOB", "rowspan" => "1", "colspan" => "3"],
                            ["name" => "SOA", "rowspan" => "1", "colspan" => "3"],
                            ["name" => "Unit", "rowspan" => "1", "colspan" => "3"]
                        ]);

                        $headTwo = collect([
                            ["name" => "Mfg Date"],
                            ["name" => "Exp Date"],
                            ["name" => "1st"],
                            ["name" => "2nd"],
                            ["name" => "3rd"],
                            ["name" => "1st"],
                            ["name" => "2nd"],
                            ["name" => "3rd"],
                            ["name" => "1st"],
                            ["name" => "2nd"],
                            ["name" => "3rd"],
                            ["name" => "1st"],
                            ["name" => "2nd"],
                            ["name" => "3rd"]
                        ]);

                        $bodyOne = collect([
                            ["name" => "SKU No.", "field_name" => "product_code", "class" => "left"],
                            ["name" => "SKU Name", "field_name" => "product_name", "class" => "left"],
                            ["name" => "Batch", "field_name" => "mfg_date", "class" => "center"],
                            ["name" => "Batch", "field_name" => "exp_date", "class" => "center"],
                            ["name" => "1st", "field_name" => "pqtys", "class" => "right"],
                            ["name" => "2nd", "field_name" => "mqtys", "class" => "right"],
                            ["name" => "3rd", "field_name" => "bqtys", "class" => "right"],
                            ["name" => "1st", "field_name" => "pqtyp", "class" => "right"],
                            ["name" => "2nd", "field_name" => "mqtyp", "class" => "right"],
                            ["name" => "3rd", "field_name" => "bqtyp", "class" => "right"],
                            ["name" => "1st", "field_name" => "pqtya", "class" => "right"],
                            ["name" => "2nd", "field_name" => "mqtya", "class" => "right"],
                            ["name" => "3rd", "field_name" => "bqtya", "class" => "right"],
                            ["name" => "1st", "field_name" => "puom", "class" => "center"],
                            ["name" => "2nd", "field_name" => "muom", "class" => "center"],
                            ["name" => "3rd", "field_name" => "buom", "class" => "center"]
                        ]);

                        $columnCount = 16;
                    } else {
                        $headOne = collect([
                            ["name" => "SKU No.", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "SKU Name", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Date", "rowspan" => "1", "colspan" => "2"],
                            ["name" => "SOH", "rowspan" => "1", "colspan" => "1"],
                            ["name" => "SOB", "rowspan" => "1", "colspan" => "1"],
                            ["name" => "SOA", "rowspan" => "1", "colspan" => "1"],
                            ["name" => "Unit", "rowspan" => "1", "colspan" => "1"]
                        ]);

                        $headTwo = collect([
                            ["name" => "Mfg Date"],
                            ["name" => "Exp Date"],
                            ["name" => "1st"],
                            ["name" => "1st"],
                            ["name" => "1st"],
                            ["name" => "1st"],
                        ]);

                        $bodyOne = collect([
                            ["name" => "SKU No.", "field_name" => "product_code", "class" => "left"],
                            ["name" => "SKU Name", "field_name" => "product_name", "class" => "left"],
                            ["name" => "Mfg", "field_name" => "mfg_date", "class" => "center"],
                            ["name" => "Expired", "field_name" => "exp_date", "class" => "center"],
                            ["name" => "1st", "field_name" => "pqtys", "class" => "right"],
                            ["name" => "1st", "field_name" => "pqtyp", "class" => "right"],
                            ["name" => "1st", "field_name" => "pqtya", "class" => "right"],
                            ["name" => "1st", "field_name" => "puom", "class" => "center"],
                        ]);

                        $columnCount = 8;
                    }

                    $listData = [];
                    foreach ($stockList as $value) {
                        $listData[] = [
                            "product_code" => $value->product_code,
                            "product_name" => $value->product_name,
                            "puom" => $value->puom,
                            "muom" => $value->muom,
                            "buom" => $value->buom,
                            'mfg_date' => isset($value->mfg_date) ? \Carbon\Carbon::parse($value->mfg_date)->format('d-m-Y') : "",
                            'exp_date' => isset($value->exp_date) ? \Carbon\Carbon::parse($value->exp_date)->format('d-m-Y') : "",
                            "pqtys" => ($value->qtys  - ($value->qtys % $value->uppp)) / $value->uppp,
                            "mqtys" => (($value->qtys % $value->uppp) - (($value->qtys % $value->uppp) % $value->muppp)) / $value->muppp,
                            "bqtys" => $value->qtys % $value->uppp % $value->muppp,
                            "pqtyp" => ($value->qtyp  - ($value->qtyp % $value->uppp)) / $value->uppp,
                            "mqtyp" => (($value->qtyp % $value->uppp) - (($value->qtyp % $value->uppp) % $value->muppp)) / $value->muppp,
                            "bqtyp" => $value->qtyp % $value->uppp % $value->muppp,
                            "pqtya" => ($value->qtya  - ($value->qtya % $value->uppp)) / $value->uppp,
                            "mqtya" => (($value->qtya % $value->uppp) - (($value->qtya % $value->uppp) % $value->muppp)) / $value->muppp,
                            "bqtya" => $value->qtya % $value->uppp % $value->muppp
                        ];
                    }

                    $data = [
                        "title" => "Expiry Wise - Stock Report ( Summary )",
                        "css" => "portrait",
                        "headOne" => $headOne->toArray(),
                        "headTwo" => $headTwo->toArray(),
                        "bodyOne" => $bodyOne->toArray(),
                        "listData" => $listData,
                        "columnCount" => $columnCount
                    ];

                    return view("report", $data);
                    break;
            }
        } else if ($reportType == "detail") {
            switch ($GroupOn) {
                case "product":
                    $stockList = DB::table("iv_stock_ledger as a")
                        ->select(
                            "a.job_date",
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
                            "a.qtys",
                            "a.qtya",
                            "b.puom",
                            "b.muom",
                            "b.buom",
                            "a.freeze_flag",
                            "b.volume",
                            "b.gross_weight",
                            DB::raw("CASE WHEN a.status = 'B' THEN 'BAD' ELSE 'GOODS' END as status_code")
                        )
                        ->join("iv_product as b", "a.product_id", "b.id")
                        ->leftjoin("iv_site as c", "a.site_id", "c.id")
                        ->leftJoin("iv_site_area as d", "a.area_id", "d.id")
                        ->join("iv_product_group as e", "b.group_id", "e.id")
                        ->join("iv_product_brand as f", "b.brand_id", "f.id")
                        ->leftJoin("iv_location as g", "a.location_id", "g.id")
                        ->where("a.company_id", $company_id)
                        ->where("a.principal_id", $principal_id)
                        ->where("a.branch_id", $branch_id)
                        ->where("a.qtys", ">", 0)
                        ->whereBetween("e.group_code", [$group_from, $group_to])
                        ->whereBetween("f.brand_code", [$brand_from, $brand_to])
                        ->whereBetween("b.product_code", [$product_from, $product_to])
                        ->whereIn(DB::raw("COALESCE(a.site_id, 0)"), $site_list)
                        ->where(DB::raw("COALESCE(a.area_id, 0)"), "LIKE", $area_id)
                        ->whereBetween(DB::raw("COALESCE(a.location_code, '')"), [$location_from, $location_to])
                        ->whereBetween(DB::raw("COALESCE(a.exp_date, now())"), [$exp_date_from, $exp_date_to])
                        ->orderBy("b.product_name", $sortOrder)
                        ->orderBy("a.site_id", $sortOrder)
                        ->orderBy("a.location_code", $sortOrder)
                        ->get();

                    if ($principal->multi_level == "Yes") {
                        $headOne = collect([
                            ["name" => "SKU No.", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "SKU Name", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Batch No.", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Date", "rowspan" => "1", "colspan" => "3"],
                            ["name" => "Site", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Area", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Location", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "SOH", "rowspan" => "1", "colspan" => "3"],
                            ["name" => "SOA", "rowspan" => "1", "colspan" => "3"],
                            ["name" => "Unit", "rowspan" => "1", "colspan" => "3"],
                            ["name" => "Freeze", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Status", "rowspan" => "2", "colspan" => "1"],
                        ]);

                        $headTwo = collect([
                            ["name" => "Received"],
                            ["name" => "Mfg Date"],
                            ["name" => "Exp Date"],
                            ["name" => "1st"],
                            ["name" => "2nd"],
                            ["name" => "3rd"],
                            ["name" => "1st"],
                            ["name" => "2nd"],
                            ["name" => "3rd"],
                            ["name" => "1st"],
                            ["name" => "2nd"],
                            ["name" => "3rd"],
                        ]);

                        $bodyOne = collect([
                            ["name" => "SKU No.", "field_name" => "product_code", "class" => "left"],
                            ["name" => "SKU Name", "field_name" => "product_name", "class" => "left"],
                            ["name" => "Batch", "field_name" => "lot_no", "class" => "center"],
                            ["name" => "Mfg", "field_name" => "job_date", "class" => "center"],
                            ["name" => "Mfg", "field_name" => "mfg_date", "class" => "center"],
                            ["name" => "Exp", "field_name" => "exp_date", "class" => "center"],
                            ["name" => "Site", "field_name" => "site_name", "class" => "left"],
                            ["name" => "Area", "field_name" => "area_name", "class" => "left"],
                            ["name" => "Location", "field_name" => "location_code", "class" => "left"],
                            ["name" => "1st", "field_name" => "pqtys", "class" => "right"],
                            ["name" => "2nd", "field_name" => "mqtys", "class" => "right"],
                            ["name" => "3rd", "field_name" => "bqtys", "class" => "right"],
                            ["name" => "1st", "field_name" => "pqtya", "class" => "right"],
                            ["name" => "2nd", "field_name" => "mqtya", "class" => "right"],
                            ["name" => "3rd", "field_name" => "bqtya", "class" => "right"],
                            ["name" => "1st", "field_name" => "puom", "class" => "center"],
                            ["name" => "2nd", "field_name" => "muom", "class" => "center"],
                            ["name" => "3rd", "field_name" => "buom", "class" => "center"],
                            ["name" => "3rd", "field_name" => "freeze", "class" => "center"],
                            ["name" => "3rd", "field_name" => "status_code", "class" => "center"]
                        ]);

                        $columnCount = 19;
                    } else {
                        $headOne = collect([
                            ["name" => "SKU No.", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "SKU Name", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Batch No.", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Date", "rowspan" => "1", "colspan" => "3"],
                            ["name" => "Site", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Area", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Location", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "SOH", "rowspan" => "1", "colspan" => "1"],
                            ["name" => "SOA", "rowspan" => "1", "colspan" => "1"],
                            ["name" => "Unit", "rowspan" => "1", "colspan" => "1"],
                            ["name" => "Freeze", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Status", "rowspan" => "2", "colspan" => "1"],
                        ]);

                        $headTwo = collect([
                            ["name" => "Received"],
                            ["name" => "Mfg Date"],
                            ["name" => "Exp Date"],
                            ["name" => "1st"],
                            ["name" => "1st"],
                            ["name" => "1st"],
                        ]);

                        $bodyOne = collect([
                            ["name" => "SKU No.", "field_name" => "product_code", "class" => "left"],
                            ["name" => "SKU Name", "field_name" => "product_name", "class" => "left"],
                            ["name" => "Batch", "field_name" => "lot_no", "class" => "center"],
                            ["name" => "Mfg", "field_name" => "job_date", "class" => "center"],
                            ["name" => "Mfg", "field_name" => "mfg_date", "class" => "center"],
                            ["name" => "Exp", "field_name" => "exp_date", "class" => "center"],
                            ["name" => "Site", "field_name" => "site_name", "class" => "left"],
                            ["name" => "Area", "field_name" => "area_name", "class" => "left"],
                            ["name" => "Location", "field_name" => "location_code", "class" => "left"],
                            ["name" => "1st", "field_name" => "pqtys", "class" => "right"],
                            ["name" => "1st", "field_name" => "pqtya", "class" => "right"],
                            ["name" => "1st", "field_name" => "puom", "class" => "center"],
                            ["name" => "3rd", "field_name" => "freeze", "class" => "center"],
                            ["name" => "3rd", "field_name" => "status_code", "class" => "center"]
                        ]);

                        $columnCount = 13;
                    }

                    $listData = [];
                    foreach ($stockList as $value) {
                        $listData[] = [
                            "product_code" => $value->product_code,
                            "product_name" => $value->product_name,
                            "lot_no" => $value->lot_no,
                            "job_date" => \Carbon\Carbon::parse($value->job_date)->format("d-m-Y"),
                            'mfg_date' => isset($value->mfg_date) ? \Carbon\Carbon::parse($value->mfg_date)->format('d-m-Y') : "",
                            'exp_date' => isset($value->exp_date) ? \Carbon\Carbon::parse($value->exp_date)->format('d-m-Y') : "",
                            "site_name" => $value->site_name,
                            "area_name" => $value->area_name,
                            "location_code" => $value->location_code,
                            "pqtys" => ($value->qtys  - ($value->qtys % $value->uppp)) / $value->uppp,
                            "mqtys" => (($value->qtys % $value->uppp) - (($value->qtys % $value->uppp) % $value->muppp)) / $value->muppp,
                            "bqtys" => $value->qtys % $value->uppp % $value->muppp,
                            "pqtya" => ($value->qtya  - ($value->qtya % $value->uppp)) / $value->uppp,
                            "mqtya" => (($value->qtya % $value->uppp) - (($value->qtya % $value->uppp) % $value->muppp)) / $value->muppp,
                            "bqtya" => $value->qtya % $value->uppp % $value->muppp,
                            "puom" => $value->puom,
                            "muom" => $value->muom,
                            "buom" => $value->buom,
                            "freeze" => $value->freeze_flag,
                            "weight" => number_format($value->gross_weight * $value->qtys, 3, ",", "."),
                            "volume" => number_format($value->volume * $value->qtys, 3, ",", "."),
                            "status_code" => $value->status_code,
                        ];
                    }

                    $data = [
                        "title" => "Product Wise - Stock Report ( Detail )",
                        "css" => "landscape",
                        "headOne" => $headOne->toArray(),
                        "headTwo" => $headTwo->toArray(),
                        "bodyOne" => $bodyOne->toArray(),
                        "listData" => $listData,
                        "columnCount" => $columnCount
                    ];

                    return view("report", $data);
                    break;
                case "product-lot":
                    $stockList = DB::table("iv_stock_ledger as a")
                        ->select(
                            "a.job_date",
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
                            "a.qtys",
                            "a.qtya",
                            "b.puom",
                            "b.muom",
                            "b.buom"
                        )
                        ->join("iv_product as b", "a.product_id", "b.id")
                        ->leftjoin("iv_site as c", "a.site_id", "c.id")
                        ->leftJoin("iv_site_area as d", "a.area_id", "d.id")
                        ->join("iv_product_group as e", "b.group_id", "e.id")
                        ->join("iv_product_brand as f", "b.brand_id", "f.id")
                        ->where("a.company_id", $company_id)
                        ->where("a.principal_id", $principal_id)
                        ->where("a.branch_id", $branch_id)
                        ->where("a.qtys", ">", 0)
                        ->whereBetween("e.group_code", [$group_from, $group_to])
                        ->whereBetween("f.brand_code", [$brand_from, $brand_to])
                        ->whereBetween("b.product_code", [$product_from, $product_to])
                        ->whereIn(DB::raw("COALESCE(a.site_id, 0)"), $site_list)
                        ->where(DB::raw("COALESCE(a.area_id, 0)"), "LIKE", $area_id)
                        ->whereBetween(DB::raw("COALESCE(a.location_code, '')"), [$location_from, $location_to])
                        ->whereBetween(DB::raw("COALESCE(a.exp_date, now())"), [$exp_date_from, $exp_date_to])
                        ->orderBy("a.lot_no", $sortOrder)
                        ->orderBy("b.product_name", $sortOrder)
                        ->orderBy("a.location_code", $sortOrder)
                        ->get();

                    if ($principal->multi_level == "Yes") {
                        $headOne = collect([
                            ["name" => "SKU No.", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "SKU Name", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Batch No.", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Date", "rowspan" => "1", "colspan" => "3"],
                            ["name" => "Site", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Area", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Location", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "SOH", "rowspan" => "1", "colspan" => "3"],
                            ["name" => "SOA", "rowspan" => "1", "colspan" => "3"],
                            ["name" => "Unit", "rowspan" => "1", "colspan" => "3"]
                        ]);

                        $headTwo = collect([
                            ["name" => "Received"],
                            ["name" => "Mfg Date"],
                            ["name" => "Exp Date"],
                            ["name" => "1st"],
                            ["name" => "2nd"],
                            ["name" => "3rd"],
                            ["name" => "1st"],
                            ["name" => "2nd"],
                            ["name" => "3rd"],
                            ["name" => "1st"],
                            ["name" => "2nd"],
                            ["name" => "3rd"],
                        ]);

                        $bodyOne = collect([
                            ["name" => "SKU No.", "field_name" => "product_code", "class" => "left"],
                            ["name" => "SKU Name", "field_name" => "product_name", "class" => "left"],
                            ["name" => "Batch", "field_name" => "lot_no", "class" => "center"],
                            ["name" => "Mfg", "field_name" => "job_date", "class" => "center"],
                            ["name" => "Mfg", "field_name" => "mfg_date", "class" => "center"],
                            ["name" => "Exp", "field_name" => "exp_date", "class" => "center"],
                            ["name" => "Site", "field_name" => "site_name", "class" => "left"],
                            ["name" => "Area", "field_name" => "area_name", "class" => "left"],
                            ["name" => "Location", "field_name" => "location_code", "class" => "left"],
                            ["name" => "1st", "field_name" => "pqtys", "class" => "right"],
                            ["name" => "2nd", "field_name" => "mqtys", "class" => "right"],
                            ["name" => "3rd", "field_name" => "bqtys", "class" => "right"],
                            ["name" => "1st", "field_name" => "pqtya", "class" => "right"],
                            ["name" => "2nd", "field_name" => "mqtya", "class" => "right"],
                            ["name" => "3rd", "field_name" => "bqtya", "class" => "right"],
                            ["name" => "1st", "field_name" => "puom", "class" => "center"],
                            ["name" => "2nd", "field_name" => "muom", "class" => "center"],
                            ["name" => "3rd", "field_name" => "buom", "class" => "center"]
                        ]);

                        $columnCount = 18;
                    } else {
                        $headOne = collect([
                            ["name" => "SKU No.", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "SKU Name", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Batch No.", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Date", "rowspan" => "1", "colspan" => "3"],
                            ["name" => "Site", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Area", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Location", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "SOH", "rowspan" => "1", "colspan" => "1"],
                            ["name" => "SOA", "rowspan" => "1", "colspan" => "1"],
                            ["name" => "Unit", "rowspan" => "1", "colspan" => "1"]
                        ]);

                        $headTwo = collect([
                            ["name" => "Received"],
                            ["name" => "Mfg Date"],
                            ["name" => "Exp Date"],
                            ["name" => "1st"],
                            ["name" => "1st"],
                            ["name" => "1st"],
                        ]);

                        $bodyOne = collect([
                            ["name" => "SKU No.", "field_name" => "product_code", "class" => "left"],
                            ["name" => "SKU Name", "field_name" => "product_name", "class" => "left"],
                            ["name" => "Batch", "field_name" => "lot_no", "class" => "center"],
                            ["name" => "Mfg", "field_name" => "job_date", "class" => "center"],
                            ["name" => "Mfg", "field_name" => "mfg_date", "class" => "center"],
                            ["name" => "Exp", "field_name" => "exp_date", "class" => "center"],
                            ["name" => "Site", "field_name" => "site_name", "class" => "left"],
                            ["name" => "Area", "field_name" => "area_name", "class" => "left"],
                            ["name" => "Location", "field_name" => "location_code", "class" => "left"],
                            ["name" => "1st", "field_name" => "pqtys", "class" => "right"],
                            ["name" => "1st", "field_name" => "pqtya", "class" => "right"],
                            ["name" => "1st", "field_name" => "puom", "class" => "center"],
                        ]);

                        $columnCount = 12;
                    }

                    $listData = [];
                    foreach ($stockList as $value) {
                        $listData[] = [
                            "product_code" => $value->product_code,
                            "product_name" => $value->product_name,
                            "lot_no" => $value->lot_no,
                            "job_date" => \Carbon\Carbon::parse($value->job_date)->format("d-m-Y"),
                            'mfg_date' => isset($value->mfg_date) ? \Carbon\Carbon::parse($value->mfg_date)->format('d-m-Y') : "",
                            'exp_date' => isset($value->exp_date) ? \Carbon\Carbon::parse($value->exp_date)->format('d-m-Y') : "",
                            "site_name" => $value->site_name,
                            "area_name" => $value->area_name,
                            "location_code" => $value->location_code,
                            "pqtys" => ($value->qtys  - ($value->qtys % $value->uppp)) / $value->uppp,
                            "mqtys" => (($value->qtys % $value->uppp) - (($value->qtys % $value->uppp) % $value->muppp)) / $value->muppp,
                            "bqtys" => $value->qtys % $value->uppp % $value->muppp,
                            "pqtya" => ($value->qtya  - ($value->qtya % $value->uppp)) / $value->uppp,
                            "mqtya" => (($value->qtya % $value->uppp) - (($value->qtya % $value->uppp) % $value->muppp)) / $value->muppp,
                            "bqtya" => $value->qtya % $value->uppp % $value->muppp,
                            "puom" => $value->puom,
                            "muom" => $value->muom,
                            "buom" => $value->buom
                        ];
                    }

                    $data = [
                        "title" => "Batch Number Wise - Stock Report ( Detail )",
                        "css" => "landscape",
                        "headOne" => $headOne->toArray(),
                        "headTwo" => $headTwo->toArray(),
                        "bodyOne" => $bodyOne->toArray(),
                        "listData" => $listData,
                        "columnCount" => $columnCount
                    ];

                    return view("report", $data);
                    break;
                case "product-doc":
                    $stockList = DB::table("iv_stock_ledger as a")
                        ->select(
                            "a.job_date",
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
                            "a.qtys",
                            "a.qtya",
                            "b.puom",
                            "b.muom",
                            "b.buom"
                        )
                        ->join("iv_product as b", "a.product_id", "b.id")
                        ->leftjoin("iv_site as c", "a.site_id", "c.id")
                        ->leftJoin("iv_site_area as d", "a.area_id", "d.id")
                        ->join("iv_product_group as e", "b.group_id", "e.id")
                        ->join("iv_product_brand as f", "b.brand_id", "f.id")
                        ->where("a.company_id", $company_id)
                        ->where("a.principal_id", $principal_id)
                        ->where("a.branch_id", $branch_id)
                        ->where("a.qtys", ">", 0)
                        ->whereBetween("e.group_code", [$group_from, $group_to])
                        ->whereBetween("f.brand_code", [$brand_from, $brand_to])
                        ->whereBetween("b.product_code", [$product_from, $product_to])
                        ->whereIn(DB::raw("COALESCE(a.site_id, 0)"), $site_list)
                        ->where(DB::raw("COALESCE(a.area_id, 0)"), "LIKE", $area_id)
                        ->whereBetween(DB::raw("COALESCE(a.location_code, '')"), [$location_from, $location_to])
                        ->whereBetween(DB::raw("COALESCE(a.exp_date, now())"), [$exp_date_from, $exp_date_to])
                        ->orderBy("a.document_ref", $sortOrder)
                        ->orderBy("b.product_name", $sortOrder)
                        ->get();

                    if ($principal->multi_level == "Yes") {
                        $headOne = collect([
                            ["name" => "SKU No.", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "SKU Name", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Document Ref", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Date", "rowspan" => "1", "colspan" => "3"],
                            ["name" => "Site", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Area", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Location", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "SOH", "rowspan" => "1", "colspan" => "3"],
                            ["name" => "SOA", "rowspan" => "1", "colspan" => "3"],
                            ["name" => "Unit", "rowspan" => "1", "colspan" => "3"]
                        ]);

                        $headTwo = collect([
                            ["name" => "Received"],
                            ["name" => "Mfg Date"],
                            ["name" => "Exp Date"],
                            ["name" => "1st"],
                            ["name" => "2nd"],
                            ["name" => "3rd"],
                            ["name" => "1st"],
                            ["name" => "2nd"],
                            ["name" => "3rd"],
                            ["name" => "1st"],
                            ["name" => "2nd"],
                            ["name" => "3rd"],
                        ]);

                        $bodyOne = collect([
                            ["name" => "SKU No.", "field_name" => "product_code", "class" => "left"],
                            ["name" => "SKU Name", "field_name" => "product_name", "class" => "left"],
                            ["name" => "Batch", "field_name" => "lot_no", "class" => "center"],
                            ["name" => "Mfg", "field_name" => "job_date", "class" => "center"],
                            ["name" => "Mfg", "field_name" => "mfg_date", "class" => "center"],
                            ["name" => "Exp", "field_name" => "exp_date", "class" => "center"],
                            ["name" => "Site", "field_name" => "site_name", "class" => "left"],
                            ["name" => "Area", "field_name" => "area_name", "class" => "left"],
                            ["name" => "Location", "field_name" => "location_code", "class" => "left"],
                            ["name" => "1st", "field_name" => "pqtys", "class" => "right"],
                            ["name" => "2nd", "field_name" => "mqtys", "class" => "right"],
                            ["name" => "3rd", "field_name" => "bqtys", "class" => "right"],
                            ["name" => "1st", "field_name" => "pqtya", "class" => "right"],
                            ["name" => "2nd", "field_name" => "mqtya", "class" => "right"],
                            ["name" => "3rd", "field_name" => "bqtya", "class" => "right"],
                            ["name" => "1st", "field_name" => "puom", "class" => "center"],
                            ["name" => "2nd", "field_name" => "muom", "class" => "center"],
                            ["name" => "3rd", "field_name" => "buom", "class" => "center"]
                        ]);

                        $columnCount = 18;
                    } else {
                        $headOne = collect([
                            ["name" => "SKU No.", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "SKU Name", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Document Ref", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Date", "rowspan" => "1", "colspan" => "3"],
                            ["name" => "Site", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Area", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Location", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "SOH", "rowspan" => "1", "colspan" => "1"],
                            ["name" => "SOA", "rowspan" => "1", "colspan" => "1"],
                            ["name" => "Unit", "rowspan" => "1", "colspan" => "1"]
                        ]);

                        $headTwo = collect([
                            ["name" => "Received"],
                            ["name" => "Mfg Date"],
                            ["name" => "Exp Date"],
                            ["name" => "1st"],
                            ["name" => "1st"],
                            ["name" => "1st"],
                        ]);

                        $bodyOne = collect([
                            ["name" => "SKU No.", "field_name" => "product_code", "class" => "left"],
                            ["name" => "SKU Name", "field_name" => "product_name", "class" => "left"],
                            ["name" => "Document Ref", "field_name" => "document_ref", "class" => "center"],
                            ["name" => "Mfg", "field_name" => "job_date", "class" => "center"],
                            ["name" => "Mfg", "field_name" => "mfg_date", "class" => "center"],
                            ["name" => "Exp", "field_name" => "exp_date", "class" => "center"],
                            ["name" => "Site", "field_name" => "site_name", "class" => "left"],
                            ["name" => "Area", "field_name" => "area_name", "class" => "left"],
                            ["name" => "Location", "field_name" => "location_code", "class" => "left"],
                            ["name" => "1st", "field_name" => "pqtys", "class" => "right"],
                            ["name" => "1st", "field_name" => "pqtya", "class" => "right"],
                            ["name" => "1st", "field_name" => "puom", "class" => "center"],
                        ]);

                        $columnCount = 12;
                    }

                    $listData = [];
                    foreach ($stockList as $value) {
                        $listData[] = [
                            "product_code" => "SKU : " . $value->product_code,
                            "product_name" => $value->product_name,
                            "lot_no" => $value->lot_no,
                            "document_ref" => $value->document_ref,
                            "job_date" => \Carbon\Carbon::parse($value->job_date)->format("d-m-Y"),
                            'mfg_date' => isset($value->mfg_date) ? \Carbon\Carbon::parse($value->mfg_date)->format('d-m-Y') : "",
                            'exp_date' => isset($value->exp_date) ? \Carbon\Carbon::parse($value->exp_date)->format('d-m-Y') : "",
                            "site_name" => $value->site_name,
                            "area_name" => $value->area_name,
                            "location_code" => $value->location_code,
                            "pqtys" => ($value->qtys  - ($value->qtys % $value->uppp)) / $value->uppp,
                            "mqtys" => (($value->qtys % $value->uppp) - (($value->qtys % $value->uppp) % $value->muppp)) / $value->muppp,
                            "bqtys" => $value->qtys % $value->uppp % $value->muppp,
                            "pqtya" => ($value->qtya  - ($value->qtya % $value->uppp)) / $value->uppp,
                            "mqtya" => (($value->qtya % $value->uppp) - (($value->qtya % $value->uppp) % $value->muppp)) / $value->muppp,
                            "bqtya" => $value->qtya % $value->uppp % $value->muppp,
                            "puom" => $value->puom,
                            "muom" => $value->muom,
                            "buom" => $value->buom
                        ];
                    }

                    $data = [
                        "title" => "Document Reference Wise - Stock Report ( Detail )",
                        "css" => "landscape",
                        "headOne" => $headOne->toArray(),
                        "headTwo" => $headTwo->toArray(),
                        "bodyOne" => $bodyOne->toArray(),
                        "listData" => $listData,
                        "columnCount" => $columnCount
                    ];

                    return view("report", $data);
                    break;
                case "product-exp":
                    $stockList = DB::table("iv_stock_ledger as a")
                        ->select(
                            "a.job_date",
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
                            "a.qtys",
                            "a.qtya",
                            "b.puom",
                            "b.muom",
                            "b.buom"
                        )
                        ->join("iv_product as b", "a.product_id", "b.id")
                        ->leftjoin("iv_site as c", "a.site_id", "c.id")
                        ->leftJoin("iv_site_area as d", "a.area_id", "d.id")
                        ->join("iv_product_group as e", "b.group_id", "e.id")
                        ->join("iv_product_brand as f", "b.brand_id", "f.id")
                        ->where("a.company_id", $company_id)
                        ->where("a.principal_id", $principal_id)
                        ->where("a.branch_id", $branch_id)
                        ->where("a.qtys", ">", 0)
                        ->whereBetween("e.group_code", [$group_from, $group_to])
                        ->whereBetween("f.brand_code", [$brand_from, $brand_to])
                        ->whereBetween("b.product_code", [$product_from, $product_to])
                        ->whereIn(DB::raw("COALESCE(a.site_id, 0)"), $site_list)
                        ->where(DB::raw("COALESCE(a.area_id, 0)"), "LIKE", $area_id)
                        ->whereBetween(DB::raw("COALESCE(a.location_code, '')"), [$location_from, $location_to])
                        ->whereBetween(DB::raw("COALESCE(a.exp_date, now())"), [$exp_date_from, $exp_date_to])
                        ->orderBy("a.exp_date", $sortOrder)
                        ->orderBy("b.product_name", $sortOrder)
                        ->orderBy("c.site_name", $sortOrder)
                        ->orderBy("d.area_name", $sortOrder)
                        ->orderBy("a.location_code", $sortOrder)
                        ->get();

                    if ($principal->multi_level == "Yes") {
                        $headOne = collect([
                            ["name" => "SKU No.", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "SKU Name", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Date", "rowspan" => "1", "colspan" => "3"],
                            ["name" => "Batch No.", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Site", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Area", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Location", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "SOH", "rowspan" => "1", "colspan" => "3"],
                            ["name" => "SOA", "rowspan" => "1", "colspan" => "3"],
                            ["name" => "Unit", "rowspan" => "1", "colspan" => "3"]
                        ]);

                        $headTwo = collect([
                            ["name" => "Received"],
                            ["name" => "Mfg Date"],
                            ["name" => "Exp Date"],
                            ["name" => "1st"],
                            ["name" => "2nd"],
                            ["name" => "3rd"],
                            ["name" => "1st"],
                            ["name" => "2nd"],
                            ["name" => "3rd"],
                            ["name" => "1st"],
                            ["name" => "2nd"],
                            ["name" => "3rd"],
                        ]);

                        $bodyOne = collect([
                            ["name" => "SKU No.", "field_name" => "product_code", "class" => "left"],
                            ["name" => "SKU Name", "field_name" => "product_name", "class" => "left"],
                            ["name" => "Mfg", "field_name" => "job_date", "class" => "center"],
                            ["name" => "Mfg", "field_name" => "mfg_date", "class" => "center"],
                            ["name" => "Exp", "field_name" => "exp_date", "class" => "center"],
                            ["name" => "Batch", "field_name" => "lot_no", "class" => "center"],
                            ["name" => "Site", "field_name" => "site_name", "class" => "left"],
                            ["name" => "Area", "field_name" => "area_name", "class" => "left"],
                            ["name" => "Location", "field_name" => "location_code", "class" => "left"],
                            ["name" => "1st", "field_name" => "pqtys", "class" => "right"],
                            ["name" => "2nd", "field_name" => "mqtys", "class" => "right"],
                            ["name" => "3rd", "field_name" => "bqtys", "class" => "right"],
                            ["name" => "1st", "field_name" => "pqtya", "class" => "right"],
                            ["name" => "2nd", "field_name" => "mqtya", "class" => "right"],
                            ["name" => "3rd", "field_name" => "bqtya", "class" => "right"],
                            ["name" => "1st", "field_name" => "puom", "class" => "center"],
                            ["name" => "2nd", "field_name" => "muom", "class" => "center"],
                            ["name" => "3rd", "field_name" => "buom", "class" => "center"]
                        ]);

                        $columnCount = 18;
                    } else {
                        $headOne = collect([
                            ["name" => "SKU No.", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "SKU Name", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Batch No.", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Date", "rowspan" => "1", "colspan" => "3"],
                            ["name" => "Site", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Area", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Location", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "SOH", "rowspan" => "1", "colspan" => "1"],
                            ["name" => "SOA", "rowspan" => "1", "colspan" => "1"],
                            ["name" => "Unit", "rowspan" => "1", "colspan" => "1"]
                        ]);

                        $headTwo = collect([
                            ["name" => "Received"],
                            ["name" => "Mfg Date"],
                            ["name" => "Exp Date"],
                            ["name" => "1st"],
                            ["name" => "1st"],
                            ["name" => "1st"],
                        ]);

                        $bodyOne = collect([
                            ["name" => "SKU No.", "field_name" => "product_code", "class" => "left"],
                            ["name" => "SKU Name", "field_name" => "product_name", "class" => "left"],
                            ["name" => "Mfg", "field_name" => "job_date", "class" => "center"],
                            ["name" => "Mfg", "field_name" => "mfg_date", "class" => "center"],
                            ["name" => "Exp", "field_name" => "exp_date", "class" => "center"],
                            ["name" => "Batch", "field_name" => "lot_no", "class" => "center"],
                            ["name" => "Site", "field_name" => "site_name", "class" => "left"],
                            ["name" => "Area", "field_name" => "area_name", "class" => "left"],
                            ["name" => "Location", "field_name" => "location_code", "class" => "left"],
                            ["name" => "1st", "field_name" => "pqtys", "class" => "right"],
                            ["name" => "1st", "field_name" => "pqtya", "class" => "right"],
                            ["name" => "1st", "field_name" => "puom", "class" => "center"],
                        ]);

                        $columnCount = 12;
                    }

                    $listData = [];
                    foreach ($stockList as $value) {
                        $listData[] = [
                            "product_code" => $value->product_code,
                            "product_name" => $value->product_name,
                            "lot_no" => $value->lot_no,
                            "document_ref" => $value->document_ref,
                            "job_date" => \Carbon\Carbon::parse($value->job_date)->format("d-m-Y"),
                            'mfg_date' => isset($value->mfg_date) ? \Carbon\Carbon::parse($value->mfg_date)->format('d-m-Y') : "",
                            'exp_date' => isset($value->exp_date) ? \Carbon\Carbon::parse($value->exp_date)->format('d-m-Y') : "",
                            "site_name" => $value->site_name,
                            "area_name" => $value->area_name,
                            "location_code" => $value->location_code,
                            "pqtys" => ($value->qtys  - ($value->qtys % $value->uppp)) / $value->uppp,
                            "mqtys" => (($value->qtys % $value->uppp) - (($value->qtys % $value->uppp) % $value->muppp)) / $value->muppp,
                            "bqtys" => $value->qtys % $value->uppp % $value->muppp,
                            "pqtya" => ($value->qtya  - ($value->qtya % $value->uppp)) / $value->uppp,
                            "mqtya" => (($value->qtya % $value->uppp) - (($value->qtya % $value->uppp) % $value->muppp)) / $value->muppp,
                            "bqtya" => $value->qtya % $value->uppp % $value->muppp,
                            "puom" => $value->puom,
                            "muom" => $value->muom,
                            "buom" => $value->buom
                        ];
                    }

                    $data = [
                        "title" => "Expiry Date Wise - Stock Report ( Detail )",
                        "css" => "landscape",
                        "headOne" => $headOne->toArray(),
                        "headTwo" => $headTwo->toArray(),
                        "bodyOne" => $bodyOne->toArray(),
                        "listData" => $listData,
                        "columnCount" => $columnCount
                    ];

                    return view("report", $data);
                    break;
                case "site-loc":
                    $stockList = DB::table("iv_stock_ledger as a")
                        ->select(
                            "a.job_date",
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
                            "a.qtys",
                            "a.qtya",
                            "b.puom",
                            "b.muom",
                            "b.buom"
                        )
                        ->join("iv_product as b", "a.product_id", "b.id")
                        ->leftjoin("iv_site as c", "a.site_id", "c.id")
                        ->leftJoin("iv_site_area as d", "a.area_id", "d.id")
                        ->join("iv_product_group as e", "b.group_id", "e.id")
                        ->join("iv_product_brand as f", "b.brand_id", "f.id")
                        ->where("a.company_id", $company_id)
                        ->where("a.principal_id", $principal_id)
                        ->where("a.branch_id", $branch_id)
                        ->where("a.qtys", ">", 0)
                        ->whereBetween("e.group_code", [$group_from, $group_to])
                        ->whereBetween("f.brand_code", [$brand_from, $brand_to])
                        ->whereBetween("b.product_code", [$product_from, $product_to])
                        ->whereIn(DB::raw("COALESCE(a.site_id, 0)"), $site_list)
                        ->where(DB::raw("COALESCE(a.area_id, 0)"), "LIKE", $area_id)
                        ->whereBetween(DB::raw("COALESCE(a.location_code, '')"), [$location_from, $location_to])
                        ->whereBetween(DB::raw("COALESCE(a.exp_date, now())"), [$exp_date_from, $exp_date_to])
                        ->orderBy("c.site_name", $sortOrder)
                        ->orderBy("d.area_name", $sortOrder)
                        ->orderBy("a.location_code", $sortOrder)
                        ->orderBy("b.product_name", $sortOrder)
                        ->get();

                    if ($principal->multi_level == "Yes") {
                        $headOne = collect([
                            ["name" => "SKU No.", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "SKU Name", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Batch No.", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Date", "rowspan" => "1", "colspan" => "3"],
                            ["name" => "Site", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Area", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Location", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "SOH", "rowspan" => "1", "colspan" => "3"],
                            ["name" => "SOA", "rowspan" => "1", "colspan" => "3"],
                            ["name" => "Unit", "rowspan" => "1", "colspan" => "3"]
                        ]);

                        $headTwo = collect([
                            ["name" => "Received"],
                            ["name" => "Mfg Date"],
                            ["name" => "Exp Date"],
                            ["name" => "1st"],
                            ["name" => "2nd"],
                            ["name" => "3rd"],
                            ["name" => "1st"],
                            ["name" => "2nd"],
                            ["name" => "3rd"],
                            ["name" => "1st"],
                            ["name" => "2nd"],
                            ["name" => "3rd"],
                        ]);

                        $bodyOne = collect([
                            ["name" => "SKU No.", "field_name" => "product_code", "class" => "left"],
                            ["name" => "SKU Name", "field_name" => "product_name", "class" => "left"],
                            ["name" => "Batch", "field_name" => "lot_no", "class" => "center"],
                            ["name" => "Mfg", "field_name" => "job_date", "class" => "center"],
                            ["name" => "Mfg", "field_name" => "mfg_date", "class" => "center"],
                            ["name" => "Exp", "field_name" => "exp_date", "class" => "center"],
                            ["name" => "Site", "field_name" => "site_name", "class" => "left"],
                            ["name" => "Area", "field_name" => "area_name", "class" => "left"],
                            ["name" => "Location", "field_name" => "location_code", "class" => "left"],
                            ["name" => "1st", "field_name" => "pqtys", "class" => "right"],
                            ["name" => "2nd", "field_name" => "mqtys", "class" => "right"],
                            ["name" => "3rd", "field_name" => "bqtys", "class" => "right"],
                            ["name" => "1st", "field_name" => "pqtya", "class" => "right"],
                            ["name" => "2nd", "field_name" => "mqtya", "class" => "right"],
                            ["name" => "3rd", "field_name" => "bqtya", "class" => "right"],
                            ["name" => "1st", "field_name" => "puom", "class" => "center"],
                            ["name" => "2nd", "field_name" => "muom", "class" => "center"],
                            ["name" => "3rd", "field_name" => "buom", "class" => "center"]
                        ]);

                        $columnCount = 18;
                    } else {
                        $headOne = collect([
                            ["name" => "SKU No.", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "SKU Name", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Batch No.", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Date", "rowspan" => "1", "colspan" => "3"],
                            ["name" => "Site", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Area", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "Location", "rowspan" => "2", "colspan" => "1"],
                            ["name" => "SOH", "rowspan" => "1", "colspan" => "1"],
                            ["name" => "SOA", "rowspan" => "1", "colspan" => "1"],
                            ["name" => "Unit", "rowspan" => "1", "colspan" => "1"]
                        ]);

                        $headTwo = collect([
                            ["name" => "Received"],
                            ["name" => "Mfg Date"],
                            ["name" => "Exp Date"],
                            ["name" => "1st"],
                            ["name" => "1st"],
                            ["name" => "1st"],
                        ]);

                        $bodyOne = collect([
                            ["name" => "SKU No.", "field_name" => "product_code", "class" => "left"],
                            ["name" => "SKU Name", "field_name" => "product_name", "class" => "left"],
                            ["name" => "Batch", "field_name" => "lot_no", "class" => "center"],
                            ["name" => "Mfg", "field_name" => "job_date", "class" => "center"],
                            ["name" => "Mfg", "field_name" => "mfg_date", "class" => "center"],
                            ["name" => "Exp", "field_name" => "exp_date", "class" => "center"],
                            ["name" => "Site", "field_name" => "site_name", "class" => "left"],
                            ["name" => "Area", "field_name" => "area_name", "class" => "left"],
                            ["name" => "Location", "field_name" => "location_code", "class" => "left"],
                            ["name" => "1st", "field_name" => "pqtys", "class" => "right"],
                            ["name" => "1st", "field_name" => "pqtya", "class" => "right"],
                            ["name" => "1st", "field_name" => "puom", "class" => "center"],
                        ]);

                        $columnCount = 12;
                    }

                    $listData = [];
                    foreach ($stockList as $value) {
                        $listData[] = [
                            "product_code" => $value->product_code,
                            "product_name" => $value->product_name,
                            "lot_no" => $value->lot_no,
                            "document_ref" => $value->document_ref,
                            "job_date" => \Carbon\Carbon::parse($value->job_date)->format("d-m-Y"),
                            'mfg_date' => isset($value->mfg_date) ? \Carbon\Carbon::parse($value->mfg_date)->format('d-m-Y') : "",
                            'exp_date' => isset($value->exp_date) ? \Carbon\Carbon::parse($value->exp_date)->format('d-m-Y') : "",
                            "site_name" => $value->site_name,
                            "area_name" => $value->area_name,
                            "location_code" => $value->location_code,
                            "pqtys" => ($value->qtys  - ($value->qtys % $value->uppp)) / $value->uppp,
                            "mqtys" => (($value->qtys % $value->uppp) - (($value->qtys % $value->uppp) % $value->muppp)) / $value->muppp,
                            "bqtys" => $value->qtys % $value->uppp % $value->muppp,
                            "pqtya" => ($value->qtya  - ($value->qtya % $value->uppp)) / $value->uppp,
                            "mqtya" => (($value->qtya % $value->uppp) - (($value->qtya % $value->uppp) % $value->muppp)) / $value->muppp,
                            "bqtya" => $value->qtya % $value->uppp % $value->muppp,
                            "puom" => $value->puom,
                            "muom" => $value->muom,
                            "buom" => $value->buom
                        ];
                    }

                    $data = [
                        "title" => "Site & Location Wise - Stock Report ( Detail )",
                        "css" => "landscape",
                        "headOne" => $headOne->toArray(),
                        "headTwo" => $headTwo->toArray(),
                        "bodyOne" => $bodyOne->toArray(),
                        "listData" => $listData,
                        "columnCount" => $columnCount
                    ];

                    return view("report", $data);
                    break;
            }
        }
    }

    public function export(Request $request)
    {
        $user = Auth::user();
        $principal_id = $request->principal_id;
        $branch_id = $request->branch_id;
        $reportType = $request->reportType;

        $time = \Carbon\Carbon::now()->format("dmy.His");

        $principal = \App\Models\Master\Principal::find($principal_id);

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

        if (is_numeric($request->product_from)) {
            $product_from = (int)$product_from;
        } else {
            $product_from = $product_from;
        }
        if (is_numeric($request->product_to)) {
            $product_to = (int)$product_to;
        } else {
            $product_to = $product_to;
        }

        $area_id = "%";

        $site_list = [];
        if (!empty($request->site_id) && isset($request->site_id)) {
            $site_list[] = $request->site_id;
        } else {
            foreach ($user->site->all() as $value) {
                $site_list[] = $value->id;
            }
        }

        if (!empty($request->area_id) && isset($request->area_id)) {
            $area_id = $request->area_id;
        }

        if (!empty($request->location_from) && !empty($request->location_to)) {
            $location_from = $request->location_from;
            $location_to = $request->location_to;
        } else {
            if (!empty($request->location_from) && empty($request->location_to)) {
                $location_from = $request->location_from;
                $location_to = "zzzzzzzzzzzzzzzzzzzzzzzzzzzzzz";
            } else if (empty($request->brand_code_from) && !empty($request->location_to)) {
                $location_from = "";
                $location_to = $request->location_to;
            } else {
                $location_from = "";
                $location_to = "zzzzzzzzzzzzzzzzzzzzzzzzzzzzzz";
            }
        }

        $exp_date_from = "1990-01-01";
        $exp_date_to = "2999-12-31";
        if (!empty($request->exp_date_from) && !empty($request->exp_date_to)) {
            $exp_date_from = $request->exp_date_from;
            $exp_date_to = $request->exp_date_to;
        } else if (!empty($request->exp_date_from) && empty($request->exp_date_to)) {
            $exp_date_from = $request->exp_date_from;
            $exp_date_to = "2999-12-31";
        }

        $exp_date_from = date("Y-m-d", strtotime($exp_date_from));
        $exp_date_to = date("Y-m-d", strtotime($exp_date_to));

        $filename = "$principal->short_name-$reportType-$time.xlsx";


        return Excel::download(new StockLedgerReportExport($reportType, $branch_id, $principal_id, $group_from, $group_to, $brand_from, $brand_to, $product_from, $product_to, $exp_date_from, $exp_date_to, $site_list, $area_id, $location_from, $location_to), $filename);
    }
}
