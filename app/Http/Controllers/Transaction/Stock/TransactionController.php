<?php

namespace App\Http\Controllers\Transaction\Stock;

use App\Exports\TransactionReportExport;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class TransactionController extends Controller
{
    public function index()
    {

        return view('report.transaction-report.index');
    }

    public function report(Request $request)
    {
        $user = Auth::user();
        $company_id = Auth::user()->company_id;
        $branch_id = $request->branch_id;
        $principal_id = $request->principal_id;
        $GroupOn = $request->GroupOn;
        $jobType = $request->jobType;
        $principal = \App\Models\Master\Principal::find($request->principal_id);

        switch ($jobType) {
            case 'All':
                $job_type = [
                    "IMP",
                    "EXP",
                    "TFRI",
                    "TFRO",
                    "ADJ+",
                    "ADJ-"
                ];
                break;
            case 'Inbound':
                $job_type = [
                    "IMP"
                ];
                break;
            case 'Outbound':
                $job_type = [
                    "EXP"
                ];
                break;
            case 'Moves':
                $job_type = [
                    "TFRI",
                    "TFRO",
                ];
                break;
            case 'Adjustments':
                $job_type = [
                    "ADJ+",
                    "ADJ-"
                ];
                break;

            default:
                # code...
                break;
        }

        if (!empty($request->product_code_from) && !empty($request->product_code_to)) {
            $product_from = $request->product_code_from;
            $product_to = $request->product_code_to;
        } else {
            if (!empty($request->product_code_from) && empty($request->product_code_to)) {
                $product_from = $request->product_code_from;
                $product_to = "zzzzzzzzzzzzzzzzzzzzzzzzzzzzzz";
            } else if (empty($request->product_code_from) && !empty($request->product_code_to)) {
                $product_from = "";
                $product_to = $request->product_code_to;
            } else {
                $product_from = "";
                $product_to = "zzzzzzzzzzzzzzzzzzzzzzzzzzzzzz";
            }
        }

        if (!empty($request->batch_from) && !empty($request->batch_to)) {
            $batch_from = $request->batch_from;
            $batch_to = $request->batch_to;
        } else {
            if (!empty($request->batch_from) && empty($request->batch_to)) {
                $batch_from = $request->batch_from;
                $batch_to = "zzzzzzzzzzzzzzzzzzzzzzzzzzzzzz";
            } else if (empty($request->batch_from) && !empty($request->batch_to)) {
                $batch_from = "";
                $batch_to = $request->batch_to;
            } else {
                $batch_from = "";
                $batch_to = "zzzzzzzzzzzzzzzzzzzzzzzzzzzzzz";
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

        $date_from = '1990-01-01';
        $date_to = '2999-12-31';
        if (!empty($request->date_from) && !empty($request->date_to)) {
            $date_from = $request->date_from;
            $date_to = $request->date_to;
        } else if (!empty($request->date_from) && empty($request->date_to)) {
            $date_from = $request->date_from;
            $date_to = '2999-12-31';
        }

        $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $date_from);
        $date_from = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

        $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $date_to);
        $date_to = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');


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

        switch ($GroupOn) {
            case 'product':
                $stockBefore = DB::table('iv_stock_transaction as a')
                    ->select(
                        'a.product_id',
                        DB::raw("SUM(CASE WHEN a.job_type IN ('IMP','TFRI','ADJ+') THEN a.qty ELSE 0 END) as qty_received"),
                        DB::raw("SUM(CASE WHEN a.job_type IN ('EXP','TFRO','ADJ-') THEN a.qty ELSE 0 END) as qty_issue")
                    )
                    ->join('iv_product as b', 'a.product_id', 'b.id')
                    ->where('a.company_id', $company_id)
                    ->where('a.principal_id', $principal_id)
                    ->where('a.branch_id', $branch_id)
                    ->whereDate('a.job_date', '<=', Carbon::parse($date_from)->subDay()->toDateString())
                    ->whereBetween('b.product_code', [$product_from, $product_to])
                    ->whereIn(DB::raw("COALESCE(a.site_id, 0)"), $site_list)
                    ->where(DB::raw("COALESCE(a.area_id, 0)"), "LIKE", $area_id)
                    ->whereBetween(DB::raw("COALESCE(a.location_code, '')"), [$location_from, $location_to])
                    ->groupBy('a.product_id')
                    ->get();


                $stockList = DB::table('iv_stock_transaction as a')
                    ->select(
                        'a.job_no',
                        'a.job_date',
                        'a.job_type',
                        'a.product_id',
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
                        'a.qty',
                        'b.puom',
                        'b.muom',
                        'b.buom',
                        'a.pqty',
                        'a.mqty',
                        'a.bqty',
                        'a.qty',
                        'a.reference_no',
                        'b.volume',
                        'b.gross_weight'
                    )
                    ->join('iv_product as b', 'a.product_id', 'b.id')
                    ->leftjoin('iv_site as c', 'a.site_id', 'c.id')
                    ->leftjoin('iv_site_area as d', 'a.area_id', 'd.id')
                    ->where('a.company_id', $company_id)
                    ->where('a.principal_id', $principal_id)
                    ->where('a.branch_id', $branch_id)
                    ->whereBetween('b.product_code', [$product_from, $product_to])
                    ->whereIn(DB::raw("COALESCE(a.site_id, 0)"), $site_list)
                    ->where(DB::raw("COALESCE(a.area_id, 0)"), "LIKE", $area_id)
                    ->whereBetween(DB::raw("COALESCE(a.location_code, '')"), [$location_from, $location_to])
                    ->whereBetween(DB::raw("COALESCE(a.lot_no, '')"), [$batch_from, $batch_to])
                    ->whereDate('a.job_date', '>=', Carbon::parse($date_from)->toDateString())
                    ->whereDate('a.job_date', '<=', Carbon::parse($date_to)->toDateString())
                    ->whereIn('a.job_type', $job_type)
                    ->orderBy('b.product_code', 'asc')
                    ->orderBy('b.product_name', 'asc')
                    ->orderBy('a.job_date', 'asc')
                    // ->orderBy(DB::raw("CASE WHEN a.job_type = 'IMP' THEN 1 WHEN a.job_type = 'TFRI' THEN 2 WHEN a.job_type = 'ADJ+' THEN 3 WHEN a.job_type = 'EXP' THEN 4 WHEN a.job_type = 'TFRO' THEN 5 ELSE 6 END"), 'asc')
                    ->get();

                $data = [
                    "title" => "Product Wise - Stock Transaction Report",
                    "css" => "landscape",
                    "stock_before" => $stockBefore,
                    "stock_list" => $stockList,
                    "date_from" => $date_from,
                    "date_to" => $date_to,
                    "principal" => $principal,
                    "columnCount" => 21
                ];

                return view('report.transaction-report.product', $data);
                break;

            case 'product-lot':
                $stockBefore = DB::table('iv_stock_transaction as a')
                    ->select(
                        'a.product_id',
                        'a.lot_no',
                        DB::raw("sum(CASE WHEN a.job_type IN ('IMP', 'TFRI', 'ADJ+') THEN a.qty ELSE 0 END ) as qty_received"),
                        DB::raw("sum(CASE WHEN a.job_type IN ('EXP', 'TFRO', 'ADJ-') THEN a.qty ELSE 0 END ) as qty_issue")
                    )
                    ->join('iv_product as b', 'a.product_id', 'b.id')
                    ->where('a.company_id', $company_id)
                    ->where('a.principal_id', $principal_id)
                    ->where('a.branch_id', $branch_id)
                    ->where('a.job_date', '<', date($date_from))
                    ->whereBetween('b.product_code', [$product_from, $product_to])
                    ->whereIn(DB::raw("COALESCE(a.site_id, 0)"), $site_list)
                    ->where(DB::raw("COALESCE(a.area_id, 0)"), "LIKE", $area_id)
                    ->whereBetween(DB::raw("COALESCE(a.location_code, '')"), [$location_from, $location_to])
                    ->groupBy('a.product_id', 'a.lot_no')
                    ->get();

                $stockList = DB::table('iv_stock_transaction as a')
                    ->select(
                        'a.job_no',
                        'a.job_date',
                        'a.job_type',
                        'a.product_id',
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
                        'a.qty',
                        'b.puom',
                        'b.muom',
                        'b.buom',
                        'a.pqty',
                        'a.mqty',
                        'a.bqty',
                        'a.qty',
                        'b.volume',
                        'b.gross_weight'
                    )
                    ->join('iv_product as b', 'a.product_id', 'b.id')
                    ->join('iv_site as c', 'a.site_id', 'c.id')
                    ->join('iv_site_area as d', 'a.area_id', 'd.id')
                    ->where('a.company_id', $company_id)
                    ->where('a.principal_id', $principal_id)
                    ->where('a.branch_id', $branch_id)
                    ->whereBetween('b.product_code', [$product_from, $product_to])
                    ->whereIn(DB::raw("COALESCE(a.site_id, 0)"), $site_list)
                    ->where(DB::raw("COALESCE(a.area_id, 0)"), "LIKE", $area_id)
                    ->whereBetween(DB::raw("COALESCE(a.location_code, '')"), [$location_from, $location_to])
                    ->whereBetween(DB::raw("COALESCE(a.lot_no, '')"), [$batch_from, $batch_to])
                    ->whereBetween('a.job_date', [date($date_from), date($date_to)])
                    ->whereIn('a.job_type', $job_type)
                    ->orderBy('b.product_name', 'asc')
                    ->orderBy('a.lot_no', 'asc')
                    ->orderBy('a.job_date', 'asc')
                    ->orderBy(DB::raw("CASE WHEN a.job_type = 'IMP' THEN 1 WHEN a.job_type = 'TFRI' THEN 2 WHEN a.job_type = 'ADJ+' THEN 3 WHEN a.job_type = 'EXP' THEN 4 WHEN a.job_type = 'TFRO' THEN 5 ELSE 6 END"), 'asc')
                    ->get();

                $data = [
                    "title" => "Batch Number Wise - Stock Transaction Report",
                    "css" => "landscape",
                    "stock_before" => $stockBefore,
                    "stock_list" => $stockList,
                    "date_from" => $date_from,
                    "date_to" => $date_to,
                    "principal" => $principal,
                    "columnCount" => 21
                ];

                return view('report.transaction-report.batch', $data);
                break;

            case 'product-doc':
                $stockBefore = DB::table('iv_stock_transaction as a')
                    ->select(
                        'a.product_id',
                        'a.document_ref',
                        DB::raw("sum(CASE WHEN a.job_type IN ('IMP', 'TFRI', 'ADJ+') THEN a.qty ELSE 0 END ) as qty_received"),
                        DB::raw("sum(CASE WHEN a.job_type IN ('EXP', 'TFRO', 'ADJ-') THEN a.qty ELSE 0 END ) as qty_issue")
                    )
                    ->join('iv_product as b', 'a.product_id', 'b.id')
                    ->where('a.company_id', $company_id)
                    ->where('a.principal_id', $principal_id)
                    ->where('a.branch_id', $branch_id)
                    ->where('a.job_date', '<', date($date_from))
                    ->whereBetween('b.product_code', [$product_from, $product_to])
                    ->whereIn(DB::raw("COALESCE(a.site_id, 0)"), $site_list)
                    ->where(DB::raw("COALESCE(a.area_id, 0)"), "LIKE", $area_id)
                    ->whereBetween(DB::raw("COALESCE(a.location_code, '')"), [$location_from, $location_to])
                    ->groupBy('a.product_id', 'a.document_ref')
                    ->get();

                $stockList = DB::table('iv_stock_transaction as a')
                    ->select(
                        'a.job_no',
                        'a.job_date',
                        'a.job_type',
                        'a.product_id',
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
                        'a.qty',
                        'b.puom',
                        'b.muom',
                        'b.buom',
                        'a.pqty',
                        'a.mqty',
                        'a.bqty',
                        'a.qty',
                        'b.volume',
                        'b.gross_weight'
                    )
                    ->join('iv_product as b', 'a.product_id', 'b.id')
                    ->join('iv_site as c', 'a.site_id', 'c.id')
                    ->join('iv_site_area as d', 'a.area_id', 'd.id')
                    ->where('a.company_id', $company_id)
                    ->where('a.principal_id', $principal_id)
                    ->where('a.branch_id', $branch_id)
                    ->whereBetween('b.product_code', [$product_from, $product_to])
                    ->whereIn(DB::raw("COALESCE(a.site_id, 0)"), $site_list)
                    ->where(DB::raw("COALESCE(a.area_id, 0)"), "LIKE", $area_id)
                    ->whereBetween(DB::raw("COALESCE(a.location_code, '')"), [$location_from, $location_to])
                    ->whereBetween(DB::raw("COALESCE(a.lot_no, '')"), [$batch_from, $batch_to])
                    ->whereBetween('a.job_date', [date($date_from), date($date_to)])
                    ->whereIn('a.job_type', $job_type)
                    ->orderBy('b.product_name', 'asc')
                    ->orderBy('a.document_ref', 'asc')
                    ->orderBy('a.job_date', 'asc')
                    ->orderBy(DB::raw("CASE WHEN a.job_type = 'IMP' THEN 1 WHEN a.job_type = 'TFRI' THEN 2 WHEN a.job_type = 'ADJ+' THEN 3 WHEN a.job_type = 'EXP' THEN 4 WHEN a.job_type = 'TFRO' THEN 5 ELSE 6 END"), 'asc')
                    ->get();

                $data = [
                    "title" => "Batch Number Wise - Stock Transaction Report",
                    "css" => "landscape",
                    "stock_before" => $stockBefore,
                    "stock_list" => $stockList,
                    "date_from" => $date_from,
                    "date_to" => $date_to,
                    "principal" => $principal,
                    "columnCount" => 21
                ];

                return view('report.transaction-report.document', $data);
                break;

            case 'product-site':
                $stockBefore = DB::table('iv_stock_transaction as a')
                    ->select(
                        'a.product_id',
                        'a.site_id',
                        DB::raw("sum(CASE WHEN a.job_type IN ('IMP', 'TFRI', 'ADJ+') THEN a.qty ELSE 0 END ) as qty_received"),
                        DB::raw("sum(CASE WHEN a.job_type IN ('EXP', 'TFRO', 'ADJ-') THEN a.qty ELSE 0 END ) as qty_issue")
                    )
                    ->join('iv_product as b', 'a.product_id', 'b.id')
                    ->where('a.company_id', $company_id)
                    ->where('a.principal_id', $principal_id)
                    ->where('a.branch_id', $branch_id)
                    ->where('a.job_date', '<', date($date_from))
                    ->whereBetween('b.product_code', [$product_from, $product_to])
                    ->whereIn(DB::raw("COALESCE(a.site_id, 0)"), $site_list)
                    ->where(DB::raw("COALESCE(a.area_id, 0)"), "LIKE", $area_id)
                    ->whereBetween(DB::raw("COALESCE(a.location_code, '')"), [$location_from, $location_to])
                    ->groupBy('a.product_id', 'a.site_id')
                    ->get();

                $stockList = DB::table('iv_stock_transaction as a')
                    ->select(
                        'a.job_no',
                        'a.job_date',
                        'a.job_type',
                        'a.product_id',
                        'a.product_code',
                        'b.product_name',
                        'a.lot_no',
                        'a.document_ref',
                        'a.mfg_date',
                        'a.exp_date',
                        'a.site_id',
                        'c.site_name',
                        'd.area_name',
                        'a.location_code',
                        'b.uppp',
                        'b.muppp',
                        'a.qty',
                        'b.puom',
                        'b.muom',
                        'b.buom',
                        'a.pqty',
                        'a.mqty',
                        'a.bqty',
                        'a.qty',
                        'b.volume',
                        'b.gross_weight'
                    )
                    ->join('iv_product as b', 'a.product_id', 'b.id')
                    ->join('iv_site as c', 'a.site_id', 'c.id')
                    ->join('iv_site_area as d', 'a.area_id', 'd.id')
                    ->where('a.company_id', $company_id)
                    ->where('a.principal_id', $principal_id)
                    ->where('a.branch_id', $branch_id)
                    ->whereBetween('b.product_code', [$product_from, $product_to])
                    ->whereIn(DB::raw("COALESCE(a.site_id, 0)"), $site_list)
                    ->where(DB::raw("COALESCE(a.area_id, 0)"), "LIKE", $area_id)
                    ->whereBetween(DB::raw("COALESCE(a.location_code, '')"), [$location_from, $location_to])
                    ->whereBetween(DB::raw("COALESCE(a.lot_no, '')"), [$batch_from, $batch_to])
                    ->whereBetween('a.job_date', [date($date_from), date($date_to)])
                    ->whereIn('a.job_type', $job_type)
                    ->orderBy('b.product_name', 'asc')
                    ->orderBy('a.site_id', 'asc')
                    ->orderBy('a.area_id', 'asc')
                    ->orderBy('a.location_code', 'asc')
                    ->orderBy('a.job_date', 'asc')
                    ->orderBy(DB::raw("CASE WHEN a.job_type = 'IMP' THEN 1 WHEN a.job_type = 'TFRI' THEN 2 WHEN a.job_type = 'ADJ+' THEN 3 WHEN a.job_type = 'EXP' THEN 4 WHEN a.job_type = 'TFRO' THEN 5 ELSE 6 END"), 'asc')
                    ->get();

                $data = [
                    "title" => "Site Wise - Stock Transaction Report",
                    "css" => "landscape",
                    "stock_before" => $stockBefore,
                    "stock_list" => $stockList,
                    "date_from" => $date_from,
                    "date_to" => $date_to,
                    "principal" => $principal,
                    "columnCount" => 21
                ];

                return view('report.transaction-report.site', $data);
                break;
        }
    }

    public function export(Request $request)
    {
        $user = Auth::user();
        $branch_id = $request->branch_id;
        $principal_id = $request->principal_id;
        $GroupOn = $request->GroupOn;
        $jobType = $request->jobType;
        $time = \Carbon\Carbon::now()->format("dmy.His");
        $principal = \App\Models\Master\Principal::find($request->principal_id);

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

        $date_from = '1990-01-01';
        $date_to = '2999-12-31';
        if (!empty($request->date_from) && !empty($request->date_to)) {
            $date_from = $request->date_from;
            $date_to = $request->date_to;
        } else if (!empty($request->date_from) && empty($request->date_to)) {
            $date_from = $request->date_from;
            $date_to = '2999-12-31';
        }

        $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $date_from);
        $date_from = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

        $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $date_to);
        $date_to = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

        $filename = "$principal->short_name-$GroupOn-$time.xlsx";

        return Excel::download(new TransactionReportExport($GroupOn, $jobType, $branch_id, $principal_id, $product_from, $product_to, $date_from, $date_to, $site_list, $area_id, $location_from, $location_to), $filename);
    }
}
